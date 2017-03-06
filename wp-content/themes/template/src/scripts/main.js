$(document).ready(function () {

    $('#primaryPostForm').validate()

    // Image preview in upload input
    $('.form__input--upload').on('change', function () {

        var label = $(this).data('label');
        var image = (window.URL ? URL : webkitURL).createObjectURL(this.files[0]);

        $(label).css('background-image', 'url(' + image + ')');

        $('.remover').css('display', 'block');
    })

    // Remove image from form
    $('.remover').on('click', function () {
        var input = $(this).data('for');
        var label = $(input).data('label');

        $(input).wrap('<form>').closest('form').get(0).reset();
        $(input).unwrap();

        $(label).css('background-image', 'none');

        $('.remover').css('display', 'none');
    })

    // Color switcher
    $('.color-switcher-button').on('click', function() {
        $('link#stylesheet').attr('href', $(this).data('stylesheet'));
        $('.color-switcher-button').removeClass('active');
        $(this).addClass('active');
    })
});

