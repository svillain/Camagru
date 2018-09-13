<?php
include("database.php");

if (!file_exists('../userphoto')) {
    mkdir('../userphoto');
}

$DB_DNS = "mysql:host=localhost";
global $DB_USER;
global $DB_PASSWORD;
try {
    $pdobj = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
    $pdobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdobj->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
try {
    $query = file_get_contents("db.sql");
    $pdobj->exec($query);
}
catch (PDOException $e)
{
    echo $e->getMessage();
    die();
}
