<?php

namespace ORQ;

class Database extends Connected
{
    /** @var string */
    protected $name;

    /**
     * Database constructor.
     * @param Connection $connection
     * @param string $name
     */
    public function __construct(Connection $connection, string $name)
    {
        parent::__construct($connection);

        $this->name = $name;
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
     * @return Skeleton[]
     */
    public function skeletons()
    {
        foreach($this->pdo()->query('SHOW TABLES FROM '.$this->getNameSafe())->fetchAll(\PDO::FETCH_COLUMN) as $name)
        {
            yield new Skeleton($this->getConnection(), $name, $this->getName());
        }
    }
}
