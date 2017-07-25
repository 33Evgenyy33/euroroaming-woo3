jQuery(document).ready(function($) {
    $('.single-post li a[href^="#"]').click(function() {
        $('html,body').animate({ scrollTop: $(this.hash).offset().top-37}, 700);
        return false;
        e.preventDefault();
    });
});