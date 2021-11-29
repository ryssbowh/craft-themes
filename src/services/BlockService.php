<?php
namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\BlockEvent;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use craft\db\ActiveRecord;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class BlockService extends Service
{
    const EVENT_BEFORE_SAVE = 'before_save';
    const EVENT_AFTER_SAVE = 'after_save';
    const EVENT_BEFORE_APPLY_DELETE = 'before_apply_delete';
    const EVENT_AFTER_DELETE = 'after_delete';
    const EVENT_BEFORE_DELETE = 'before_delete';
    const CONFIG_KEY = 'themes.blocks';

    /**
     * @var Collection
     */
    protected $_blocks;

    /**
     * Get all blocks
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        if (is_null($this->_blocks)) {
            $this->_blocks = collect();
            foreach (BlockRecord::find()->orderBy(['order' => SORT_ASC])->all() as $record) {
                //Catching a provider exception, in case the block can't be created.
                //Reason is whether the block provider or the block handle isn't defined
                try {
                    $this->_blocks->push($this->create($record));
                } catch (BlockProviderException $e) {
                    \Craft::error('themes', 'Couldn\'t create block record ' . $record->id . ', it has been skipped');
                }
            }
        }
        return $this->_blocks;
    }

    /**
     * Creates a block from config
     * 
     * @param  array|ActiveRecord $config
     * @return BlockInterface
     * @throws BlockException
     */
    public function create($config): BlockInterface
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        if (!isset($config['provider'])) {
            throw BlockException::parameterMissing('provider', __METHOD__);
        }
        if (!isset($config['handle'])) {
            throw BlockException::parameterMissing('handle', __METHOD__);
        }
        $handle = $config['handle'];
        unset($config['handle']);
        $block = $this->blockProviderService()->getByHandle($config['provider'])->createBlock($handle);
        $attributes = $block->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $block->setAttributes($config);
        return $block;
    }

    /**
     * Saves a block
     * 
     * @param  BlockInterface $block
     * @param  bool           $validate
     * @return bool
     */
    public function save(BlockInterface $block, bool $validate = true): bool
    {
        if ($validate and !$block->validate()) {
            return false;
        }

        $isNew = !is_int($block->id);

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new BlockEvent([
            'block' => $block,
            'isNew' => $isNew
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $block->getConfig();
        $uid = $block->uid ?? StringHelper::UUID();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $attributes = $record->getAttributes();
        unset($attributes['handle']);
        unset($attributes['options']);
        $block->setAttributes($attributes);
        $block->afterSave();
        
        if ($isNew) {
            //Sorting internal caches
            $this->add($block);
            $block->layout->getRegion($block->region)->blocks = null;
        }

        return true;
    }

    /**
     * Deletes a block
     * 
     * @param  BlockInterface $block
     * @return bool
     */
    public function delete(BlockInterface $block): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new BlockEvent([
            'block' => $block
        ]));

        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $block->uid);

        $this->_blocks = $this->all()->where('id', '!=', $block->id);
        $block->layout->getRegion($block->region)->blocks = null;

        return true;
    }

    /**
     * Handles a change in block config
     * 
     * @param ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        ProjectConfigHelper::ensureAllLayoutsProcessed();
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $block = $this->getRecordByUid($uid);
            $isNew = $block->getIsNewRecord();

            $block->provider = $data['provider'];
            $block->region = $data['region'];
            $block->handle = $data['handle'];
            $block->order = $data['order'];
            $block->active = $data['active'];
            $block->options = $data['options'] ?? [];
            $block->cacheStrategy = $data['cacheStrategy'] ?? [];
            $block->layout_id = Themes::$plugin->layouts->getRecordByUid($data['layout_id'])->id;
            $block->save(false);
            
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE, new BlockEvent([
            'block' => $block,
            'isNew' => $isNew,
        ]));
    }

    /**
     * Handles a deletion in block config
     * 
     * @param ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $block = $this->getRecordByUid($uid);

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE, new BlockEvent([
            'block' => $block
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(BlockRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE, new BlockEvent([
            'block' => $block
        ]));
    }

    /**
     * Respond to rebuild config event
     * 
     * @param RebuildConfigEvent $e
     */
    public function rebuildConfig(RebuildConfigEvent $e)
    {
        $parts = explode('.', self::CONFIG_KEY);
        foreach ($this->all() as $block) {
            $e->config[$parts[0]][$parts[1]][$block->uid] = $block->getConfig();
        }
    }

    /**
     * Clean up for layout, deletes old blocks
     *
     * @param array $blocks
     * @param LayoutInterface $layout
     */
    public function cleanUp(array $blocks, LayoutInterface $layout)
    {
        $toKeep = array_map(function ($block) {
            return $block->id;
        }, $blocks);
        $toDelete = $this->all()
            ->whereNotIn('id', $toKeep)
            ->where('layout_id', $layout->id)
            ->all();
        foreach ($toDelete as $block) {
            $this->delete($block);
        }
    }
    
    /**
     * Get a block by id
     * 
     * @param  int    $id
     * @return ?BlockInterface
     */
    public function getById(int $id): ?BlockInterface
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Get blocks for a region
     * 
     * @param  Region $region
     * @return array
     */
    public function getForRegion(Region $region): array
    {
        return $this->all()
            ->where('layout_id', $region->layout->id)
            ->where('region', $region->handle)
            ->values()
            ->all();
    }

    /**
     * Get block record by uid or create a new one if not found
     * 
     * @param  string $uid
     * @return BlockRecord
     */
    public function getRecordByUid(string $uid): BlockRecord
    {
        return BlockRecord::findOne(['uid' => $uid]) ?? new BlockRecord(['uid' => $uid]);
    }

    /**
     * Validates an array of blocks
     * 
     * @param  array  $blocks
     * @return bool
     */
    public function validateAll(array $blocks): bool
    {
        $res = true;
        foreach ($blocks as $block) {
            if (!$block->validate()) {
                $res = false;
            }
        }
        return $res;
    }

    /**
     * Add a block to internal cache
     * 
     * @param BlockInterface $layout
     */
    protected function add(BlockInterface $block)
    {
        if (!$this->all()->firstWhere('id', $block->id)) {
            $this->all()->push($block);
        }
    }
}