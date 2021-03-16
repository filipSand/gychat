//Filip Sandström

//Calls the server to check wether there is a new message.
// If one exist, update the page.


setInterval(async () => {
    let conversationId = document.getElementById("conversation-id").innerText;

    let response = await fetch('./checkmessage?id=' + conversationId);
    let reply = await response.text();

    if (reply == "1") {
        location.reload();
    }
}, 2000);

