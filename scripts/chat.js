//https://stackoverflow.com/questions/11715646/scroll-automatically-to-the-bottom-of-the-page
// var scrollingElement = (document.scrollingElement || document.body);
// scrollingElement.scrollTop = scrollingElement.scrollHeight;

//Get all messages
let messages = document.getElementsByClassName("message")
//Scroll to the last one
document.body.scrollTo(0, messages[messages.length - 1]);






