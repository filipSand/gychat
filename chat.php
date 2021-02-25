<?php
include_once "scripts/config.php";
include_once "scripts/functions.php";
session_start();

$userID = checkLogin();

var_dump($userID);

$otherUserName = "";
$otherUserFriendly = "";

//If no conversation is present, auto-redirect.
if (isset($_GET['conversation']) == false) {
    print("Hello!");
    autoRedirectToConversation($userID);
} else {
    $conversationID = $_GET['conversation'];
    //Get the friendly name and usernameof the person the conversation is with .
    $sql = "SELECT * FROM conversation WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $conversationID);
    $ps->execute();

    $result = $ps->fetch();
    if ($result['user1_id'] == $userID) {
        //User 1 is the logged in user.
        $chatOtherUserID = $result['user2_id'];
    } else {
        $chatOtherUserID = $result['user1_id'];
    }

    //Get the other users username and friendly name
    $sql = "SELECT * FROM user WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $chatOtherUserID);
    $ps->execute();

    $row = $ps->fetch();
    $otherUserName = $row['name'];
    $otherUserFriendly = $row['friendly_name'];
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
    <title>Chat - Gymnasiearbete</title>
</head>

<body>
    <header class="chat-window-header">
        <button id="menu" class="header-button"><img src="graphics/menuicon.svg" alt="Öppna meny" class="button-image"></button>

        <div class="current-chat">
            <h2 class="current-chat-friendly-name"><?= $otherUserFriendly ?></h2>
            <h3 class="current-chat-username"><?= $otherUserName ?></h3>
        </div>
        <button id="user-button" class="header-button"><img src="graphics/usericon.svg" alt="(användarnamn)" class="button-image"></button>
    </header>
    <!-- Show the 'blackbox' only when the menu is active -->
    <div id="blackbox"></div>
    <nav class="left-menu" id="left-menu-a">
        <ul class="left-menu-list conversation-list">

            <?php
            //Generate a list of all conversations the logged in user has.
            $sql = "SELECT * FROM conversation WHERE user1_id=:user_id OR user2_id=:user_id";
            $ps = $db->prepare($sql);
            $ps->bindValue(":user_id", $userID);
            $ps->execute();

            while ($row = $ps->fetch()) {
                echo "<li class=\"conversation-list-litem\">";
                echo "<a href=\"chat.php?conversation=" . htmlspecialchars($row['id'], 2)  . ">";
                //Check wether the logged in user is 1 or 2
                if ($row['user1_id'] == $userID) {
                    $otherUserID = $row['user2_id'];
                } else {
                    $otherUserID = $row['user1_id'];
                }

                //Get the name of the other user!
                $sql = "SELECT friendly_name FROM user WHERE id=:id";
                $psinfo = $db->prepare($sql);
                $psinfo->bindValue(":id", $otherUserID);
                $psinfo->execute();

                $rowinfo = $psinfo->fetch();

                echo "<p class=\"conversation-list-friendly-name\">{$rowinfo['friendly_name']}</p>";
                echo "<p class=\"conversation-list-last-message\">Hej på dig! Jag skriver här ett meddelande..</p>";
                echo "</a> </li>";
            }
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
    <main class="chat-window-main">
        <section class="message sentby-this">
            <p class="message-details">
                Du
                <time datetime="2020-12-02 15:03">2/12 15:09</time>
            </p>
            <p class="message-text">
                ok
            </p>
        </section>
        <?php
        //Find all messages in this conversation
        $sql = "SELECT * FROM message WHERE conversation_id=:conversation_id";
        $ps = $db->prepare($sql);
        $ps->bindValue(":conversation_id", $conversationID);
        $ps->execute();

        if ($ps->rowCount() == 0) {
            //Send a first message.
        } else {
            while ($message = $ps->fetch()) {
                //Check who the message is from.
                if ($message['from_id'] == $userID) {
                    //The message was sent by this user
                    echo "<section class=\"message sentby-other\"> <p class=\"message-details\"> Du <time datetime=\"{$message['time_sent']}\">{$message['time_sent']}</time>";
                } else {
                }
            }
        }


        ?>
    </main>
    <footer>
        <form method="post" class="message-send">
            <input type="text" name="new-message-text" id="new-message-text">
            <button id="message-send"><img src="graphics/sendicon.svg" alt="Skicka" class="button-image"></button>
        </form>
    </footer>
</body>

</html>