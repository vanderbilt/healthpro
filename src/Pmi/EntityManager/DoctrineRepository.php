<?php
namespace Pmi\EntityManager;

class DoctrineRepository
{
    protected $dbal;
    protected $entity;

    public function __construct($dbal, $entity)
    {
        $this->dbal = $dbal;
        $this->entity = $entity;
    }

    public function fetchOneBy(array $where)
    {
        $parameters = [];
        $columns = [];
        foreach ($where as $column => $value) {
            $parameters[] = $value;
            $columns[] = "`{$column}` = ?";
        }
        $query = "SELECT * FROM `{$this->entity}` WHERE " . join(' AND ', $columns);
        return $this->dbal->fetchAssoc($query, $parameters);
    }

    public function fetchBy(array $where, array $order = [], $limit = null)
    {
        $query = "SELECT * FROM `{$this->entity}`";
        $parameters = [];
        if (!empty($where)) {
            $columns = [];
            foreach ($where as $column => $value) {
                $parameters[] = $value;
                $columns[] = "`{$column}` = ?";
            }
            $query .= ' WHERE ' . join(' AND ', $columns);
        }
        if (!empty($order)) {
            $sorts = [];
            foreach ($order as $column => $direction) {
                $sorts[] = "`{$column}` {$direction}";
            }
            $query .= ' ORDER BY ' . join(', ', $sorts);
        }
        if ($limit) {
            $query .= ' LIMIT ' . (int)$limit;
        }
        return $this->dbal->fetchAll($query, $parameters);
    }

    public function fetchBySql($where, array $parameters = [], array $order = [], $limit = null)
    {
        $query = "SELECT * FROM `{$this->entity}`";
        $query .= ' WHERE ' . $where;
        if (!empty($order)) {
            $sorts = [];
            foreach ($order as $column => $direction) {
                $sorts[] = "`{$column}` {$direction}";
            }
            $query .= ' ORDER BY ' . join(', ', $sorts);
        }
        if ($limit) {
            $query .= ' LIMIT ' . (int)$limit;
        }
        return $this->dbal->fetchAll($query, $parameters);
    }

    public function insert($data)
    {
        $success = $this->dbal->insert($this->entity, $data);
        if ($success) {
            return $this->dbal->lastInsertId();
        } else {
            return false;
        }
    }

    public function update($id, $data)
    {
        return $this->dbal->update(
            $this->entity,
            $data,
            ['id' => $id]
        );
    }

    public function delete($id)
    {
        return $this->dbal->delete(
            $this->entity,
            ['id' => $id]
        );
    }
}
