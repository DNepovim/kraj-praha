$(document).ready(function () {

    // Show color switcher
    $('#color-switcher').show()

    $('#primaryPostForm').validate()

    // Image preview in upload input
    $('.form__input--upload').on('change', function () {

        var label = $(this).data('label')
        var image = (window.URL ? URL : webkitURL).createObjectURL(this.files[0])

        $(label).css('background-image', 'url(' + image + ')')

        $('.remover').css('display', 'block')
    })

    // Remove image from form
    $('.remover').on('click', function () {
        var input = $(this).data('for')
        var label = $(input).data('label')

        $(input).wrap('<form>').closest('form').get(0).reset()
        $(input).unwrap()

        $(label).css('background-image', 'none')

        $('.remover').css('display', 'none')
    })

    // Color switcher
    $('.color-switcher-button').on('click', function() {
      getCSS($(this).data('stylesheet'))
      createCookie("style", $(this).attr('title'), 365)
      $('.color-switcher-button').removeClass('active')
      $(this).addClass('active')
    })
})

function createCookie(name,value,days) {
  if (days) {
    var date = new Date()
    date.setTime(date.getTime()+(days*24*60*60*1000))
    var expires = " expires="+date.toGMTString()
  }
  else expires = ""
  document.cookie = name + "=" + value + ";" + expires + "; path=/"
}

function getCSS( url, callback ) {
    $(document.createElement('link')).attr({
        href: url,
        media: 'screen',
        type: 'text/css',
        rel: 'stylesheet'
    }).appendTo('head')

    $(document.createElement('img')).attr('src', url)
}