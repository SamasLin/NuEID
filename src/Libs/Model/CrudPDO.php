<?php
declare(strict_types=1);

namespace App\Libs\Model;


use PDO, RuntimeException;
use App\Libs\Kit\AppKit;

class CrudPDO extends PDO
{
    private static $dbConfig = [];

    /**
     * __construct
     * @param  string  $dataSourceName  data source name
     * @param  string  $database  database name
     * @return void
     */
    public function __construct(string $dataSourceName, string $database)
    {
        if (empty(self::$dbConfig)) {
            $this->initDbConfig();
        }
        if (!isset(self::$dbConfig[$dataSourceName])) {
            throw new RuntimeException("Config of data source 'database.$dataSourceName' is missing!\n");
        } elseif (empty(self::$dbConfig[$dataSourceName]['host'])) {
            throw new RuntimeException("Config of data source 'database.$dataSourceName' need host attributes!\n");
        }

        $type     = self::$dbConfig[$dataSourceName]['type'] ?? 'mysql';
        $host     = self::$dbConfig[$dataSourceName]['host'];
        $port     = self::$dbConfig[$dataSourceName]['port'] ?? 5432;
        $dbname   = $database ?: self::$dbConfig[$dataSourceName]['dbname'];
        $user     = self::$dbConfig[$dataSourceName]['user'] ?? '';
        $password = self::$dbConfig[$dataSourceName]['password'] ?? '';

        $dsn = sprintf("%s:host=%s;port=%s;dbname=%s;options='-c client_encoding=utf8'", $type, $host, $port, $dbname);
        parent::__construct($dsn, $user, $password);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * make sure config is complete
     * @return void
     */
    private function initDbConfig()
    {
        if (!defined('CONFIG_FILE_PATH')) {
            throw new RuntimeException("Constant CONFIG_FILE_PATH is not defined!\n");
        }
        if (empty(AppKit::config())) {
            $config = AppKit::readConfig(CONFIG_FILE_PATH);
        }
        if (empty(AppKit::config('database'))) {
            throw new RuntimeException("Config of 'database' is missing!\n");
        }
        self::$dbConfig = AppKit::config('database');
    }
}
