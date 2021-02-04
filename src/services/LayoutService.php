<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\events\LayoutLineEvent;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Layout;
use Ryssbowh\CraftThemes\models\LayoutLine;
use Ryssbowh\CraftThemes\models\PageLayout;
use Ryssbowh\CraftThemes\records\LayoutLineRecord;
use craft\base\Component;
use craft\events\ConfigEvent;
use craft\events\RebuildConfigEvent;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use yii\base\Event;

class LayoutService extends Component
{
	const EVENT_BEFORE_SAVE_LAYOUT = 1;
	const EVENT_AFTER_SAVE_LAYOUT = 2;
	const EVENT_BEFORE_APPLY_DELETE_LAYOUT = 3;
	const EVENT_AFTER_DELETE_LAYOUT = 4;
    const EVENT_BEFORE_DELETE_LAYOUT = 5;
    const LAYOUTS_CONFIG_KEY = 'themes.layouts';

    protected $layoutLines;

	public function getAllLayoutLines(): array
	{
        if (is_null($this->layoutLines)) {
            $this->layoutLines = [];
            foreach (LayoutLineRecord::find()->all() as $record) {
                $this->layoutLines[$record->id] = $record->toModel();
            }
        }
		return $this->layoutLines;
	}

	public function getLayoutLineById(int $id): ?LayoutLine
	{
        if (isset($this->layoutLines[$id])) {
            return $this->layoutLines[$id]->toModel();
        }
		$layout = LayoutLineRecord::find()->where(['id' => $id])->one();
        return $layout ? $layout->toModel() : null;
	}

	public function saveLayout(Layout $layout): bool
    {   
        $layout->validate();
        $ids = [];
        foreach ($layout->regions as $region => $lines) {
            foreach ($lines as $line) {
                $line = $this->saveLine($line, $region, $layout->theme);
                $ids[] = $line->id;
            }
        }
        $toDelete = LayoutLineRecord::find(['theme' => $layout->theme])->where(['not in', 'id', $ids])->all();
        foreach ($toDelete as $line) {
            $this->deleteLine($line);
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

    public function handleChanged(ConfigEvent $event)
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

        $this->triggerEvent(self::EVENT_BEFORE_DELETE_LAYOUT, new LayoutLineEvent([
            'line' => $line->toModel(),
            'isNew' => $isNew,
        ]));
    }

    public function handleDeleted(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        $line = $this->getLayoutLineRecordByUid($uid);

        if (!$line) {
            return;
        }

        $this->triggerEvent(self::EVENT_BEFORE_APPLY_DELETE_LAYOUT, new LayoutLineEvent([
            'line' => $line
        ]));

        \Craft::$app->getDb()->createCommand()
            ->delete(LayoutLineRecord::tableName(), ['uid' => $uid])
            ->execute();

        $this->triggerEvent(self::EVENT_AFTER_DELETE_LAYOUT, new LayoutLineEvent([
            'line' => $line
        ]));
    }

    public function getLayout(ThemeInterface $theme): Layout
    {
        return (new Layout([
            'theme' => $theme
        ]))->loadFromDb();
    }

    public function getPageLayout(ThemeInterface $theme): PageLayout
    {
        return (new PageLayout([
            'theme' => $theme
        ]))->loadFromDb();
    }

    protected function triggerEvent(string $type, Event $event)
    {
        if ($this->hasEventHandlers($type)) {
            $this->trigger($type, $event);
        }
    }

    protected function deleteLine(LayoutLineRecord $record): bool
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE_LAYOUT, new LayoutLineEvent([
            'line' => $record
        ]));
        \Craft::$app->getProjectConfig()->remove(self::LAYOUTS_CONFIG_KEY . '.' . $record->uid);
        return true;
    }

    protected function saveLine(LayoutLine $line, string $region, string $theme): LayoutLine
    {
        $this->triggerEvent(self::EVENT_BEFORE_DELETE_LAYOUT, new LayoutLineEvent([
            'line' => $line
        ]));
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