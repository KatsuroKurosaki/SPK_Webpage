<?php
namespace MySQLDb;

class DbErrorConnection extends \Exception
{

    private $conn = null;
    private $sql = "";

    public function __construct(\Mysqli $conn, string $sql)
    {
        $this->conn = $conn;
        $this->sql = $sql;
        parent::__construct($conn->error, $conn->errno);
    }

    public function __desctruct()
    {
        unset($this->conn);
        unset($this->sql);
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function __toString()
    {
        return get_class($this) . "\nMessage: " . $this->message . "\nFile: " . $this->file . "::" . $this->line . "\n\n" . $this->getTraceAsString();
    }
}
