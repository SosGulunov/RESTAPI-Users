<?php

class DB
{
    // ДАННЫЕ ДЛЯ СОЕДИНЕНИЯ С БД
    private $host = DB_HOST;
    private $db   = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $charset = 'utf8';
    private $dsn;
    private $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    private static $instance = null;
    private $pdo;

    public function __construct()
    {
        $this->dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $this->opt);
    }

    public function getConnect()
    {
        return $this->pdo;
    }

    public function closeConnect()
    {
        $this->pdo = NULL;
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance->pdo;
    }
}
