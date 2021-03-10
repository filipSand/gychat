<?php
include_once "scripts/config.php";
include_once "scripts/functions.php";
session_start();

$userID = checkLogin();
//TODO Make this page

$message = "";

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
                $message = "Lösenordet är bytt!";
            } else {
                $message = userErrorCodes(7);
            }
        } else {
            $message = userErrorCodes(5);
        }
    } else {
        $message = userErrorCodes(5);
    }
}



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
            <p id="password-req-label">Krav på lösenord</p>
            <p id="password-req-text">Minst 12 tecken. Minst en stor och en liten bokstav. Minst en siffra.</p>
            <label for="new-password" id="new-password-label">Nytt lösenord</label>
            <input type="password" name="new-password" id="new-password-field" required>
            <label for="confirm-new-password" id="confirm-new-password-label">Bekräfta nytt lösenord</label>
            <input type="password" name="confirm-new-password" id="confirm-new-password-field" required>
            <button id="change-button" name="change-password">Byt lösenord</button>
            <p><?= $message ?> </p>
        </form>

    </main>

</body>

</html>