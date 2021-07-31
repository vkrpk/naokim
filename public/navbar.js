const toggleButton = document.getElementById("toggle-button");
const sideBar = document.getElementById("side-bar");
const header = document.getElementById("header");
const searchInput = document.getElementById("search-input");

toggleButton.addEventListener("click", show);
function changePlaceholder() {
    if (window.innerWidth < 576) {
        searchInput.placeholder = "Recherchez..";
    } else {
        searchInput.placeholder = "Recherchez un produit..";
    }
}

function show() {
    header.classList.toggle("active");
    changePlaceholder();
}

const content = document.getElementById("content");
const navbarLg = document.getElementById("navbar-lg");

closeOnClick = [content, navbarLg];

closeOnClick.forEach((element) => {
    element.addEventListener("click", () => {
        header.classList.remove("active");
    });
});
window.onresize = () => {
    changePlaceholder();
};
