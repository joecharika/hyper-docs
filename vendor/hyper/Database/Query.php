<?php


namespace Hyper\Database;


use Exception;
use Hyper\Exception\HyperException;
use Hyper\Functions\Debug;
use PDOStatement;

trait Query
{
    use Database;

    /**
     * Run a prepared query on the PDO database object
     * @param string $query The query to run
     * @param array $params Parameters to bind if the statement was a template sql
     * @param bool $eagerResult If you want to get the statement object instead returns true if the statement was executed successfully. See PDOStatement::execute
     * @return bool|PDOStatement
     */
    private function query($query, array $params = [], $eagerResult = false)
    {
        try {
            $statement = $this->db->prepare("$query");
            $result = $statement->execute($params);
            return $eagerResult ? $statement : $result;
        } catch (Exception $e) {
            (new HyperException)->throw($e->getMessage());
        }
        return false;
    }

}