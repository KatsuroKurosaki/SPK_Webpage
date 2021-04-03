<?php
namespace Session;

class SessionConf
{

    // Session configuration parameters for SessionDb class
    const SESSION_CONF = [
        "db" => "database", // MySQL DB
        "dbtable" => "php_session", // MySQL DB table
        "expires" => "1 DAY", // How long will a session be valid
        "sidlen" => 16, // Will produce 32 characters, change DB row length if this value is changed
        "debug" => false, // In case of an exception, we will use var_dump to show it
    ];

    // Session initialization parameters for session_start() (https://secure.php.net/manual/en/session.configuration.php
    const SESSION_OPTS = [
        "use_cookies" => 0, // No use cookies
        "use_only_cookies" => 0, // Force no use cookies
        "use_trans_sid" => 1, // Use custom $_GET parameter for session
        "name" => "s", // Name of the $_GET parameter
        "trans_sid_tags" => "", // No autoreplace any HTML tags
    ];
}
