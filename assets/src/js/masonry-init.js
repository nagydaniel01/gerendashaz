import imagesLoaded from "imagesloaded";
import Masonry from "masonry-layout";

document.addEventListener('DOMContentLoaded', () => {
    // Select all grids on the page
    const grids = document.querySelectorAll('.grid');

    grids.forEach(grid => {
        // Wait until images are loaded for this grid
        imagesLoaded(grid, () => {
            new Masonry(grid, {
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: true
            });
        });
    });
});
