<?php

namespace arkuuu\Publin;

use arkuuu\Publin\Modules\ModuleFactory;
use BadMethodCallException;

/**
 * Class FormatHandler
 *
 * @package arkuuu\Publin
 */
class FormatHandler
{

    /**
     * @param Publication $publication
     * @param             $format
     *
     * @return string
     */
    public static function export(Publication $publication, $format)
    {
        $module = (new ModuleFactory())->getModule($format);

        if (method_exists($module, 'export')) {

            return $module->export($publication);
        } else {
            throw new BadMethodCallException('export method in module for '.$format.' not found');
        }
    }


    /**
     * @param array $publications
     * @param       $format
     *
     * @return string
     */
    public static function exportMultiple(array $publications, $format)
    {
        $module = (new ModuleFactory())->getModule($format);

        if (method_exists($module, 'exportMultiple')) {

            return $module->exportMultiple($publications);
        } else {
            throw new BadMethodCallException('multiple export method in module for '.$format.' not found');
        }
    }


    /**
     * @param $data
     * @param $format
     *
     * @return mixed
     */
    public static function import($data, $format)
    {
        $module = (new ModuleFactory())->getModule($format);

        if (method_exists($module, 'import')) {

            return $module->import($data);
        } else {
            throw new BadMethodCallException('import method in module for '.$format.' not found');
        }
    }
}
