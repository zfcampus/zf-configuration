<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZF\Configuration;

use stdClass;
use Traversable;
use Zend\Config\Writer\WriterInterface as ConfigWriter;
use Zend\Stdlib\ArrayUtils;

class ConfigResource
{
    /**
     * @var array
     */
    protected $config;

    /**
     * File to which to write configuration
     * 
     * @var string
     */
    protected $fileName;

    /**
     * @var ConfigWriter
     */
    protected $writer;

    /**
     * @param array $config 
     */
    public function __construct(array $config, $fileName, ConfigWriter $writer)
    {
        $this->config   = $config;
        $this->fileName = $fileName;
        $this->writer   = $writer;
    }

    /**
     * Allow patching one or more key/value pairs
     *
     * Expects data to be in the form of key/value pairs
     * 
     * @param  array|stdClass|Traversable $data 
     * @return array
     */
    public function patch($data, $tree = false)
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }

        if ($data instanceof stdClass) {
            $data = (array) $data;
        }

        // Update configuration from dot-separated key/value pairs
        if (!$tree) {
            $patchValues = array();
            foreach ($data as $key => $value) {
                $this->createNestedKeyValuePair($patchValues, $key, $value);
            }
        } else {
            $patchValues = $data;
        }

        // Get local config file
        $localConfig = array();
        if (file_exists($this->fileName)) {
            $localConfig = include $this->fileName;
            if (!is_array($localConfig)) {
                $localConfig = array();
            }
        }
        $localConfig = ArrayUtils::merge($localConfig, $patchValues);

        // Write to configuration file
        $this->writer->toFile($this->fileName, $localConfig);

        // Return written values
        return $data;
    }

    /**
     * Fetch all configuration values
     *
     * Flattens nested configuration to dot-separated key/value pairs and returns them.
     * 
     * @param  array $params 
     * @return array
     */
    public function fetch($tree = false)
    {
        // If requested as a tree, return as-is
        if ($tree) {
            return $this->config;
        }

        // Collapse to key/value pairs -- meaning to dot-separated nested keys
        return $this->traverseArray($this->config);
    }

    /**
     * Traverse a nested array and flatten to dot-separated key/value pairs
     * 
     * @param  array $array 
     * @param  string $currentKey Current key, if called recursively
     * @return array
     */
    public function traverseArray(array $array, $currentKey = '')
    {
        $flattened = array();
        foreach ($array as $key => $value) {
            $targetKey = ('' === $currentKey) ? $key : $currentKey . '.' . $key;
            if (is_array($value)) {
                $value = $this->traverseArray($value, $targetKey);
                $flattened = array_merge($flattened, $value);
                continue;
            }

            $flattened[$targetKey] = $value;
        }
        return $flattened;
    }

    /**
     * Create a nested key/value pair from a dot-separated key value pair
     *
     * Extracts the nested pair into the array provided in $patchValues
     * 
     * @param array $patchValues 
     * @param string $key 
     * @param mixed $value 
     */
    public function createNestedKeyValuePair(&$patchValues, $key, $value)
    {
        if (!is_array($patchValues)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects the $patchValues argument to be an array; received %s',
                __METHOD__,
                (is_object($patchValues) ? get_class($patchValues) : gettype($patchValues))
            ));
        }

        $this->extractAndSet(explode('.', $key), $value, $patchValues);
    }

    /**
     * Recursively extract keys into a nested array
     * 
     * @param array $keys 
     * @param string $value 
     * @param array $array 
     */
    protected function extractAndSet(array $keys, $value, &$array)
    {
        $key = array_shift($keys);
        if (count($keys)) {
            $array[$key] = array();
            $reference   = &$array[$key];
            $this->extractAndSet($keys, $value, $reference);
            return;
        }
        $array[$key] = $value;
    }
}
