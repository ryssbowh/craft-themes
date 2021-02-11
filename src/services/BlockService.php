<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\BlockEvent;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\records\BlockRecord;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\StringHelper;

class BlockService extends Service
{
	const EVENT_BEFORE_SAVE_BLOCK = 1;
	const EVENT_AFTER_SAVE_BLOCK = 2;
	const EVENT_BEFORE_APPLY_DELETE_BLOCK = 3;
	const EVENT_AFTER_DELETE_BLOCK = 4;
    const EVENT_BEFORE_DELETE_BLOCK = 5;
    const CONFIG_KEY = 'themes.blocks';

	public static function createBlock(string $blockClass, array $attributes = []): BlockInterface
	{
		unset($attributes['handle']);
		return new $blockClass($attributes);
	}

	public function getById(int $id): ?BlockInterface
	{
		$block = BlockRecord::find()->where(['id' => $id])->one();
        return $block ? $block->toModel() : null;
	}

	public function getForTheme(ThemeInterface $theme): array
	{
		return array_map(function ($record) {
			return $record->toModel();
		}, BlockRecord::find()->where(['theme' => $theme->getHandle()])->all());
	}

	public function rebuildBlocksConfig(RebuildConfigEvent $e)
    {
    	$parts = explode('.', self::CONFIG_KEY);
        foreach ($this->getAllBlockRecords() as $block) {
            $e->config[$parts[0]][$parts[1]][$block->uid] = $block->getConfig();
        }
    }

    public function getAllBlocks(): array
	{
		$blocks = [];
        foreach (BlockRecord::find()->all() as $record) {
            $blocks[$record->id] = $record->toModel();
        }
		return $blocks;
	}

	public function getRecordByUid(string $uid): BlockRecord
	{
		return BlockRecord::findOne(['uid' => $uid]) ?? new BlockRecord;
	}

    public function saveBlocks(array $blocks, string $theme, $validate = true): bool
    {   
        $ids = [];
        $hasErrors = false;
        foreach ($blocks as $block) {
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
            $toDelete = BlockRecord::find(['theme' => $theme])->where(['not in', 'id', $ids])->all();
            foreach ($toDelete as $block) {
                $this->blockService()->deleteBlock($block);
            }
        }
        return !$hasErrors;
    }

	public function saveBlock(Block $block): Block
    {
        $isNew = !is_int($block->id);
        $uid = $isNew ? StringHelper::UUID() : $block->uid;

        $this->triggerEvent(self::EVENT_BEFORE_SAVE_BLOCK, new BlockEvent([
            'block' => $block
        ]));

        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $block->getConfig();
        $configPath = self::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = $this->getRecordByUid($uid);
        $record->ignore = $block->ignore;
        $record->save(false);
        
        if ($isNew) {
            $block->id = $record->id;
            $block->dateCreated = $record->dateCreated;
            $block->dateUpdated = $record->dateUpdated;
        }

        return $block;
    }

    public function deleteBlock(BlockRecord $record): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE_BLOCK, new BlockEvent([
            'block' => $record
        ]));
        \Craft::$app->getProjectConfig()->remove(self::CONFIG_KEY . '.' . $record->uid);
        return true;
    }

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
            $block->theme = $data['theme'];
            $block->handle = $data['handle'];
            $block->provider = $data['provider'];
            $block->order = $data['order'];
            $block->active = $data['active'];
            
            $block->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->triggerEvent(self::EVENT_AFTER_SAVE_BLOCK, new BlockEvent([
            'block' => $block,
            'isNew' => $isNew,
        ]));
    }

    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $block = $this->getRecordByUid($uid);

        if (!$block) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE_BLOCK, new BlockEvent([
            'block' => $block
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(BlockRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE_BLOCK, new BlockEvent([
            'block' => $block
        ]));
    }

    public function fromData(array $data): Block
    {
        unset($data['index']);
        if ($data['id'] ?? false) {
            $block = $this->getById($data['id']);
            unset($data['handle']);
            $block->setAttributes($data);
            return $block;
        }
        if (!isset($data['provider'])) {
            throw BlockException::noProviderInData(__METHOD__);
        }
        if (!isset($data['handle'])) {
            throw BlockException::noHandleInData(__METHOD__);
        }
        $provider = $this->providerService()->getByHandle($data['provider']); 
        return $provider->getBlock($data['handle'], $data);
    }
}