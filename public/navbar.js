const toggleButton = document.getElementById("toggle-button");
const sideBar = document.getElementById("side-bar");
const header = document.getElementById("header");

toggleButton.addEventListener("click", show);

function show() {
    header.classList.toggle("active");
}

const content = document.getElementById("content");
const navbarLg = document.getElementById("navbar-lg");

closeOnClick = [content, navbarLg];

closeOnClick.forEach((element) => {
    element.addEventListener("click", () => {
        header.classList.remove("active");
    });
});
