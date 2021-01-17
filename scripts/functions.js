/**
 *  Hide the side-meny via an animation and set required showSideMenu.
 */
function hideMenu() {
    sideMenu.style.display = "none";
    blackBox.style.display = "none";
    showSideMenu = false;
}

/**
 *  Show the side-meny via an animation and set required showSideMenu.
 */
function showMenu() {
    sideMenu.style.display = "flex";
    blackBox.style.display = "block";
    showSideMenu = true;
}