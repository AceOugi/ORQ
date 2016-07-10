<?php

namespace ORQ;

class Skeleton extends Connected
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $name_database;

    /**
     * Skeleton constructor.
     * @param Connection $connection
     * @param string $name
     * @param string|null $name_database
     */
    public function __construct(Connection $connection, string $name, string $name_database = null)
    {
        parent::__construct($connection);

        $this->name = $name;
        $this->name_database = $name_database;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameSafe()
    {
        return '`'.$this->name.'`';
    }

    /**
     * @return string
     */
    public function getFullNameSafe()
    {
        return ($this->name_database) ? '`'.$this->name_database.'`.'.$this->getNameSafe() : $this->getNameSafe();
    }

    /**
     * @return array[]
     */
    public function elements()
    {
        foreach($this->pdo()->query('SHOW COLUMNS FROM '.$this->getFullNameSafe())->fetchAll() as $data)
        {
            yield $data;
        }
    }

    /**
     * @return array[]
     */
    public function indexes()
    {
        foreach($this->pdo()->query('SHOW INDEXES FROM '.$this->getFullNameSafe())->fetchAll() as $data)
        {
            yield $data;
        }
    }
}
