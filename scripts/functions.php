<?php

declare(strict_types=1);
require_once "config.php";

/**
 * Generates the side menu
 * @param $userID The user id for whom the menu should be generated.
 */
function generateSideMenu($userID)
{
    global $db;

    //Generate a list of all conversations the logged in user has.
    $sql = "SELECT * FROM conversation WHERE user1_id=:user_id OR user2_id=:user_id";
    $ps = $db->prepare($sql);
    $ps->bindValue(":user_id", $userID);
    $ps->execute();

    while ($row = $ps->fetch()) {
        echo "<li class=\"conversation-list-litem\">";
        echo "<a href=\"chat.php?conversation=" . htmlspecialchars($row['id'], 2)  . "\" class=\"left-menu-link\">";
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

        echo "<p class=\"conversation-list-friendly-name\">" . htmlspecialchars($rowinfo['friendly_name']) . "</p>";
        echo "<p class=\"conversation-list-last-message\">Hej på dig! Jag skriver här ett meddelande..</p>";
        echo "</a> </li>";
    }
}


/**
 * Check what conversation the server should auto-redirect to if none is set in $_GET.
 * Takes the conversation 
 * If no conversation is found auto-redirect to newconversation.php
 * 
 * @param $user - The user ID for which redirection should occur. 
 */
function autoRedirectToConversation($userID)
{

    global $db;

    //Get's the most recent conversation, as that would probably where the user would like to start. 
    $sql = "SELECT id FROM conversation WHERE user1_id=:user_id OR user2_id=:user_id ORDER BY last_message_sent DESC";
    $ps = $db->prepare($sql);
    $ps->bindValue(":user_id", $userID);
    $ps->execute();

    if ($ps->rowCount() == 0) {
        //There are no conversations for this user, redirect to newconversation.php
        header("Location: newconversation.php");
        exit;
    } else {
        $row = $ps->fetch();
        header("Location: chat.php?conversation=" . $row['id']);
        exit;
    }
}


/**
 * Call this function when a users identity has been confirmed and they should be logged in. 
 * Generates and handles the userSessionToken that makes sure that a user is who they claim to be.
 * NOTICE: Verification of identity must be complete. 
 */
function logInUser(int $userId, bool $keepBetweenSessions)
{
    print("I'm here");
    global $db;
    $token = generateUniqueToken();
    //Store the token in session
    $_SESSION['token'] = $token;

    //Put the session in the database.
    $sql = "INSERT INTO session (token, user_id, date_created, keep_between_sessions, browser_session) VALUES (:token, :user_id, CURRENT_TIMESTAMP, :keep_between_sessions, :browser_session)";
    $ps = $db->prepare($sql);
    $ps->bindValue(":token", $token);
    $ps->bindValue(":user_id", $userId);
    $ps->bindValue(":keep_between_sessions", intval($keepBetweenSessions));
    $ps->bindValue(":browser_session", session_id());
    $ps->execute();



    //Store a cookie with the token on the user's computer for 2 months
    if ($keepBetweenSessions) {
        setcookie("keep-between-session", $token, time() + 60 * 60 * 24 * 60);
    }

    header("Location: chat.php");
    exit;
}

/**
 * Generates a new random ID and checks that it isn't already in use.
 */
function generateUniqueToken()
{
    //Generate a random candidate for an ID
    $candidate = bin2hex(random_bytes(16));

    //Get the database connection!
    //https://www.geeksforgeeks.org/php-variables/
    global $db;

    //Call the database and check if the candidate is taken!
    $sql = "SELECT token FROM session WHERE token=:token";
    $ps = $db->prepare($sql);
    $ps->bindValue(":token", $candidate);
    $ps->execute();

    if ($ps->rowCount() == 0) {
        return $candidate;
    } else {
        return generateUniqueToken();
    }
}

/**
 * Check if a user is logged in by verifying any token that is placed. 
 * Should no such token exist, the browser is redirected to the log-in page, if a user is verified,
 * user_id is returned.
 * 
 * @return $userId - The id of the user that is verified. 
 */
function checkLogin()
{
    global $db;
    var_dump($_SESSION);
    if (isset($_SESSION['token'])) {
        $token = $_SESSION['token'];

        //Call the database and verify the token!
        $sql = "SELECT * FROM session WHERE token=:token";
        $ps = $db->prepare($sql);
        $ps->bindValue(":token", $token);
        $ps->execute();
        var_dump($ps->rowCount());

        //Should the token exist, store the response in $return
        if ($return = $ps->fetch()) {
            if ($return['keep_between_sessions' == 1]) {
                //Check if current session, otherwise renew this
                // only checking once per session avoids spamming the server. 
                if ($return['browser_session'] == session_id()) {
                    return $return['user_id'];
                } else {
                    //Renew the confidence given to me by the server. 
                    $sql = "UPDATE session SET date_created = CURRENT_TIMESTAMP, browser_session = :browser_session WHERE token=:token";
                    $ps = $db->prepare($sql);
                    $ps->bindValue(":token", $return['token']);
                    $ps->bindValue(":browser_session", session_id());
                    $ps->execute();
                }
            } else {
                //Check if current session, otherwise redirect to login
                if ($return['browser_session'] == session_id()) {
                    return $return['user_id'];
                } else {
                    header("Location: index.php");
                    exit;
                }
            }
        } else {
            //If the token exist but no response in database, redirect to login page and clear $_SESSION['token']
            var_dump($ps->errorInfo());
            unset($_SESSION['token']);
            header("Location: index.php");
            exit;
        }
    } else if (doesValidLoginCookieExist()) {
        //Call yourself and verify the token in $_SESSION.
        checkLogin();
    } else {
        header("Location: index.php");
        exit;
    }
}

/**
 * Check for a valid token cookie. Stores the token in session if true. 
 * @return bool True if a valid cookie exist, otherwise false
 */
function doesValidLoginCookieExist()
{

    global $db;

    //Verify if $token is set.
    if (isset($_COOKIE['keep-between-session'])) {
        $token = $_COOKIE['keep-between-session'];

        //Test that this works (it should unless it has been tampered with)
        $sql = "SELECT * FROM session WHERE token=:token";
        $ps = $db->prepare($sql);
        $ps->bindValue(":token", $token);
        $ps->execute();
        if ($ps->rowCount() > 0) {
            $result = $ps->fetch();
            if ($result['keep_between_sessions'] == 1) {
                $_SESSION['token'] = $result['token'];
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Sets the $message variable to a 
 * specified message so it don't have to be typed out
 * See the definition to see values accepted.
 * Returns the error for the code specified. 
 */
function userErrorCodes(int $code)
{
    switch ($code) {
        case 0:
            return "0: Klart!";
            break;

        case 1:
            return "1: Oops. Något gick fel!";
            break;

        case 2:
            return "2: Fel användarnamn eller lösenord";
            break;

        case 3:
            return "3: Databasfel. Försök igen om ett tag";
            break;

        case 4:
            return "4: Användarnamn upptaget. Använd ett annat";
            break;

        case 5:
            return "5: Lösenord matchar inte. Försök igen";
            break;
        case 6:
            return "6: Den användaren finns inte. Försök igen";
            break;
        case 7:
            return "7: Lösenordet uppfyller inte kraven.";
            break;
        case 8:
            return "8: Fel lösenord. Försök igen";
            break;
    }
}
