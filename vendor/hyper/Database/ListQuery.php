<?php


namespace Hyper\Database;


use Hyper\Models\Pagination;
use PDO;

/**
 * Trait ListQuery
 * @package Hyper\Database
 */
trait ListQuery
{
    use Query;

    /** @var array */
    private $list = null;

    /**
     * SELECT * query with limit and offset params
     *
     * @param int|null $limit
     * @param int|null $offset
     * @return ListQuery
     */
    public function select($limit = null, $offset = null)
    {
        if (!isset($this->list) || empty($this->list)) {
            $isLimited = !isset($limit) ? "" : "LIMIT $limit";
            $isOffset = !isset($offset) ? "" : "OFFSET $offset";
            $stmt = $this->query("SELECT * FROM `$this->tableName` WHERE `deletedAt` is NULL $isLimited $isOffset", [],
                true);
            if ($stmt->setFetchMode(PDO::FETCH_ASSOC)) {
                $arr = [];
                foreach ($stmt->fetchAll() as $key => $value) {
                    array_push($arr, $this->attachForeignEntities($value));
                }
                $this->list = $arr;
            }
        }
        return $this;
    }

    /**
     * Paginate a result
     * @param int $page
     * @param int $perPage
     * @return Pagination
     */
    public function paginate($page = 1, $perPage = 20): Pagination
    {
        if (empty($this->list))
            $this->select();

        return new Pagination($this->list, $page, $perPage);

    }

    /**
     * Take limited results from the database or available list
     *
     * @param int $limit
     * @return FilterQuery|ListQuery
     */
    public function take(int $limit)
    {
        if (!isset($this->list)) {
            $this->select($limit);
        } else {
            $this->list = array_splice($this->list, $limit);
        }
        return $this;
    }

    /**
     * Get the list from multi-select executions such as select, where, search etc.
     * @return array
     */
    public function toList(): array
    {
        return $this->list;
    }
}