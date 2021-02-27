<?php
session_start();
include_once "scripts/config.php";
include_once "scripts/functions.php";
$userID = checkLogin();
$message = "";


//Check, validate and create new conversation for submitted username
if (isset($_POST['new-user'])) {
    //Look up the user and see if they exist
    $sql = "SELECT * FROM user WHERE name=:name";
    $ps = $db->prepare($sql);
    $ps->bindValue(":name", $_POST['new-user']);
    $ps->execute();

    if ($ps->rowCount() == 1) {
        //User found, create and redirect to conversation.
        $user2Details = $ps->fetch();
        $sql = "INSERT INTO conversation (id, user1_id, user2_id, user1_block, user2_block) VALUES (:id, :user1_id, :user2_id, 0, 0)";
        $ps = $db->prepare($sql);
        //Generate a random candidate for an ID
        $candidate = bin2hex(random_bytes(4));
        $ps->bindValue(":id", $candidate);
        $ps->bindValue(":user1_id", $userID);
        $ps->bindValue(":user2_id", $user2Details['id']);
        $ps->execute();

        $errorInfo = $ps->errorInfo();
        if ($errorInfo[0] == "00000") {
            //Executed succesfully! Redirect
            header("Location: chat.php?conversation={$candidate}");
            exit;
        } else {
            $message = userErrorCodes(1);
        }
    } else {
        $message = userErrorCodes(6);
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
    <link rel="stylesheet" href="style/chat.css">
    <script src="scripts/functions.js"></script>
    <script src="scripts/chat.js" defer></script>
    <title>Ny konversation - Gymnasiearbete</title>
</head>

<body>
    <header class="chat-window-header">
        <button id="menu" class="header-button"><img src="graphics/menuicon.svg" alt="Öppna meny" class="button-image"></button>

        <div class="current-chat">
            <h2 class="current-chat-friendly-name">Skapa ny konversation</h2>
            <h3 class="current-chat-username">Sök på @användarnamn</h3>
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
                <a href="#" class="left-menu-link"><img src="graphics/plusicon.svg" alt="Lägg till" class="button-image left-menu-setting-image">Ny chatt</a>
            </li>
            <li class="left-menu-setting">
                <a href="#" class="left-menu-link"><img src="graphics/settingsicon.svg" alt="Inställningar" class="button-image left-menu-setting-image">Inställningar</a>
            </li>
            <li class="left-menu-setting">
                <a href="#" class="left-menu-link"><img src="graphics/exitsymbol.svg" alt="Logga ut" class="button-image left-menu-setting-image">
                    Logga ut
                </a>
            </li>
        </ul>
    </nav>
    <footer>
        <form method="post" class="message-send">
            <p><?= $message ?></p>
            <label for="new-message-text">Skriv användarnamn</label>
            <input type="text" name="new-user" id="new-message-text">
            <button id="message-send"><img src="graphics/plusicon.svg" alt="Sök" class="button-image"></button>
        </form>
    </footer>
</body>

</html>