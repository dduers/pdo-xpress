<?php
declare(strict_types=1);
namespace Dduers\PDOMySql;

class PDOMySql extends \PDO {

    private $statement;

    public function __construct(string $connection, string $user = '', string $password = '', array $options = [])
    {
        parent::__construct($connection, $user, $password);
        if (empty($options)) {
            $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    public function query(string $sql, array $params = [])
    {
        $this->statement = $this->prepare($sql);
        return $this->statement->execute($params);
    }

    public function fetch()
	{
        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll()
	{
        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchObject()
    {
        return $this->statement->fetch(\PDO::FETCH_OBJ);
    }

    public function fetchAllObject()
    {
        return $this->statement->fetchAll(\PDO::FETCH_OBJ);
    }

    public function insert(string $table, array $data)
    {
        $sql = "";
        $params = [];
        foreach ($data as $key => $value)
            $params[":".$key] = $value;
        $sql = "INSERT INTO `$table` (".implode(',', array_keys($data)).") VALUES (".implode(',', array_keys($params)).")";
        return $this->query($sql, $params);
    }

    /**
     * update a table record by record id with new data
     * @param string $table table name
     * @param array $data new data for the record
     * @param int $recordId id of the record
     * @param string $recordIdColumn (optional) name of the id column
     */
    public function update(string $table, array $data, int $recordId, string $recordIdColumn = 'id')
    {
        $sql = '';
        $sql_parts = [];
        $params = [];
        foreach ($data as $key => $value) {
            $params[':'.$key] = $value;
            $sql_parts[] = "`$key`=:$key";
        }
        $sql = "UPDATE `$table` SET ".implode(',', $sql_parts)." WHERE `$recordIdColumn`=$recordId";
        return $this->query($sql, $params);
    }

    /**
     * delete a record from a table
     * @param string $table table name
     * @param int $recordId id of the record
     * @param string $recordIdColumn (optional) name of the id column
     */
    public function delete(string $table, int $recordId, string $recordIdColumn = 'id')
    {
        $sql = "DELETE FROM `$table` WHERE `$recordIdColumn`=$recordId";
        return $this->query($sql);
    }

    public function select(string $table, array $arguments = [], array $columns = []) {
        $sql = '';
        $params = [];
        $sql_columns = [];
        $sql_arguments = [];
        if ($columns)
            foreach($columns as $column)
                $sql_columns[] = "`$column`";
        else $sql_columns[] = '*';
        if ($arguments)
            foreach($arguments as $column => $value) {
                $params[':'.$column] = $value;
                $sql_arguments[] = "`$column`=:$column";
            }
        else $sql_arguments[] = '1';
        $sql = "SELECT ".implode(',', $sql_columns)." FROM `$table` WHERE ".implode(' AND ', $sql_arguments);
        return $this->query($sql, $params);
    }
}
