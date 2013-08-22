<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZF\Configuration;

use ReflectionObject;
use Zend\Config\Writer\WriterInterface as ConfigWriter;
use Zend\ModuleManager\ModuleManager;

class ResourceFactory
{
    /**
     * @var array
     */
    protected $modules;

    /**
     * @var ConfigWriter
     */
    protected $writer;

    /**
     * @var ConfigResource[]
     */
    protected $resources = array();

    /**
     * @param  ModuleManager $modules 
     * @param  ConfigWriter $writer 
     */
    public function __construct(ModuleManager $modules, ConfigWriter $writer)
    {
        $this->modules = $modules->getLoadedModules();
        $this->writer  = $writer;
    }

    /**
     * Retrieve a ConfigResource for a given module
     * 
     * @param  string $moduleName
     * @return ConfigResource
     * @throws Exception\InvalidArgumentException for unknown modules
     * @throws Exception\RuntimeException if unable to locate module config
     */
    public function factory($moduleName)
    {
        if (isset($this->resources[$moduleName])) {
            return $this->resources[$moduleName];
        }

        if (!array_key_exists($moduleName, $this->modules)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The module specified, "%s", does not exist; cannot retrieve configuration resource',
                $moduleName
            ));
        }

        $moduleConfigPath = $this->deriveModuleConfig($moduleName);
        $config           = include($moduleConfigPath);

        $this->resources[$moduleName] = ConfigResource($config, $moduleConfigPath, $this->writer);
        return $this->resources[$moduleName];
    }

    /**
     * Derives the module class's filesystem location
     * 
     * @param  string $moduleName 
     * @return string
     */
    protected function getModuleClassPath($moduleName)
    {
        $module   = $this->modules[$moduleName];
        $r        = new ReflectionObject($module);
        $fileName = $r->getFileName();
        return dirname($fileName);
    }

    /**
     * Determines the location of the module configuration file
     * 
     * @param  string $moduleName 
     * @return string
     * @throws Exception\RuntimeException if unable to find the configuration file
     */
    protected function deriveModuleConfig($moduleName)
    {
        $moduleClassPath = $this->getModuleClassPath($moduleName);
        $configPath      = $this->recurseTree($moduleClassPath);

        if (false === $configPath) {
            throw new Exception\RuntimeException(sprintf(
                'Unable to determine configuration path for module "%s"',
                $moduleName
            ));
        }

        return $configPath;
    }

    /**
     * Recurse upwards through a tree to find the module configuration file
     * 
     * @param  string $path 
     * @return false|string
     */
    protected function recurseTree($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        if (file_exists($path . '/config/module.config.php')) {
            return $path;
        }

        if (in_array(array('.', '/', '\\\\', '\\'), $path)
            || preg_match('#[a-z]:\\\\#i', $path)
        ) {
            // Don't recurse past the root
            return false;
        }

        return $this->recurseTree(dirname($path));
    }
}
