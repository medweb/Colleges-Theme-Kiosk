jQuery( document ).ready(function($) {

    // Prevent clicking anywhere else besides prev/next
    function blockClicks(e) {
        if (e.target.closest('.sb-wall')){
            e.stopPropagation();
            e.preventDefault();
        }
    }
    document.addEventListener("click", blockClicks, true);
});
