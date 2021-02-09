<?php

declare(strict_types=1);

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
            return "Success!";
            break;

        case 1:
            return "Oops. Något gick fel!";
            break;

        case 2:
            return "Fel användarnamn eller lösenord";
            break;

        case 3:
            return "Databasfel. Försök igen om ett tag";
            break;

        case 4:
            return "Användarnamn upptaget. Använd ett annat";
            break;

        case 5:
            return "Lösenord matchar inte. Försök igen";
            break;
    }
}
