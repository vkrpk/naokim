const imageCarousel = document.querySelector(".image-carousel");
const legendImage = document.querySelector(".legend-image");
window.addEventListener("resize", () => {
    let widthImage = imageCarousel.getBoundingClientRect().width;
    console.log(widthImage);
    legendImage.style.maxWidth = widthImage + "px";
    //     console.log(widthImage);
});
