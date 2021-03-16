<?php
include_once "config.php";
include_once "functions.php";

$conversation = $_GET['id'];

$sql = "SELECT last_message_sent FROM conversation WHERE id=:id";
$ps = $db->prepare($sql);
$ps->bindValue("id", $conversation);
$ps->execute();
