<?php
session_start();
include_once "scripts/config.php";
include_once "scripts/functions.php";

$message = "";

if (doesValidLoginCookieExist()) {
    //If a valid cookie exists, redirect to chat.php and let that script handle verification.
    header("Location: chat.php");
    exit;
}

if (isset($_POST['username'])) {
    $sql = "SELECT * FROM user WHERE name=:username";
    $ps = $db->prepare($sql);
    $ps->bindValue(":username", $_POST['username']);
    $ps->execute();

    var_dump($ps->errorInfo());

    if ($ps->rowCount() == 1) {
        $info = $ps->fetch();
        $passwordHash = $info['password_hash'];
        $passwordProvided = $_POST['password'];

        if (password_verify($passwordProvided, $passwordHash)) {
            $message = userErrorCodes(0);
            logInUser($info['id'], true);
            exit;
        } else {
            $message = userErrorCodes(2);
        }
    } else if ($ps->rowCount() == 0) {
        $message = userErrorCodes(2);
    } else {
        $message = userErrorCodes(1);
    }
}

?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- From Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style/external.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/login.css">
    <script src="scripts/functions.js"></script>
    <script src="scripts/chat.js" defer></script>
    <title>Chatt - Gymnasiearbete</title>
</head>

<body>
    <h1>Chattapplikation</h1>
    <h2>Gymnasiearbete Filip Sandström</h2>
    <h3>Logga in</h3>
    <form action="" method="POST" class="login-page-form">
        <label for="username" id="username-label">Användarnamn</label>
        <input type="text" id="username-field" name="username" class="login-field" required>
        <label for="password" id="password-label">Lösenord</label>
        <input type="password" id="password-field" name="password" class="login-field" placeholder="Minst 12 tecken" required>
        <button type="submit" id="login-button">Logga in</button>
        <p id="login-message"><?= $message ?></p>
    </form>
    <h3><a href="signup.php">Ny användare</a></h3>


</body>

</html>