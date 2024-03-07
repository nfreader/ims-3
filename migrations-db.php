<?php

use Doctrine\DBAL\Connection;

// require_once(__DIR__.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."bootstrap.php");
// var_dump($app);

return (require_once __DIR__.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."bootstrap.php")->getContainer()->get(Connection::class);
