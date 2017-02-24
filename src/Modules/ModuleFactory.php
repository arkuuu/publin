<?php

namespace arkuuu\Publin\Modules;

use Exception;

/**
 * Class ModuleFactory
 *
 * @package arkuuu\Publin\Modules
 */
class ModuleFactory
{

    /**
     * @param string $format
     *
     * @return ModuleInterface
     * @throws Exception
     */
    public function getModule($format)
    {
        $class = 'arkuuu\\Publin\\Modules\\'.$format;

        if (!class_exists($class)) {
            throw new Exception('module class for '.$format.' not found');
        }

        return new $class();
    }
}
