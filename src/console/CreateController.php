<?php
namespace Ryssbowh\CraftThemes\console;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\CreatorException;
use craft\console\Controller;
use yii\console\ExitCode;

class CreateController extends Controller
{   
    /**
     * Creates a new theme
     * 
     * @param  string
     * @param  string made up from the name if empty
     * @param  string The backslashes must be doubled (\\), made up from the name if empty
     * @param  string made up from the name if empty
     * @param  string taken from settings if empty
     * @return int
     */
    public function actionTheme(string $name, string $handle = '', string $namespace = '', string $className = '', string $folder = null)
    {
        if ($folder === null) {
            $folder = Themes::$plugin->settings->folder;
        }
        try {
            list($handle, $className, $namespace) = Themes::$plugin->creator->createTheme($name, $handle, $namespace, $className, $folder);
        } catch (CreatorException $e) {
            $this->stderr($e->getMessage() . "\n");
            return $e->getCode();
        }
        $this->stdout(\Craft::t("themes", "The theme $name has been created, you can require it in composer with 'composer require themes/$handle'") . "\n");
        $this->stdout(\Craft::t('themes', "Make sure your root composer.json defines the folder '$folder' as a repository :") . "\n");
        $this->stdout('"repositories": {
    "themes": {
        "type": "path",
        "url": "' . $folder . '/*",
        "options": {
            "symlink": true
        }
    }
}' . "\n");
        return ExitCode::OK;
    }

    /**
     * Create a new block class for a theme
     * 
     * @param  string Theme handle
     * @param  string
     * @param  string Made up from the name if empty
     * @param  string
     * @param  string Made up from the name if empty
     * @return int
     */
    public function actionBlock(string $theme, string $name, string $handle = '', string $description = '', string $className = '')
    {
        try {
            Themes::$plugin->creator->createBlock($theme, $name, $handle, $description, $className);
        } catch (CreatorException $e) {
            $this->stderr($e->getMessage() . "\n");
            return $e->getCode();
        }
        $this->stdout(\Craft::t("themes", "The block $name has been created") . "\n");
        return ExitCode::OK;
    }

    /**
     * Create a new block provider class for a theme
     * 
     * @param  string Theme handle
     * @param  string
     * @param  string Made up from the name if empty
     * @param  string Made up from the name if empty
     * @return int
     */
    public function actionBlockProvider(string $theme, string $name, string $handle = '', string $className = '')
    {
        try {
            Themes::$plugin->creator->createBlockProvider($theme, $name, $handle, $className);
        } catch (CreatorException $e) {
            $this->stderr($e->getMessage() . "\n");
            return $e->getCode();
        }
        $this->stdout(\Craft::t("themes", "The block provider $name has been created") . "\n");
        return ExitCode::OK;
    }

    /**
     * Create a new field displayer class for a theme
     * 
     * @param  string Theme handle
     * @param  string
     * @param  string Made up from the name if empty
     * @param  string Made up from the name if empty
     * @return int
     */
    public function actionFieldDisplayer(string $theme, string $name, string $handle = '', string $className = '')
    {
        try {
            Themes::$plugin->creator->createFieldDisplayer($theme, $name, $handle, $className);
        } catch (CreatorException $e) {
            $this->stderr($e->getMessage() . "\n");
            return $e->getCode();
        }
        $this->stdout(\Craft::t("themes", "The field displayer $name has been created") . "\n");
        return ExitCode::OK;
    }

    /**
     * Create a new file displayer class for a theme
     * 
     * @param  string Theme name
     * @param  string
     * @param  string Made up from the name if empty
     * @param  string Made up from the name if empty
     * @return int
     */
    public function actionFileDisplayer(string $theme, string $name, string $handle = '', string $className = '')
    {
        try {
            Themes::$plugin->creator->createFileDisplayer($theme, $name, $handle, $className);
        } catch (CreatorException $e) {
            $this->stderr($e->getMessage() . "\n");
            return $e->getCode();
        }
        $this->stdout(\Craft::t("themes", "The file displayer $name has been created") . "\n");
        return ExitCode::OK;
    }
}