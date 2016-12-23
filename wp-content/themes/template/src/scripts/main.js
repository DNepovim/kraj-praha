$(document).ready(function() {

    $('#primaryPostForm').validate()

    // Image preview in upload input
    $('.form__input--upload').on('change', function () {
        $('#img_url')[0].src = (window.URL ? URL : webkitURL).createObjectURL(this.files[0]);
    })

});
