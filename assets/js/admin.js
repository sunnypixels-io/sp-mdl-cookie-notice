(function ($) {

    $(document).ready(function () {

    });

    $(document).on('click', '.js-mdl-customizer-reset-cookies', function(e) {
        e.preventDefault();
        window.wpCookies.remove('EuAccCookies', '/', window.location.hostname);
        wp.customize.previewer.refresh()
    });

})(jQuery);