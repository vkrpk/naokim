const imageCarousel = document.querySelector(".image-carousel");
const legendImage = document.querySelector(".legend-image");
window.addEventListener("resize", () => {
    let widthImage = imageCarousel.getBoundingClientRect().width;
    legendImage.style.maxWidth = widthImage + "px";
});
