<?php
declare(strict_types=1);

namespace App\Libs\Model;

use PDO, RuntimeException;
use App\Libs\Kit\{AppKit, ArrayKit};

class CrudModel
{
    private static $dbPool;
    private $execResult = false;
    private $statement;
    private $database;
    private $table;

    /**
     * construct
     * @param string  $table     table name
     * @param string  $database  database name
     */
    public function __construct(string $table = '', string $database = '')
    {
        $this->table    = "`{$table}`";
        $this->database = $database;
    }

    /**
     * execute sql
     * @param  string  $sql       sql
     * @param  array   $params    binding parameters
     * @param  bool    $useSlave  force to use slave data source
     * @return mixed
     */
    public function exec(string $sql = '', array $params = [], bool $useSlave = false)
    {
        $dataSourceName = $useSlave ? 'slave' : 'master';
        $this->executePreparedSQL($dataSourceName, $sql, $params);
        return $this->execResult;
    }

    /**
     * fetch one record
     * @return mixed
     */
    public function getOne()
    {
        return $this->execResult ? $this->statement->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * fetch all records
     * @return mixed
     */
    public function getAll()
    {
        return $this->execResult ? $this->statement->fetchAll(PDO::FETCH_ASSOC) : false;
    }

    /**
     * get affteced rows count
     * @return mixed
     */
    public function rowCount()
    {
        return $this->execResult ? $this->statement->rowCount() : false;
    }

    /**
     * execute insert command
     * @param  array   $data  insert data
     * @return bool
     */
    public function insert(array $data = [])
    {
        if (empty($this->table)) {
            throw new RuntimeException("Cannot use '" . __FUNCTION__ . "' without table!\n");
        } elseif (empty($data)) {
            return 0;
        }
        $insertField = [];
        $fieldValue = [];
        $params = [];
        foreach ($data as $key => $value) {
            $insertField[] = "`$key`";
            if ($value instanceof DBsyntax) {
                $fieldValue[] = $value->getVal();
            } else {
                $fieldValue[] = ':' . $key;
                $params[':' . $key] = $value;
            }
        }
        $now = date('c');
        if (!isset($data['created_time'])) {
            $insertField[] = '"created_time"';
            $fieldValue[] = ':created_time';
            $params[':created_time'] = $now;
        }
        if (!isset($data['modify_time'])) {
            $insertField[] = '"modify_time"';
            $fieldValue[] = ':modify_time';
            $params[':modify_time'] = $now;
        }
        $sql = "INSERT INTO {$this->table} (" . implode(',', $insertField) .
               ") VALUES (" . implode(',', $fieldValue) . ")";
        return $this->executePreparedSQL('master', $sql, $params);
    }

    /**
     * execute select command
     * @param  string  $where   where condition
     * @param  array   $params  binding parameters
     * @param  string  $field   select field
     * @param  string  $order   order by condition
     * @return mixed
     */
    public function select(
        string $where = '',
        array $params = [],
        string $field = '',
        string $order = '',
        int $offset = 0,
        int $limit = 0
    ) {
        if (empty($this->table)) {
            throw new RuntimeException("Cannot use '" . __FUNCTION__ . "' without table!\n");
        }
        $field = empty(trim($field)) ? '*' : trim($field);
        $sql = "SELECT $field FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        if (!empty($order)) {
            $sql .= " ORDER BY $order";
        }
        if ($offset > 0) {
            $sql .= " OFFSET $offset";
        }
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
        }
        $this->executePreparedSQL('slave', $sql, $params);
        return $this->getAll();
    }

    /**
     * execute select command, and return the first one record
     * @param  string  $where   where condition
     * @param  array   $params  binding parameters
     * @param  string  $field   select field
     * @return mixed
     */
    public function find(string $where = '', array $params = [], string $field = '')
    {
        if (empty($this->table)) {
            throw new RuntimeException("Cannot use '" . __FUNCTION__ . "' without table!\n");
        }
        $field = empty(trim($field)) ? '*' : trim($field);
        $sql = "SELECT $field FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $this->executePreparedSQL('slave', $sql, $params);
        return $this->getOne();
    }

    /**
     * execute count command
     * @param  string  $where   where condition
     * @param  array   $params  binding parameters
     * @return mixed
     */
    public function count(string $where = '', array $params = [])
    {
        if (empty($this->table)) {
            throw new RuntimeException("Cannot use '" . __FUNCTION__ . "' without table!\n");
        }
        $sql = "SELECT COUNT(1) AS cnt FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $this->executePreparedSQL('slave', $sql, $params);
        if (!$this->execResult) {
            return false;
        }
        $data = $this->getOne();
        return isset($data['cnt']) ? $data['cnt'] : false;
    }

    /**
     * execute update command
     * @param  array   $data    update data
     * @param  string  $where   where condition
     * @param  array   $params  binding parameters
     * @return mixed
     */
    public function update(array $data = [], string $where = '', array $params = [])
    {
        if (empty($this->table)) {
            throw new RuntimeException("Cannot use '" . __FUNCTION__ . "' without table!\n");
        } elseif (empty($data)) {
            return 0;
        }
        $updateFields = [];
        foreach ($data as $key => $value) {
            if ($value instanceof DBsyntax) {
                $updateFields[] = "`$key` = {$value->getVal()}";
            } else {
                $updateFields[] = "`$key` = :upd_$key";
                $params[":upd_$key"] = $value;
            }
        }
        $now = date('c');
        if (!isset($data['modify_time'])) {
            $updateFields[] = '"modify_time" = :modify_time';
            $params[':modify_time'] = $now;
        }
        $sql = "UPDATE {$this->table} SET " . implode(',', $updateFields);
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $this->executePreparedSQL('master', $sql, $params);
        return $this->rowCount();
    }

    /**
     * execute delete command
     * @param  string  $where   where condition
     * @param  array   $params  binding parameters
     * @return mixed
     */
    public function delete(string $where = '', array $params = [])
    {
        if (empty($this->table)) {
            throw new RuntimeException("Cannot use '" . __FUNCTION__ . "' without table!\n");
        }
        $sql = "DELETE FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $this->executePreparedSQL('master', $sql, $params);
        return $this->rowCount();
    }

    /**
     * execute prepared sql flow
     * @param  string  $sql     sql
     * @param  array   $params  parameters to bind
     * @return bool
     */
    private function executePreparedSQL(string $dataSourceName, string $sql, array $params): bool
    {
        $db = $this->getCrudPDO($dataSourceName);
        $this->statement = $db->prepare($sql);
        foreach ($params as $key => &$param) {
            $this->statement->bindParam($key, $param, $this->getParamType($param));
        }

        if (AppKit::config('sql_collection')) {
            $history = [
                'source' => $dataSourceName,
                'time'   => '-',
                'execute' => [
                    'sql'    => $sql,
                    'params' => $params
                ]
            ];

            $traceList = debug_backtrace(0);
            foreach ($traceList as &$trace) {
                $file = $trace['file'] ?? '-';
                $line = $trace['line'] ?? '-';
                $class = $trace['class'] ?? '';
                $type = $trace['type'] ?? '';
                $function = $trace['function'] ?? '';

                $suffixContrller = 'Controller.php';
                $suffixService = 'Service.php';
                $suffixMiddleware = 'Middleware.php';
                if (substr($file, strlen($suffixContrller) * -1) === $suffixContrller ||
                    substr($file, strlen($suffixService) * -1) === $suffixService ||
                    substr($file, strlen($suffixMiddleware) * -1) === $suffixMiddleware
                ) {
                    $code = [];
                    $code['function'] = "{$class}{$type}{$function}()";
                    $code['args'] = $trace['args'] ?? [];
                    $code['file'] = "{$file}:{$line}";
                    ArrayKit::set($history, ['code', ''], $code);
                }
            }
            unset($trace);

            AppKit::config(['sqlHistory', ''], $history);
        }

        $start = microtime(true);
        $this->execResult = $this->statement->execute();
        $end = microtime(true);

        if (AppKit::config('sql_collection')) {
            $sqlHistory = AppKit::config('sqlHistory');
            $lastSql = array_pop($sqlHistory);
            $lastSql['time'] = ($end - $start) * 1000;
            $sqlHistory[] = $lastSql;
            AppKit::config('sqlHistory', $sqlHistory);
        }
        return $this->execResult;
    }

    /**
     * get CrudPDO instance
     * @param  string  $dataSourceName  data source name
     * @return TakoTool\Database\CrudPDO
     */
    private function getCrudPDO(string $dataSourceName)
    {
        if (!isset(self::$dbPool[$dataSourceName])) {
            self::$dbPool[$dataSourceName] = new CrudPDO($dataSourceName, $this->database);
        }
        return self::$dbPool[$dataSourceName];
    }

    /**
     * get relative data type constant of parameter
     * @param  mixed  $param  parameter
     * @return int
     */
    private function getParamType($param): int
    {
        $paramType = PDO::PARAM_STR;
        if (is_bool($param)) {
            $paramType = PDO::PARAM_BOOL;
        } elseif ($param === null) {
            $paramType = PDO::PARAM_NULL;
        } elseif (is_int($param) || is_float($param)) {
            $paramType = PDO::PARAM_INT;
        }
        return $paramType;
    }
}
