<?php


namespace Hyper\SQL;


use Hyper\Functions\Arr;

/**
 * Class QueryBuilder
 * @package Hyper\SQL
 */
class QueryBuilder
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $params = [];

    public function __construct($start = '')
    {
        $this->setQuery($start);
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     * @return QueryBuilder
     */
    public function setQuery(string $query)
    {
        $this->query .= empty($query) ? $query : ((empty($this->query) ? '' : ' ') . $query);
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param $key
     * @param $value
     * @return QueryBuilder
     */
    public function setParams($key, $value): QueryBuilder
    {
        $this->params[$key] = $value;
        return $this;
    }


    /**
     * Adds a column in an existing table
     * @param $name
     * @return QueryBuilder
     */
    function add($name = ''): QueryBuilder
    {
        return $this->setQuery("add $name");
    }

    /**
     * Adds a constraint after a table is already created
     * @param $constraint
     * @return QueryBuilder
     */
    function addConstraint($constraint): QueryBuilder
    {
        return $this->add()->constraint($constraint);
    }

    /**
     * Adds, deletes, or modifies columns in a table, or changes the data type of a column in a table
     * @param $name
     * @return QueryBuilder
     */
    function alter($name): QueryBuilder
    {
        return $this->setQuery("alter $name");
    }

    /**
     * Changes the data type of a column in a table
     * @param $column
     * @return QueryBuilder
     */
    function alterColumn($column): QueryBuilder
    {
        return $this->alter("column $column");
    }

    /**
     * Adds, deletes, or modifies columns in a table
     * @param $tableName
     * @return QueryBuilder
     */
    function alterTable($tableName)
    {
        return $this->alter("table $tableName");
    }


    /**
     * Renames a column or table with an alias
     * @param $alias
     * @return QueryBuilder
     */
    function as($alias): QueryBuilder
    {
        return $this->setQuery("as $alias");
    }


    /**
     * Sorts the result set in ascending order
     */
    function asc()
    {
        return $this->setQuery('asc');
    }


    /**
     * Creates different outputs based on conditions
     * @param $column
     * @param array $cases
     * @param $default
     * @return QueryBuilder
     */
    function case($column, array $cases, $default): QueryBuilder
    {
        $this->setQuery("(case $column");

        foreach ($cases as $key => $case) {
            $this->setQuery("when $case then $key");
        }

        return $this->setQuery("else $default end)");
    }


    /**
     * A constraint that limits the value that can be placed in a column
     * @param $column
     * @param $operator
     * @param $value
     * @return QueryBuilder
     */
    function check($column, $operator, $value): QueryBuilder
    {
        return $this->setQuery("check ($column $operator $value)");
    }


    /**
     * Changes the data type of a column or deletes a column in a table
     * @param $name
     * @return QueryBuilder
     */
    function column($name): QueryBuilder
    {
        return $this->setQuery("column $name");
    }


    /**
     * Adds or deletes a constraint
     * @param $constraint
     * @return QueryBuilder
     */
    function constraint($constraint): QueryBuilder
    {
        return $this->setQuery("constraint $constraint");
    }


    /**
     * Creates a database, index, view, table, or procedure
     * @param string $name
     * @return QueryBuilder
     */
    function create(string $name = ''): QueryBuilder
    {
        return $this->setQuery("create $name");
    }


    /**
     * Creates a new SQL database
     * @param $name
     * @return QueryBuilder
     */
    function createDatabase($name): QueryBuilder
    {
        return $this->create()->database($name);
    }


    /**
     * Creates an index on a table (allows duplicate values)
     * @param $name
     * @return QueryBuilder
     */
    function createIndex($name): QueryBuilder
    {
        return $this->create("index $name");
    }

    /**
     * Creates a new table in the database
     * @param $tableName
     * @param $definition
     * @return QueryBuilder
     */
    function createTable($tableName, $definition)
    {
        $tableName = \str_replace('`', '\'', $tableName);
        $this->create('table');
        return $this->setQuery("`$tableName`(" . Arr::spread($definition, true) . ")");
    }


    /**
     * Creates a stored procedure
     * @param $name
     * @param $procedure
     * @return QueryBuilder
     */
    function createProcedure($name, $procedure): QueryBuilder
    {
        return $this->create()->procedure($name, $procedure);
    }


    /**
     * Creates a unique index on a table (no duplicate values)
     */
    function createUniqueIndex($name, $columns)
    {
        return $this->create(SQLLogicalOperator::unique)->index($name);
    }


    /**
     * Creates a view based on the result set of a SELECT statement
     * @param $name
     * @param $select
     * @return QueryBuilder
     */
    function createView($name, $select): QueryBuilder
    {
        return $this->create("view $name as $select");
    }


    /**
     * Creates or deletes an SQL database
     */
    function database($name): QueryBuilder
    {
        return $this->setQuery("database `$name`");
    }


    /**
     * A constraint that provides a default value for a column
     * @param string $value
     * @return QueryBuilder
     */
    function default($value = '')
    {
        return $this->setQuery("default $value");
    }


    /**
     * Deletes rows from a table
     */
    function delete()
    {
    }


    /**
     * Sorts the result set in descending order
     */
    function desc()
    {
    }


    /**
     * Selects only distinct (different) values
     */
    function distinct()
    {
    }


    /**
     * Deletes a column, constraint, database, index, table, or view
     */
    function drop()
    {
    }


    /**
     * Deletes a column in a table
     */
    function dropColumn()
    {
    }


    /**
     * Deletes a UNIQUE, PRIMARY KEY, FOREIGN KEY, or CHECK constraint
     */
    function dropConstraint()
    {
    }


    /**
     * Deletes an existing SQL database
     */
    function dropDatabase()
    {
    }


    /**
     * Deletes a DEFAULT constraint
     */
    function dropDefault()
    {
    }


    /**
     * Deletes an index in a table
     */
    function dropIndex()
    {
    }


    /**
     * Deletes an existing table in the database
     */
    function dropTable()
    {
    }


    /**
     * Deletes a view
     */
    function dropView()
    {
    }


    /**
     * Executes a stored procedure
     */
    function exec()
    {
    }


    /**
     * Tests for the existence of any record in a subquery
     */
    function exists()
    {
    }


    /**
     * A constraint that is a key used to link two tables together
     */
    function foreignKey()
    {
    }


    /**
     * Specifies which table to select or delete data from
     */
    function from()
    {
    }


    /**
     * Returns all rows when there is a match in either left table or right table
     */
    function fullOuterJoin()
    {
    }


    /**
     * Groups the result set (used with aggregate functions: COUNT, MAX, MIN, SUM, AVG)
     */
    function groupBy()
    {
    }


    /**
     * Used instead of WHERE with aggregate functions
     */
    function having()
    {
    }


    /**
     * Allows you to specify multiple values in a WHERE clause
     */
    function in()
    {
    }


    /**
     * Creates or deletes an index in a table
     */
    function index($name)
    {
        return $this->setQuery("index $name");
    }


    /**
     * Returns rows that have matching values in both tables
     */
    function innerJoin()
    {
    }


    /**
     * Inserts new rows in a table
     */
    function insertInto()
    {
    }


    /**
     * Copies data from one table into another table
     */
    function insertIntoSelect()
    {
    }


    /**
     * Tests for empty values
     */
    function isNull()
    {
    }


    /**
     * Tests for non-empty values
     */
    function isNotNull()
    {
    }


    /**
     * Joins tables
     */
    function join()
    {
    }


    /**
     * Returns all rows from the left table, and the matching rows from the right table
     */
    function leftJoin()
    {
    }


    /**
     * Searches for a specified pattern in a column
     */
    function like()
    {
    }


    /**
     * Specifies the number of records to return in the result set
     */
    function limit()
    {
    }


    /**
     * Only includes rows where a condition is not true
     */
    function not()
    {
    }


    /**
     * A constraint that enforces a column to not accept NULL values
     */
    function notNull(): QueryBuilder
    {
        return $this->setQuery('not null');
    }


    /**
     * Includes rows where either condition is true
     */
    function or()
    {
    }


    /**
     * Sorts the result set in ascending or descending order
     */
    function orderBy()
    {
    }


    /**
     * Returns all rows when there is a match in either left table or right table
     */
    function outerJoin()
    {
    }


    /**
     * A constraint that uniquely identifies each record in a database table
     */
    function primaryKey(): QueryBuilder
    {
        return $this->setQuery('primary key');
    }

    /**
     * A constraint that uniquely identifies each record in a database table
     */
    function autoIncrement(): QueryBuilder
    {
        return $this->setQuery('auto_increment');
    }


    /**
     * A stored procedure
     * @param $name
     * @param $procedure
     * @return QueryBuilder
     */
    function procedure($name, $procedure): QueryBuilder
    {
        return $this->setQuery("$name $procedure");
    }


    /**
     * Returns all rows from the right table, and the matching rows from the left table
     */
    function rightJoin()
    {
    }


    /**
     * Specifies the number of records to return in the result set
     */
    function rowNum()
    {
    }


    /**
     * Selects data from a database
     */
    function select()
    {
    }


    /**
     * Selects only distinct (different) values
     */
    function selectDistinct()
    {
    }


    /**
     * Copies data from one table into a new table
     */
    function selectInto()
    {
    }


    /**
     * Specifies the number of records to return in the result set
     */
    function selectTop()
    {
    }


    /**
     * Specifies which columns and values that should be updated in a table
     */
    function set()
    {
    }


    /**
     * Creates a table, or adds, deletes, or modifies columns in a table, or deletes a table or data inside a table
     */
    function table()
    {
    }


    /**
     * Specifies the number of records to return in the result set
     */
    function top()
    {
    }


    /**
     * Deletes the data inside a table, but not the table itself
     */
    function truncateTable()
    {
    }


    /**
     * Combines the result set of two or more SELECT statements (only distinct values)
     */
    function union()
    {
    }


    /**
     * Combines the result set of two or more SELECT statements (allows duplicate values)
     */
    function unionAll()
    {
    }


    /**
     * A constraint that ensures that all values in a column are unique
     */
    function unique()
    {
    }


    /**
     * Updates existing rows in a table
     */
    function update()
    {
    }


    /**
     * Specifies the values of an INSERT INTO statement
     */
    function values()
    {
    }


    /**
     * Creates, updates, or deletes a view
     */
    function view()
    {
    }


    /**
     * Filters a result set to include only records that fulfill a specified condition
     */
    function where()
    {
    }

    public function __toString()
    {
        return $this->getQuery();
    }
}