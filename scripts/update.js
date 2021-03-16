//Filip Sandström

//Calls the server to check wether there is an update.
// If one exist, update the page.


setInterval(async () => {
    let conversationId = document.getElementById("conversation-id").innerText;

    let response = await fetch('scripts/checkmessage?id=' + conversationId);
    let reply = await response.text();

    if (reply == "1") {
        window.location.href("chat.php?conversation=" + conversationId);
    }
}, 1500);

