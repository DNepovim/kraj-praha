var Component = require('./component')
var $ = window.jQuery

class ImageUpload extends Component {

	get listeners() {
		return {
			'change .form__input--upload': 'handleChange',
			'click .remover': 'handleClick',
		}
	}

	handleChange(e, self) {

		const target = e.currentTarget
		const $label = $($(e.currentTarget).data('label'))
		let image = (window.URL ? URL : webkitURL).createObjectURL(target.files[0])

		$label.css('background-image', 'url(' + image + ')')

		$('.remover').css('display', 'block')
	}

	handleClick(e, self) {
        var input = $(e.currentTarget).data('for')
        var label = $(input).data('label')

        console.log($(input).wrap('<form>').closest('form'))
        console.log($(input).wrap('<form>').closest('form').get(0))

        $(input).wrap('<form>').closest('form').get(0).reset()
        $(input).unwrap()

        $(label).css('background-image', 'none')

        $('.remover').css('display', 'none')
	}

}

module.exports = ImageUpload
