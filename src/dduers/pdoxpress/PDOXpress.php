<?php
declare(strict_types=1);
namespace Dduers\PDOXpress;

class PDOXpress extends \PDO {

    private ?\PDOStatement $statement;

    /**
     * pdo constructor
     * more information at https://www.php.net/manual/de/pdo.construct.php
     */
    public function __construct(string $dsn, string $username = '', string $passwd = '', array $options = [])
    {
        parent::__construct($dsn, $username, $passwd, $options);
        if (empty($options)) {
            $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * make an sql query
     * @param string $sql the sql query
     * @param array (optional) $params params, if the sql query contains placeholders
     * @return bool true on success
     */
    public function query(string $sql, array $params = []) : bool
    {
        $this->statement = $this->prepare($sql);
        return $this->statement->execute($params);
    }

    /**
     * fetch one record from a select query as assoc array
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function fetch(bool $htmlspecialchars = false)
	{
        if (!$this->statement)
            return NULL;
        if (!($result = $this->statement->fetch(\PDO::FETCH_ASSOC)))
            return NULL;
        if ($htmlspecialchars)
            foreach ($result as $key => $value)
                if (!is_numeric($value))
                    $result[$key] = htmlspecialchars($value);
        return $result;
    }

    /**
     * fetch all records from a select query at once as assoc array
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function fetchAll(bool $htmlspecialchars = false)
	{
        if (!$this->statement)
            return NULL;
        if (!($result = $this->statement->fetchAll(\PDO::FETCH_ASSOC)))
            return NULL;
        if ($htmlspecialchars)
            foreach ($result as $key => $value)
                foreach ($value as $column => $content)
                    if (!is_numeric($content))
                        $result[$key][$column] = htmlspecialchars($content);
        return $result;
    }

    /**
     * fetch one record from a select query as object
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Object|NULL
     */
    public function fetchObject(bool $htmlspecialchars = false)
    {
        if (!$this->statement)
            return NULL;
        if (!($result = $this->statement->fetch(\PDO::FETCH_OBJ)))
            return NULL;
        if ($htmlspecialchars)
            foreach ($result as $key => $value)
                if (!is_numeric($value))
                    $result->$key = htmlspecialchars($value);
        return $result;
    }

    /**
     * fetch all records from a select query at once as array of objects
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Object|NULL
     */
    public function fetchAllObject(bool $htmlspecialchars = false)
    {
        if (!$this->statement)
            return NULL;
        if (!($result = $this->statement->fetchAll(\PDO::FETCH_OBJ)))
            return NULL;
        if ($htmlspecialchars)
            foreach ($result as $key => $value)
                foreach ($value as $column => $content)
                    if (!is_numeric($content))
                        $result[$key]->$column = htmlspecialchars($content);
        return $result;
    }

    /**
     * insert a record to a table
     * @param string $table table name
     * @param array $data data for the record
     * @param reference &$insertId will be filled with the record id
     * @return bool true on success
     */
    public function insert(string $table, array $data, bool $stripTags = false) : bool
    {
        $sql = "";
        $params = [];
        foreach ($data as $key => $value)
            $params[":".$key] = $stripTags ? strip_tags($value) : $value;
        $sql = "INSERT INTO `$table` (".implode(",", array_keys($data)).") VALUES (".implode(",", array_keys($params)).")";
        $result = $this->query($sql, $params);
        $insertId = $this->lastInsertId();
        return $result;
    }

    /**
     * update a table record by record id with new data
     * @param string $table table name
     * @param array $data new data for the record
     * @param int $recordId id of the record
     * @param string $recordIdColumn (optional) name of the id column
     * @return bool true on success
     */
    public function update(string $table, array $data, int $recordId, string $recordIdColumn = 'id', bool $stripTags = false) : bool
    {
        $sql = "";
        $sql_parts = [];
        $params = [];
        foreach ($data as $key => $value) {
            $params[":".$key] = $stripTags ? strip_tags($value) : $value;
            $sql_parts[] = "`$key`=:$key";
        }
        $sql = "UPDATE `$table` SET ".implode(",", $sql_parts)." WHERE `$recordIdColumn`=$recordId";
        return $this->query($sql, $params);
    }

    /**
     * delete a record from a table
     * @param string $table table name
     * @param int $recordId id of the record
     * @param string $recordIdColumn (optional) name of the id column
     * @return bool true on success
     */
    public function delete(string $table, int $recordId, string $recordIdColumn = "id") : bool
    {
        $sql = "DELETE FROM `$table` WHERE `$recordIdColumn`=$recordId";
        return $this->query($sql);
    }

    /**
     * select one or more records from a table
     * @param string $table table name
     * @param array $arguments (optional) column => value pair assoc array for select arguments
     * @param array $columns (optional) column names for the select
     * @return bool true on success
     */
    public function select(string $table, array $arguments = [], array $columns = []) : bool
    {
        $sql = "";
        $params = [];
        $sql_columns = [];
        $sql_arguments = [];
        if ($columns)
            foreach($columns as $column)
                $sql_columns[] = "`$column`";
        else $sql_columns[] = "*";
        if ($arguments)
            foreach($arguments as $column => $value) {
                $params[":".$column] = $value;
                $sql_arguments[] = "`$column`=:$column";
            }
        else $sql_arguments[] = "1";
        $sql = "SELECT ".implode(",", $sql_columns)." FROM `$table` WHERE ".implode(" AND ", $sql_arguments);
        return $this->query($sql, $params);
    }

    /**
     * select one or more records from a table and directly fetch to assoc array
     * @param string $table table name
     * @param array $arguments (optional) column => value pair assoc array for select arguments
     * @param array $columns (optional) column names for the select
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function selectFetchAll(string $table, array $arguments = [], array $columns = [], bool $htmlspecialchars = false)
    {
        $this->select($table, $arguments, $columns);
        return $this->fetchAll($htmlspecialchars);
    }

    /**
     * select one or more records from a table and directly fetch to array of objects
     * @param string $table table name
     * @param array $arguments (optional) column => value pair assoc array for select arguments
     * @param array $columns (optional) column names for the select
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function selectFetchAllObject(string $table, array $arguments = [], array $columns = [], bool $htmlspecialchars = false)
    {
        $this->select($table, $arguments, $columns);
        return $this->fetchAllObject($htmlspecialchars);
    }
}
