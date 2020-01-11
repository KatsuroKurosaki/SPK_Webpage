<?php
namespace Session;

class Manage
{

    public static function start($sessionConf = [], $sessionOpts = [])
    {
        if (session_status() == PHP_SESSION_NONE) {
            \session_set_save_handler(new SessionDb($sessionConf), true);

            $_sessionOpts = array_replace_recursive(SessionConf::SESSION_OPTS, $sessionOpts);
            \session_start($_sessionOpts);
        }
    }

    public static function destroy()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            \session_destroy();
        }
    }

    public static function isValid($headUnauth = false)
    {
        if (empty($_SESSION)) {
            if ($headUnauth) {
                header("HTTP/1.1 401 Unauthorized");
            }

            Manage::destroy();
            return false;
        }
        return true;
    }

    public static function cleanup()
    { // PHP 7.1.0
        if (session_status() == PHP_SESSION_ACTIVE) {
            return \session_gc();
        }
    }
}
