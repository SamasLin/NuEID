<?php
declare(strict_types=1);

namespace App\Libs\Kit;

use RuntimeException;

/**
 * Kit for processing application actions
 */
class AppKit
{
    private static $appConfig = [];
    private static $appVar    = [];

    /**
     * read application configs and save them
     * @param  string  $configPath  file path of config file
     * @return void
     */
    public static function readConfig(string $configPath)
    {
        static::$appConfig = array_merge(static::$appConfig, self::parseConfig($configPath));
    }

    /**
     * operate config with overloading
     *     config()            => get all config
     *     config($key)        => get specific config by key
     *     config($key, value) => set value with specific key
     *     note: $key can be string or array, refer to ArrayKit::get() and ArrayKit::set()
     * @return mixed
     */
    public static function config()
    {
        $argNum = func_num_args();
        if ($argNum == 0) {
            return static::$appConfig;
        }

        $key = func_get_arg(0);
        if ($argNum == 1) {
            return ArrayKit::get(static::$appConfig, $key);
        }
        return ArrayKit::set(static::$appConfig, $key, func_get_arg(1));
    }

    /**
     * operate config with overloading
     *     config()            => get all config
     *     config($key)        => get specific config by key
     *     config($key, value) => set value with specific key
     *     note: $key can be string or array, refer to ArrayKit::get() and ArrayKit::set()
     * @return mixed
     */
    public static function var()
    {
        $argNum = func_num_args();
        if ($argNum == 0) {
            return static::$appVar;
        }

        $key = func_get_arg(0);
        if ($argNum == 1) {
            return ArrayKit::get(static::$appVar, $key);
        }
        return ArrayKit::set(static::$appVar, $key, func_get_arg(1));
    }

    /**
     * read config file and convert to array, supported extension: php, json, yml, xml
     * @param  string  $configFilePath  file path of config file
     * @return array
     */
    private static function parseConfig(string $configFilePath): array
    {
        $fileType = pathinfo($configFilePath, PATHINFO_EXTENSION);
        $config = [];
        switch ($fileType) {
            case 'php':
                $config = require $configFilePath;
                break;
            case 'json':
                if (!extension_loaded('json')) {
                    throw new RuntimeException('json extension is not loaded!');
                }
                $config = json_decode(file_get_contents($configFilePath), true);
                break;
            case 'yaml':
            case 'yml':
                if (!extension_loaded('yaml')) {
                    throw new RuntimeException('yaml extension is not loaded!');
                }
                $config = yaml_parse(file_get_contents($configFilePath));
                break;
            case 'xml':
                if (!extension_loaded('libxml')) {
                    throw new RuntimeException('libxml extension is not loaded!');
                } elseif (!extension_loaded('simplexml')) {
                    throw new RuntimeException('simplexml extension is not loaded!');
                }
                $config = XMLKit::parse(simplexml_load_file($configFilePath, 'SimpleXMLElement', LIBXML_NOCDATA));
                break;
            default:
                throw new RuntimeException("type of config file '$configFilePath' not supports!");
                break;
        }
        return (array)$config;
    }
}
