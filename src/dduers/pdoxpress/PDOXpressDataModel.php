<?php

declare(strict_types=1);

namespace Dduers\PDOXpress;

use PDO;

/**
 * pdo-xpress datamodel class
 */
class PDOXpressDataModel extends PDOXpressConnection
{
    /**
     * pdo-xpress connection
     */
    private PDOXpressConnection $connection;

    /**
     * mapped table
     */
    private string $table;

    /**
     * class constructor
     * @param PDOXpressConnection $connection instance of pdo-xpress connection class
     * @param string $table name of table to map
     */
    function __construct(PDOXpressConnection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * select one or more records from a table
     * @param array $arguments (optional) column => value pair assoc array for select arguments
     * @param array $columns (optional) column names for the select
     * @param int (optional) $attrCase the case of the column names in the result, will be reset after the query
     * @return bool true on success
     */
    public function select(
        array $arguments = [],
        array $columns = [],
        int $attrCase = PDO::CASE_NATURAL
    ): bool {
        $sql = "";
        $params = [];
        $sql_columns = [];
        $sql_arguments = [];
        if ($columns)
            foreach ($columns as $column)
                $sql_columns[] = "`$column`";
        else $sql_columns[] = "*";
        if ($arguments)
            foreach ($arguments as $column => $value) {
                $params[":" . $column] = $value;
                $sql_arguments[] = "`$column`=:$column";
            }
        else $sql_arguments[] = "1";
        $sql = "SELECT " . implode(",", $sql_columns) . " FROM `$this->table` WHERE " . implode(" AND ", $sql_arguments);
        return $this->connection->execQuery($sql, $params, $attrCase);
    }

    /**
     * insert a record to the mapped table
     * @param array $data data for the record
     * @param reference &$insertId will be filled with the record id
     * @return bool true on success
     */
    public function insert(
        array $data,
        bool $strip_tags = true
    ): bool {
        $sql = "";
        $sql_columns = [];
        $params = [];
        foreach ($data as $key => $value) {
            $params[":" . $key] =
                true === $strip_tags && is_string($value)
                ? strip_tags($value)
                : $value;
            $sql_columns[] = "`$key`";
        }
        $sql = "INSERT INTO `$this->table` (" . implode(",", $sql_columns) . ") VALUES (" . implode(",", array_keys($params)) . ")";
        return $this->connection->execQuery($sql, $params);
    }

    /**
     * update mapped table record by record id with new data
     * @param array $data new data for the record
     * @param int $recordId id of the record
     * @param string $recordIdColumn (optional) name of the id column
     * @return bool true on success
     */
    public function update(
        array $data,
        int $recordId,
        bool $strip_tags = true
    ): bool {
        if (!$recordIdColumn = $this->getPrimaryKeyColumnName())
            throw new PDOXpressException(PDOXpressException::CANNOT_UPDATE_TABLE_WITHOUT_PRIMARY_KEY);
        $sql = "";
        $sql_parts = [];
        $params = [];
        foreach ($data as $key => $value) {
            $params[":" . $key] =
                true === $strip_tags && is_string($value)
                ? strip_tags($value)
                : $value;
            $sql_parts[] = "`$key`=:$key";
        }
        $sql = "UPDATE `$this->table` SET " . implode(",", $sql_parts) . " WHERE `$recordIdColumn`=$recordId";
        return $this->connection->execQuery($sql, $params);
    }

    /**
     * delete a record from the mapped table
     * @param int $recordId id of the record
     * @return bool true on success
     */
    public function delete(
        int $recordId
    ): bool {
        if (!$recordIdColumn = $this->getPrimaryKeyColumnName())
            throw new PDOXpressException(PDOXpressException::CANNOT_DELETE_RECORD_FROM_TABLE_WITHOUT_PRIMARY_KEY);
        $sql = "DELETE FROM `$this->table` WHERE `$recordIdColumn`=$recordId";
        return $this->connection->execQuery($sql);
    }

    /**
     * select one or more records from a table and directly fetch to assoc array
     * @param array $arguments (optional) column => value pair assoc array for select arguments
     * @param array $columns (optional) column names for the select
     * @param int $attrCase (optional) case of resulting array keys
     * @param bool $htmlspecialchars (optional) set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function selectFetchAll(
        array $arguments = [],
        array $columns = [],
        int $attrCase = PDO::CASE_NATURAL,
        bool $htmlspecialchars = true
    ) {
        $this->select($arguments, $columns, $attrCase);
        return $this->connection->fetchAll($htmlspecialchars);
    }

    /**
     * select one or more records from a table and directly fetch to array of objects
     * @param array $arguments (optional) column => value pair assoc array for select arguments
     * @param array $columns (optional) column names for the select
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Object|NULL
     */
    public function selectFetchAllObject(
        array $arguments = [],
        array $columns = [],
        int $attrCase = PDO::CASE_NATURAL,
        bool $htmlspecialchars = true
    ) {
        $this->select($arguments, $columns, $attrCase);
        return $this->connection->fetchAllObject($htmlspecialchars);
    }

    /**
     * get primary key column name of mapped table
     * @return string|NULL
     */
    public function getPrimaryKeyColumnName()
    {
        switch ($this->connection->getDriverName()) {
            case 'mysql':
                $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$this->table' AND CONSTRAINT_NAME = 'PRIMARY';";
                $this->connection->execQuery($query);
                $result = $this->connection->fetch();
                return $result['COLUMN_NAME'] ?? NULL;
                break;
            default:
                throw new PDOXpressException(PDOXpressException::DATABASE_DRIVER_NOT_SUPPORTED_BY_PDOXPRESS);
                break;
        }
    }
}
