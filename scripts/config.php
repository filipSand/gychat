<?php
$dbprefix = "";
$username = "root";
$password = "";

$db = new PDO(
    "mysql:host=localhost;dbname={$dbprefix}gychat;charset=utf8",
    $username,
    $password
);
