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
    <link rel="stylesheet" href="style/signup.css">
    <script src="scripts/functions.js"></script>
    <script src="scripts/chat.js" defer></script>
    <title>Ny användare - Gymnasiearbete</title>
</head>

<body>
    <h1>Chattapplikation</h1>
    <h2>Gymnasiearbete Filip Sandström</h2>
    <h3>Registrera dig</h3>
    <form action="" method="POST" class="login-page-form">
        <label for="username" id="username-label">Användarnamn</label>
        <input type="text" id="username-field" name="username" class="login-field" required>
        <label for="friendly-name" id="friendly-name-label">Namn</label>
        <input type="text" id="friendly-name-field" name="friendly-name" class="login-field" required>
        <p id="password-req-label">Krav på lösenord</p>
        <p id="password-req-text">Minst 12 tecken. Minst en stor och en liten bokstav. Minst en siffra.</p>
        <label for="password" id="password-label">Lösenord</label>
        <input type="password" name="password" id="password-field" min-length="12" required>

        <label for="password-confirm" id="password-confirm-label">Bekräfta lösen</label>
        <input type="password" name="password-confirm" id="password-confirm-field" required>
        <button type="submit" id="login-button">Registrera dig</button>
    </form>


</body>

</html>