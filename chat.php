<?php
include_once "scripts/config.php";
include_once "scripts/functions.php";
session_start();

$userID = checkLogin();


$otherUserName = "";
$otherUserFriendly = "";
//If no conversation is present, auto-redirect.
if (isset($_GET['conversation']) == false) {
    autoRedirectToConversation($userID);
} else {
    $conversationID = $_GET['conversation'];
    //Get the friendly name and username of the person the conversation is with .
    $sql = "SELECT * FROM conversation WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $conversationID);
    $ps->execute();

    $result = $ps->fetch();
    if ($result['user1_id'] == $userID) {
        //User 1 is the logged in user.
        $chatOtherUserID = $result['user2_id'];
    } else if ($result['user2_id'] == $userID) {
        $chatOtherUserID = $result['user1_id'];
    } else {
        //The user has attempted to make unathorized entry to another conversation. Redirect to regular chat.php and redirect
        header("Location: chat.php");
        exit;
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

//Check and send a message
if (isset($_POST['new-message-text'])) {
    $sql = "INSERT INTO message (id, conversation_id, from_id, content, been_read) VALUES (null, :conversation_id, :from_id, :content, 0)";
    $ps = $db->prepare($sql);
    $ps->bindValue(":conversation_id", $conversationID);
    $ps->bindValue(":from_id", $userID);
    $ps->bindValue(":content", $_POST['new-message-text']);
    $ps->execute();

    //Update the timestamp in conversation
    $sql = "UPDATE conversation SET last_message_sent = CURRENT_TIMESTAMP WHERE id=:id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":id", $conversationID);
    $ps->execute();
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
    <script src="scripts/update.js" defer></script>
    <title>Chat - Gymnasiearbete</title>
</head>

<body>
    <header class="chat-window-header">
        <button id="menu" class="header-button"><img src="graphics/menuicon.svg" alt="Öppna meny" class="button-image"></button>

        <div class="current-chat">
            <h2 class="current-chat-friendly-name"><?= htmlspecialchars($otherUserFriendly) ?></h2>
            <h3 class="current-chat-username"><?= htmlspecialchars($otherUserName) ?></h3>
            <span id="conversation-id" style="display: none"><?= $conversationID ?></span>
        </div>
        <button id="user-button" class="header-button"><img src="graphics/usericon.svg" alt="(användarnamn)" class="button-image"></button>
    </header>
    <!-- Show the 'blackbox' only when the menu is active -->
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
                <a href="./settings.php" class="left-menu-link"><img src="graphics/settingsicon.svg" alt="Inställningar" class="button-image left-menu-setting-image">Inställningar</a>
            </li>
            <li class="left-menu-setting">
                <a href="./scripts/logout.php" class="left-menu-link"><img src="graphics/exitsymbol.svg" alt="Logga ut" class="button-image left-menu-setting-image">
                    Logga ut
                </a>
            </li>
        </ul>
    </nav>
    <main class="chat-window-main">
        <?php
        //Find all messages in this conversation
        $sql = "SELECT * FROM message WHERE conversation_id=:conversation_id ORDER BY time_sent DESC";
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
                    echo "<section class=\"message sentby-this\">";
                    //Check if the message has been read
                    if ($message['been_read'] == 1) {
                        $readStatus = "Läst";
                    } else {
                        $readStatus = "Skickat";
                    }
                    echo "<p class=\"message-details\"> Du <time datetime=\"{$message['time_sent']}\">{$message['time_sent']}</time> - $readStatus</p>";
                } else {
                    echo "<section class=\"message sentby-other\">";
                    echo "<p class=\"message-details\">" . htmlspecialchars($otherUserFriendly) . " <time datetime=\"{$message['time_sent']}\">{$message['time_sent']}</time></p>";
                }

                echo "<p class=\"message-text\">" . htmlspecialchars($message['content']) . "</p></section>";

                //If the message sent by the other user is marked as un-read, then mark it as read!
                if ($message['been_read'] == 0) {
                    $sql = "UPDATE message SET been_read=1 WHERE id=:id AND from_id=:from_id";
                    $updatePs = $db->prepare($sql);
                    $updatePs->bindValue(":id", $message['id']);
                    $updatePs->bindValue(":from_id", $chatOtherUserID);
                    $updatePs->execute();
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