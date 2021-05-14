<?php 

namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\events\BlockEvent;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\exceptions\BlockProviderException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\BlockRecord;
use Ryssbowh\CraftThemes\records\LayoutRecord;
use craft\db\ActiveRecord;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class BlockService extends Service
{
    /**
     * @var array
     */
    protected $_blocks;

    /**
     * Get all blocks
     * 
     * @return array
     */
    public function all(): Collection
    {
        if (is_null($this->_blocks)) {
            $this->_blocks = collect();
            foreach (BlockRecord::find()->all() as $record) {
                $this->_blocks->push($this->create($record));
            }
        }
        return $this->_blocks;
    }

    public function create($config): BlockInterface
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        if (!isset($config['provider'])) {
            throw BlockException::noProviderInData(__METHOD__);
        }
        if (!isset($config['handle'])) {
            throw BlockException::noHandleInData(__METHOD__);
        } else {
            $handle = $config['handle'];
            unset($config['handle']);
        }
        $block = $this->blockProviderService()->getByHandle($config['provider'])->createBlock($handle); 
        $attributes = $block->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $block->setAttributes($config);
        return $block;
    }
    
    /**
     * Get block by id
     * 
     * @param  int    $id
     * @return ?BlockInterface
     */
    public function getById(int $id): ?BlockInterface
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * Get blocks for a layout
     * 
     * @param  int    $layout
     * @return array
     */
    public function getForLayout(Layout $layout): array
    {
        if (!$layout->id) {
            return [];
        }
        return $this->all()
            ->where('layout_id', $layout->id)
            ->values()
            ->all();
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

    public function saveMany(array $data, LayoutRecord $layout)
    {
        $ids = [];
        foreach ($data as $blockData) {
            $block = $this->getRecordByUid($blockData['uid']);
            $block->uid = $blockData['uid'];
            $block->provider = $blockData['provider'];
            $block->region = $blockData['region'];
            $block->handle = $blockData['handle'];
            $block->order = $blockData['order'];
            $block->options = $blockData['options'] ?? null;
            $block->layout_id = $layout->id;
            $block->save(false);
            $ids[] = $block->id;
        }
        $toDelete = BlockRecord::find()
            ->where(['layout_id' => $layout->id])
            ->andWhere(['not in', 'id', $ids])
            ->all();
        foreach ($toDelete as $block) {
            $block->delete();
        }
    }

    public function installContentBlock(ThemeInterface $theme)
    {
        if (!$region = $theme->contentBlockRegion()) {
            return;
        }
        foreach ($this->layoutService()->getForTheme($theme->handle, null, true) as $layout) {
            $block = $this->create([
                'provider' => 'system',
                'handle' => 'content',
                'region' => $region,
                'order' => 0
            ]);
            $layout->addBlock($block);
            $this->layoutService()->save($layout);
        }
    }
}