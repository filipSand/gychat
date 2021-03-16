<?php
include_once "config.php";
include_once "functions.php";
session_start();

$conversation = $_GET['id'];

$sql = "SELECT last_message_sent FROM conversation WHERE id=:id";
$ps = $db->prepare($sql);
$ps->bindValue("id", $conversation);
$ps->execute();
$lastMessageSent = $ps->fetchColumn();

if ($lastMessageSent > $_SESSION['lastUpdate']) {
    echo "1";
} else {
    echo "0";
}
