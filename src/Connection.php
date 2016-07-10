<?php

namespace ORQ;

class Connection
{
    /** @var \PDO */
    protected $pdo;

    /**
     * Connection constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function pdo()
    {
        return $this->pdo;
    }

    /**
     * @return string|null
     */
    public function databaseString()
    {
        return $this->pdo()->query('SELECT DATABASE()')->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * @param string|null $name
     * @return null|Database
     */
    public function database(string $name = null)
    {
        return ($name OR $name = $this->databaseString()) ? new Database($this, $name) : null;
    }

    /**
     * @return Database[]
     */
    public function databases()
    {
        foreach($this->pdo()->query('SHOW DATABASES')->fetchAll(\PDO::FETCH_COLUMN) as $name)
        {
            yield new Database($this, $name);
        }
    }

    // ==============================================

    /** @var self */
    protected static $instance = [];
    /** @var array */
    protected static $register = [];

    /**
     * @param \PDO $instance
     * @param string $key
     * @return self
     */
    public static function setPDO(\PDO $instance, string $key = '__default')
    {
        return self::$instance[$key] = new self($instance);
    }

    /**
     * @param array $registry
     * @param string $key
     */
    public static function setRegistry(array $registry, string $key = '__default')
    {
        if (!isset($registry['dsn']))
            throw new \UnderflowException('Missing DSN on the connection array');

        self::$register[$key] = $registry;
    }

    /**
     * @param string $key
     * @return self
     * @throws \OutOfBoundsException
     */
    public static function get(string $key = '__default')
    {
        if (isset(self::$instance[$key]))
            return self::$instance[$key];

        if (isset(self::$register[$key]))
        {
            return self::$instance[$key] = new self(new \PDO(
                self::$register[$key]['dsn'],
                self::$register[$key]['username'] ?? '',
                self::$register[$key]['password'] ?? '',
                self::$register[$key]['options'] ?? []
            ));
        }

        throw new \OutOfBoundsException('Undefined key "'.$key.'"');
    }
}
