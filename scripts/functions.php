<?php

declare(strict_types=1);
include_once "config.php";


/**
 * Call this function when a users identity has been confirmed and they should be logged in. 
 * Generates and handles the userSessionToken that makes sure that a user is who they claim to be.
 * NOTICE: Verification of identity must be complete. 
 */
function logInUser(int $userId, bool $keepBetweenSessions)
{
    global $db;
    $token = generateUniqueToken();
    //Store the token in session
    $_SESSION['token'] = $token;

    //Put the session in the database.
    $sql = "INSERT INTO session (token, user_id, date_created, keep_between_sessions, browser_session) VALUES (:token, :user_id, CURRENT_TIMESTAMP, :keep_between_sessions, :browser_session)";
    $ps = $db->prepare($sql);
    $ps->bindValue(":token", $token);
    $ps->bindValue(":user_id", $userId);
    $ps->bindValue(":keep_between_sessions", $keepBetweenSessions);
    $ps->bindValue(":browser_session", session_id());
    $ps->execute();

    //Store a cookie with the token on the user's computer for 2 months
    if ($keepBetweenSessions) {
        setcookie("keep-between-session", $token, time() + 60 * 60 * 24 * 60);
    }

    header("Location: ../chat.php");
    exit;
}

/**
 * Generates a new random ID and checks that it isn't already in use.
 */
function generateUniqueToken()
{
    //Generate a random candidate for an ID
    $candidate = bin2hex(random_bytes(16));
    var_dump($candidate);

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
        generateUniqueToken();
    }
}

/**
 * Check if a user is logged in by verifying any token that is placed. 
 * Should no such token exist, the browser is redirected to the log-in page, if a user is verified,
 * user_id is returned.
 * 
 * @return int $userId - The id of the user that is verified. 
 */
function checkLogin()
{
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
            return "0: Success!";
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
    }
}
