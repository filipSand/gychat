//https://stackoverflow.com/questions/11715646/scroll-automatically-to-the-bottom-of-the-page
// var scrollingElement = (document.scrollingElement || document.body);
// scrollingElement.scrollTop = scrollingElement.scrollHeight;

//Get all messages
let messages = document.getElementsByClassName("message")
//Scroll to the last one
document.body.scrollTo(0, messages[messages.length - 1]);

//Show or hide menu
let sideMenu = document.getElementById("left-menu-a");
let sideMenuButton = document.getElementById("menu");
let blackBox = document.getElementById("blackbox");
let showSideMenu = false;

//Menu button click
sideMenuButton.onclick = () => {
    //console.log("Click!")
    if (showSideMenu) {
        //console.log("IF!")
        hideMenu();
    } else {
        //console.log("ELSE!")
        showMenu();
    }
}

blackBox.onclick = () => {
    hideMenu();
}








