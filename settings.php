<?php
include_once "scripts/config.php";
include_once "scripts/functions.php";
session_start();

$userID = checkLogin();
//TODO Make this page



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
        <form class="setting-form" id="change-password" method="POST">
            <p><label for="current-password">Nuvarande lösenord</label></p>
            <input type="password" name="current-password" id="current-password" required>
            <p><label for="new-password">Nytt lösenord</label></p>
            <input type="password" name="new-password" id="new-password" required>
            <p><label for="confirm-new-password">Bekräfta nytt lösenord</label></p>
            <input type="password" name="confirm-new-password" id="confirm-new-password" required>
            <button type="submit">Bytt lösenord</button>
        </form>

    </main>

</body>

</html>