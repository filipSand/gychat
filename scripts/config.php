<?php
$dbprefix = "filsan-9_";
$username = "filsan-9";
$password = "tuqi7Ed";

$db = new PDO(
    "mysql:host=localhost;dbname={$dbprefix}gychat;charset=utf8",
    $username,
    $password
);
