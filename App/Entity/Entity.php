<?php


namespace App\Entity;


abstract class Entity
{
    protected $id = 0;
    protected $created_at = '';
    protected $updated_at = '';
    protected $table = '';

    public function __construct()
    {
        if(empty($this->table)){
            $this->table = strtolower(get_called_class() . 's');
            $this->table = explode('\\', $this->table);
            $this->table = end($this->table);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int)$id;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }
}