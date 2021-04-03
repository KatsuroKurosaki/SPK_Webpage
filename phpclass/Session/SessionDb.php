<?php
namespace Session;

class SessionDb implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{

    private $_sessionConf;

    // Constructor. I like this array procedure like jQuery plugins.
    public function __construct($sessionConf = [])
    {
        $this->_sessionConf = array_replace_recursive(
            SessionConf::SESSION_CONF,
            $sessionConf
        );
    }

    // return value should be true for success or false for failure
    public function close()
    {
        // DB Connections are persistent
        return true;
    }

    // return value should be true for success or false for failure
    public function destroy($session_id)
    {
        try {
            \Db\DbConnection::execute(
                "DELETE FROM `" . $this->_sessionConf['db'] . "`.`" . $this->_sessionConf['dbtable'] . "`
				WHERE `id` = ?;",
                's',
                [$session_id]
            );
            return true;
        } catch (\Db\DbErrorConnection $e) {
            if ($this->_sessionConf['debug']) {
                var_dump($e);
            }
            return false;
        }
    }

    // return value should be true for success or false for failure
    public function gc($maxlifetime)
    {
        try {
            \Db\DbConnection::execute(
                "DELETE FROM `" . $this->_sessionConf['db'] . "`.`" . $this->_sessionConf['dbtable'] . "`
				WHERE `expires` < UNIX_TIMESTAMP(NOW());"
            );
            return true;
        } catch (\Db\DbErrorConnection $e) {
            if ($this->_sessionConf['debug']) {
                var_dump($e);
            }
            return false;
        }
    }

    // return value should be true for success or false for failure
    public function open($save_path, $session_name)
    {
        try {
            \Db\DbConnection::execute(
                "SELECT true
				FROM `" . $this->_sessionConf['db'] . "`.`" . $this->_sessionConf['dbtable'] . "`
				LIMIT 1;"
            );
            return true;
        } catch (\Db\DbErrorConnection $e) {
            if ($this->_sessionConf['debug']) {
                var_dump($e);
            }
            return false;
        }
    }

    // return value should be the session data or an empty string
    public function read($session_id)
    {
        try {
            $data = \Db\DbConnection::execute(
                "SELECT `session_data`
				FROM `" . $this->_sessionConf['db'] . "`.`" . $this->_sessionConf['dbtable'] . "`
				WHERE `id` = ? AND `expires` > UNIX_TIMESTAMP(NOW());",
                's',
                [$session_id]
            )->getSingleData();

            if ($data != null) {
                \Db\DbConnection::execute(
                    "UPDATE `" . $this->_sessionConf['db'] . "`.`" . $this->_sessionConf['dbtable'] . "`
					SET `expires` = UNIX_TIMESTAMP(DATE_ADD(NOW(),INTERVAL " . $this->_sessionConf['expires'] . "))
					WHERE `id` = ?;",
                    's',
                    [$session_id]
                );
                return $data['session_data'];
            }
        } catch (\Db\DbErrorConnection $e) {
            if ($this->_sessionConf['debug']) {
                var_dump($e);
            }
        }
        return "";
    }

    // return value should be true for success or false for failure
    public function write($session_id, $session_data)
    {
        try {
            \Db\DbConnection::execute(
                "INSERT INTO `" . $this->_sessionConf['db'] . "`.`" . $this->_sessionConf['dbtable'] . "` (`id`, `session_data`, `expires`,`ip_address`,`user_agent`)
				VALUES (?,?,UNIX_TIMESTAMP(DATE_ADD(NOW(),INTERVAL " . $this->_sessionConf['expires'] . ")),INET_ATON(?),SUBSTRING(?,1,255))
				ON DUPLICATE KEY UPDATE `session_data` = ?, `expires` = UNIX_TIMESTAMP(DATE_ADD(NOW(),INTERVAL " . $this->_sessionConf['expires'] . "));",
                'sssss',
                [$session_id, $session_data, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $session_data]
            );
            return true;
        } catch (\Db\DbErrorStatement $e) {
            if ($this->_sessionConf['debug']) {
                var_dump($e);
            }
            return false;
        }
    }

    // invoked internally when a new session id is needed
    public function create_sid()
    {
        return bin2hex(random_bytes($this->_sessionConf['sidlen']));
    }

    // return value should be true if the session id is valid otherwise false
    public function validateId($session_id)
    {
        // WIP
        return true;
    }

    // return value should be true for success or false for failure
    public function updateTimestamp($session_id, $session_data)
    {
        // WIP
        return true;
    }
}
