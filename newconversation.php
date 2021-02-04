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
    <title>Logga in - Gymnasiearbete</title>
</head>

<body>
    <header class="chat-window-header">
        <button id="menu" class="header-button"><img src="graphics/menuicon.svg" alt="Öppna meny"
                class="button-image"></button>

        <div class="current-chat">
            <h2 class="current-chat-friendly-name">Sök ny konversation</h2>
            <h3 class="current-chat-username">Sök på @användarnamn</h3>
        </div>

    </header>
    <div id="blackbox"></div>
    <nav class="left-menu" id="left-menu-a">
        <ul class="left-menu-list conversation-list">
            <li class="conversation-list-item">
                <a href="#" class="left-menu-link">
                    <p class="conversation-list-friendly-name">Förnamn Efternamn</p>
                    <p class="conversation-list-last-message">Hej på dig! Jag skriver här ett meddelande..</p>
                </a>
            </li>
            <li class="conversation-list-item">
                <a href="#" class="left-menu-link">
                    <p class="conversation-list-friendly-name">Förnamn Efternamn</p>
                    <p class="conversation-list-last-message">Hej på dig! Jag skriver här ett meddelande..</p>
                </a>
            </li>
            <li class="conversation-list-item">
                <a href="#" class="left-menu-link">
                    <p class="conversation-list-friendly-name">Förnamn Efternamn</p>
                    <p class="conversation-list-last-message">Hej på dig! Jag skriver här ett meddelande..</p>
                </a>
            </li>
            <li class="conversation-list-item">
                <a href="#" class="left-menu-link">
                    <p class="conversation-list-friendly-name">Förnamn Efternamn</p>
                    <p class="conversation-list-last-message">Hej på dig! Jag skriver här ett meddelande..</p>
                </a>
            </li>
            <li class="conversation-list-item">
                <a href="#" class="left-menu-link">
                    <p class="conversation-list-friendly-name">Förnamn Efternamn</p>
                    <p class="conversation-list-last-message">Hej på dig! Jag skriver här ett meddelande..</p>
                </a>
            </li>
        </ul>
        <ul class="left-menu-list left-menu-settings">
            <li class="left-menu-setting">
                <a href="#" class="left-menu-link"><img src="graphics/plusicon.svg" alt="Lägg till"
                        class="button-image left-menu-setting-image">Ny chatt</a>
            </li>
            <li class="left-menu-setting">
                <a href="#" class="left-menu-link"><img src="graphics/settingsicon.svg" alt="Inställningar"
                        class="button-image left-menu-setting-image">Inställningar</a>
            </li>
            <li class="left-menu-setting">
                <a href="#" class="left-menu-link"><img src="graphics/exitsymbol.svg" alt="Logga ut"
                        class="button-image left-menu-setting-image">
                    Logga ut
                </a>
            </li>
        </ul>
    </nav>
    <footer>
        <form method="post" class="message-send">
            <input type="text" name="new-message-text" id="new-message-text">
            <button id="message-send"><img src="graphics/plusicon.svg" alt="Sök" class="button-image"></button>
        </form>
    </footer>
</body>

</html>