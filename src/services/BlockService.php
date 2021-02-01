<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\LayoutLineEvent;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\models\Layout;
use Ryssbowh\CraftThemes\models\LayoutLine;
use Ryssbowh\CraftThemes\records\LayoutLineRecord;
use craft\base\Component;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\Db;
use craft\helpers\StringHelper;

class BlockService extends Component
{
	const EVENT_BEFORE_SAVE_LAYOUT = 1;
	const EVENT_AFTER_SAVE_LAYOUT = 2;
	const EVENT_BEFORE_APPLY_DELETE_LAYOUT = 3;
	const EVENT_AFTER_DELETE_LAYOUT = 4;
    const EVENT_BEFORE_DELETE_LAYOUT = 5;
    const LAYOUTS_CONFIG_KEY = 'themes.layouts';

    protected $blockLayouts;

	public static function createBlock($block): BlockInterface
	{
		if (is_array($block)) {
			if (!isset($block['class'])) {
				throw BlockException::noClass(__METHOD__);
			}
			$blockClass = $block['class'];
			unset($block['class']);
			$block = new $blockClass($block);
		} elseif (is_string($block)) {
			$block = new $block;
		}
		if (!$block instanceof BlockInterface) {
			throw BlockException::notAblock(__METHOD__);
		}
		return $block;
	}

	public function getAllLayoutLines(): array
	{
        if (is_null($this->blockLayouts)) {
            $this->blockLayouts = [];
            foreach (LayoutLineRecord::find()->all() as $record) {
                $this->blockLayouts[$record->id] = $record->toModel();
            }
        }
		return $this->blockLayouts;
	}

	public function getLayoutLineById(int $id): ?LayoutLine
	{
		$layout = LayoutLineRecord::find()->where(['id' => $id])->one();
        return $layout ? $layout->toModel() : null;
	}

	public function saveLayout(Layout $layout): bool
    {   
        $layout->validate();
        foreach ($layout->regions as $region => $lines) {
            foreach ($lines as $line) {
                $this->saveLayoutLine($line, $region, $layout->theme);
            }
        }
        return true;
    }

    public function getLayoutLineRecordByUid(string $uid): LayoutLineRecord
	{
		return LayoutLineRecord::findOne(['uid' => $uid]) ?? new LayoutLineRecord(['uid' => $uid]);
	}

	public function rebuildLayoutConfig(RebuildConfigEvent $e)
    {
    	$parts = explode('.', self::LAYOUTS_CONFIG_KEY);
        foreach ($this->getAllLayoutLines() as $line) {
            $e->config[$parts[0]][$parts[1]][$line->uid] = $line->getConfig();
        }
    }

    public function handleLayoutChanged(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $data = $event->newValue;
        $transaction = \Craft::$app->getDb()->beginTransaction();

        try {
            $line = $this->getLayoutLineRecordByUid($uid);
            $isNew = $line->getIsNewRecord();

            $line->region = $data['region'];
            $line->theme = $data['theme'];
            $line->blockHandle = $data['blockHandle'];
            $line->blockProvider = $data['blockProvider'];
            $line->order = $data['order'];
            $line->active = $data['active'];
            
            $line->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_LAYOUT)) {
            $this->trigger(self::EVENT_AFTER_SAVE_LAYOUT, new LayoutLineEvent([
                'line' => $line->toModel(),
                'isNew' => $isNew,
            ]));
        }
    }

    public function getLayoutForTheme(string $theme)
    {
        return (new Layout([
            'theme' => $theme
        ]))->loadFromDb();
    }

    protected function saveLayoutLine(LayoutLine $line, string $region, string $theme): LayoutLine
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_LAYOUT)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_LAYOUT, new LayoutLineEvent([
                'line' => $line
            ]));
        }
        $line->uid = $line->id ? $line->uid : StringHelper::UUID();
        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $line->getConfig();
        $configPath = self::LAYOUTS_CONFIG_KEY . '.' . $line->uid;
        $projectConfig->set($configPath, $configData);

        if (!$line->id) {
            $line->id = Db::idByUid(LayoutLineRecord::tableName(), $line->uid);
        }

        return $line;
    }
}