<?php
declare(strict_types=1);
namespace Dduers\PDOXpress;

use PDO;

/**
 * pdo-xpress connection class
 */
class PDOXpressConnection extends PDO
{
    /**
     * the pdo statement
     */
    private $statement;

    /**
     * pdo constructor
     * more information at https://www.php.net/manual/de/pdo.construct.php
     */
    public function __construct(
        string $dsn,
        string $username = "",
        string $passwd = "",
        array $options = []
    )
    {
        parent::__construct($dsn, $username, $passwd, $options);
        if (!count($options)) {
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * get the name of the database driver
     * @return string database driver name
     */
    public function getDriverName(): string
    {
        return $this->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * execute an sql query
     * @param string $sql the sql query
     * @param array (optional) $params params, if the sql query contains placeholders
     * @param int (optional) $attrCase the case of the column names in the result, will be reset after the query
     * @return bool true on success
     */
    public function execQuery(
        string $sql,
        array $params = [],
        int $attrCase = PDO::CASE_NATURAL
    ) : bool
    {
        $backupAttrCase = $this->getAttribute(PDO::ATTR_CASE);
        $this->setAttribute(PDO::ATTR_CASE, $attrCase);
        $this->statement = $this->prepare($sql);
        if (false === $this->statement)
            return false;
        $result = $this->statement->execute($params);
        $this->setAttribute(PDO::ATTR_CASE, $backupAttrCase);
        return $result;
    }

    /**
     * fetch one record from a select query as assoc array
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function fetch(bool $htmlspecialchars = false)
    {
        if (!$this->statement || !($record = $this->statement->fetch(PDO::FETCH_ASSOC)))
            return NULL;
        if (true === $htmlspecialchars)
            foreach ($record as $column => $content)
                $record[$column] =
                    is_null($content) || is_numeric($content)
                        ? $content
                        : htmlspecialchars($content, ENT_QUOTES);
        return $record;
    }

    /**
     * fetch all records from a select query at once as assoc array
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Array|NULL
     */
    public function fetchAll(bool $htmlspecialchars = false)
    {
        if (!$this->statement || !($result = $this->statement->fetchAll(PDO::FETCH_ASSOC)))
            return NULL;
        if (true === $htmlspecialchars)
            foreach ($result as $key => $record)
                foreach ($record as $column => $content)
                    $result[$key][$column] =
                        is_null($content) || is_numeric($content)
                            ? $content
                            : htmlspecialchars($content, ENT_QUOTES);
        return $result;
    }

    /**
     * fetch one record from a select query as object
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Object|NULL
     */
    public function fetchObject(bool $htmlspecialchars = false)
    {
        if (!$this->statement || !($record = $this->statement->fetch(PDO::FETCH_OBJ)))
            return NULL;
        if (true === $htmlspecialchars)
            foreach ($record as $column => $content)
                $record->$column =
                    is_null($content) || is_numeric($content)
                        ? $content
                        : htmlspecialchars($content, ENT_QUOTES);
        return $record;
    }

    /**
     * fetch all records from a select query at once as array of objects
     * @param bool (optional) $htmlspecialchars set true to encode htmlspecialchars on non numeric values
     * @return Object|NULL
     */
    public function fetchAllObject(bool $htmlspecialchars = false)
    {
        if (!$this->statement || !($result = $this->statement->fetchAll(PDO::FETCH_OBJ)))
            return NULL;
        if (true === $htmlspecialchars)
            foreach ($result as $key => $record)
                foreach ($record as $column => $content)
                    $result[$key]->$column =
                        is_null($content) || is_numeric($content)
                            ? $content
                            : htmlspecialchars($content, ENT_QUOTES);
        return $result;
    }
}
