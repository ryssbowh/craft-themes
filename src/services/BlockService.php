<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\BlockEvent;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\Layout;
use Ryssbowh\CraftThemes\records\BlockRecord;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class BlockService extends Service
{
	const EVENT_BEFORE_SAVE = 1;
	const EVENT_AFTER_SAVE = 2;
	const EVENT_BEFORE_APPLY_DELETE = 3;
	const EVENT_AFTER_DELETE = 4;
    const EVENT_BEFORE_DELETE = 5;
    const CONFIG_KEY = 'themes.blocks';

    /**
     * @var array
     */
    protected $blocks;

    /**
     * Get all blocks
     * 
     * @return array
     */
    public function getAll(): array
    {
        if (is_null($this->blocks)) {
            $blocks = [];
            foreach (BlockRecord::find()->all() as $record) {
                try {
                    $blocks[$record->id] = $record->toModel();
                } catch (BlockProviderException $e) {}
            }
            $this->blocks = $blocks;
        }
        return $this->blocks;
    }
    
    /**
     * Get block by id
     * 
     * @param  int    $id
     * @return ?BlockInterface
     */
	public function getById(int $id): ?BlockInterface
	{
		foreach ($this->getAll() as $block) {
            if ($block->id == $id) {
                return $block;
            }
        }
        return null;
	}

    /**
     * Get blocks for a layout
     * 
     * @param  int    $layout
     * @return array
     */
	public function forLayout(int $layout): array
	{
        return array_filter($this->getAll(), function ($block) use ($layout) {
            return $layout == $block->layout;
        });
    }

    /**
     * Rebuild block config
     * 
     * @param  RebuildConfigEvent $e
     */
	public function rebuildBlocksConfig(RebuildConfigEvent $e)
    {
    	$parts = explode('.', self::CONFIG_KEY);
        foreach ($this->getAllBlockRecords() as $block) {
            $e->config[$parts[0]][$parts[1]][$block->uid] = $block->getConfig();
        }
    }

    /**
     * Get block record by uid or a new one if not found
     * 
     * @param  string $uid
     * @return BlockRecord
     */
	public function getRecordByUid(string $uid): BlockRecord
	{
		return BlockRecord::findOne(['uid' => $uid]) ?? new BlockRecord;
	}

    /**
     * Save blocks
     * 
     * @param  array   $blocks
     * @param  Layout  $layout
     * @param  bool    $validate
     * @return bool
     */
    public function saveBlocks(array $blocks, Layout $layout, bool $validate = true): bool
    {   
        $ids = [];
        $hasErrors = false;
        foreach ($blocks as $block) {
            $block->layout = $layout->id;
            if ($validate) {
                if ($block->validate()) {
                    $ids[] = $this->saveBlock($block)->id;
                } else {
                    $hasErrors = true;
                }
            } else {
                $ids[] = $this->saveBlock($block)->id;
            }
        }
        if (!$hasErrors) {
            $toDelete = BlockRecord::find()
                ->where(['layout' => $layout->id])
                ->andWhere(['not in', 'id', $ids])
                ->all();
            foreach ($toDelete as $block) {
                $this->deleteBlock($block);
            }
        }
        return !$hasErrors;
    }

    /**
     * Saves one block
     * 
     * @param  Block  $block
     * @return Block
     */
	public function saveBlock(Block $block): Block
    {
        $isNew = !is_int($block->id);
        $uid = $isNew ? StringHelper::UUID() : $block->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE, new BlockEvent([
            'block' => $block
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $block->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $record->save(false);
        
        if ($isNew) {
            $block->setAttributes($record->getAttributes(), false);
        }

        return $block;
    }

    /**
     * Deletes one block
     * 
     * @param  BlockRecord $record
     * @return bool
     */
    public function deleteBlock(BlockRecord $record): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE, new BlockEvent([
            'block' => $record
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $record->uid);
        return true;
    }

    /**
     * Handles block config change
     * 
     * @param  ConfigEvent $event
     */
    public function handleChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();

        try {
            $block = $this->getRecordByUid($uid);
            $isNew = $block->getIsNewRecord();

            $block->uid = $uid;
            $block->region = $data['region'];
            $block->layout = $this->layoutService()->getRecordByUid($data['layout'])->id;
            $block->handle = $data['handle'];
            $block->provider = $data['provider'];
            $block->order = $data['order'];
            $block->active = $data['active'];
            $block->options = $data['options'] ?? [];
            
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
     * Hanles block config deletion
     * 
     * @param  ConfigEvent $event
     */
    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $block = $this->getRecordByUid($uid);

        if (!$block) {
            return;
        }

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
     * Create or fetch block from raw data
     * 
     * @param  array  $data
     * @return Block
     * @throws BlockException
     */
    public function fromData(array $data): Block
    {
        if (!isset($data['handle'])) {
            throw BlockException::noHandleInData(__METHOD__);
        }
        $handle = $data['handle'];
        unset($data['handle']);
        if ($data['id'] ?? false) {
            $block = $this->getById($data['id']);
            $block->setAttributes($data);
            return $block;
        }
        if (!isset($data['provider'])) {
            throw BlockException::noProviderInData(__METHOD__);
        }
        return $this->providerService()->getByHandle($data['provider'])->getBlock($handle, $data); 
    }
}