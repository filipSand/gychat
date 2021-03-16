<?php
include_once "scripts/config.php";
include_once "scripts/functions.php";
session_start();

$userID = checkLogin();



$message1 = "";
$message2 = "";

//Password change
if (isset($_POST['change-password'])) {
    //Verify that the current password is accurate.
    $sql = "SELECT password_hash FROM user WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $userID);
    $ps->execute();
    $row = $ps->fetch();

    if (password_verify($_POST['current-password'], $row['password_hash'])) {
        //Check that the passwords match!
        if ($_POST['new-password'] == $_POST['confirm-new-password']) {
            //Check that the password matches the requirements
            if (preg_match("/(?=^.{12,}$)((?=.*\w)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]))^.*/", $_POST['password'])) {
                $sql = "UPDATE user SET password_hash=:password_hash WHERE id=:id";
                $ps = $db->prepare($sql);
                $ps->bindValue(":password_hash", password_hash($_POST['new-password'], PASSWORD_DEFAULT));
                $ps->bindValue(":id", $userID);
                $ps->execute();
                $message1 = "Lösenordet är bytt!";
            } else {
                $message1 = userErrorCodes(7);
            }
        } else {
            $message1 = userErrorCodes(5);
        }
    } else {
        $message1 = userErrorCodes(8);
    }
}

if (isset($_POST['new-name'])) {
    //Set the new friendly name
    $sql = "UPDATE user SET friendly_name=:friendly_name WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $userID);
    $ps->bindValue(":friendly_name", $_POST['new-name']);
    $ps->execute();
}

if (isset($_POST['delete-password'])) {
    //The user has entered a password and wants to delete their account.
    //First, verify the password
    $sql = "SELECT password_hash FROM user WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $userID);
    $ps->execute();
    $pswd = $ps->fetchColumn();

    if (password_verify($_POST['delete-password'], $pswd)) {
        //Password correct, delete the account.
        $sql = "DELETE FROM user WHERE id=:id";
        $ps = $db->prepare($sql);
        $ps->bindValue(":id", $userID);
        $ps->execute();
        //Delete any cookie that might exist
        setcookie("keep_between_sessions", "", 1);
        session_unset();
        //Redirect to index.php
        header("Location: index.php");
        exit;
    } else {
        $message2 = userErrorCodes(8);
    }
}

$sql = "SELECT friendly_name FROM user WHERE id=:id";
$ps = $db->prepare($sql);
$ps->bindValue(":id", $userID);
$ps->execute();
$friendlyName = $ps->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- From Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style/external.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/chat.css">
    <link rel="stylesheet" href="style/settings.css">
    <script src="scripts/functions.js"></script>
    <script src="scripts/chat.js" defer></script>
    <title>Inställningar - Gymnasiearbete</title>
</head>

<body>
    <header class="chat-window-header">
        <button id="menu" class="header-button"><img src="graphics/menuicon.svg" alt="Öppna meny" class="button-image"></button>

        <div class="current-chat">
            <h2 class="current-chat-friendly-name">Inställningar</h2>
            <h3 class="current-chat-username">Administrera ditt konto </h3>
        </div>

    </header>
    <div id="blackbox"></div>
    <nav class="left-menu" id="left-menu-a">
        <ul class="left-menu-list conversation-list">
            <?php
            generateSideMenu($userID);
            ?>
        </ul>
        <ul class="left-menu-list left-menu-settings">
            <li class="left-menu-setting">
                <a href="./newconversation.php" class="left-menu-link"><img src="graphics/plusicon.svg" alt="Lägg till" class="button-image left-menu-setting-image">Ny chatt</a>
            </li>
            <li class="left-menu-setting">
                <a href="#" class="left-menu-link"><img src="graphics/settingsicon.svg" alt="Inställningar" class="button-image left-menu-setting-image">Inställningar</a>
            </li>
            <li class="left-menu-setting">
                <a href="./scripts/logout.php" class="left-menu-link"><img src="graphics/exitsymbol.svg" alt="Logga ut" class="button-image left-menu-setting-image">
                    Logga ut
                </a>
            </li>
        </ul>
    </nav>
    <main>
        <h3>Byte av lösenord</h3>
        <form class="setting-form" id="change-password" method="POST">
            <label for="current-password" id="current-password-label">Nuvarande lösenord</label>
            <input type="password" name="current-password" id="current-password-field" required>
            <p id="password-req-text">Krav på lösenord: Minst 12 tecken. Minst en stor och en liten bokstav. Minst en siffra.</p>
            <p><label for="new-password" id="new-password-label">Nytt lösenord</label>
                <input type="password" name="new-password" id="new-password-field" required>
            </p>
            <p><label for="confirm-new-password" id="confirm-new-password-label">Bekräfta nytt lösenord</label>
                <input type="password" name="confirm-new-password" id="confirm-new-password-field" required>
            </p>
            <button id="change-button" name="change-password">Byt lösenord</button>
            <p><?= $message1 ?> </p>
        </form>
        <h3>Byt namn</h3>
        <form method="POST">
            <p>Nuvarande namn: <?= htmlspecialchars($friendlyName) ?></p>
            <label for="new-name">Nytt namn</label>
            <input type="text" name="new-name" id="new-name" required>
            <button type="submit">Byt namn</button>
        </form>
        <h3>Radera konto</h3>
        <form method="POST">
            <label for="delete-password">Ditt lösenord</label>
            <input type="password" name="delete-password" id="delete-password" required>
            <button type="submit">Radera kontot</button>
            <p>Detta raderar alla konversationer och meddelanden och kan INTE ångras.</p>
            <p><?= $message2 ?></p>
        </form>
        <h3>Attribution</h3>
        <a href="attribution.txt" target="_blank">Länk till attribution för ikoner och extern kod.</a>
    </main>

</body>

</html>