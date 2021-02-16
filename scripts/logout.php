<?php
session_start();
include_once "./config.php";
//When this script is ran, the user is logged out from the application and any login cookie is removed. 

//Delete session from database
$sql = "DELETE FROM session WHERE token=:token";
$ps = $db->prepare($sql);
$ps->bindValue(":token", $_SESSION['token']);
$ps->execute();

//Remove the login cookie
//This is done by settings it's expiration date in the past.
setcookie("keep-between-sessions", "", time() - 1);

//Unset the value in $_SESSION
//Sometimes the value in Session can be kept, but setting it to an empty string removes any such possibility
$_SESSION['token'] = "";
unset($_SESSION['token']);

//Finally, redirect to the login page
header("Location: ../index.php");
exit;
