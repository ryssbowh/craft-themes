<?php
namespace Ryssbowh\CraftThemes\interfaces;

use Ryssbowh\CraftThemes\models\FieldDisplayerOptions;

/**
 * Interface for field displayers that render files (Assets, user photo etc)
 *
 * @since 4.2.0
 */
interface FileFieldDisplayerInterface
{
    public function getDisplayerForKind(string $kind): ?FileDisplayerInterface;
}