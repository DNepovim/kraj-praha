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
		const $label = $($(target).data('label'))
		const image = (window.URL ? URL : webkitURL).createObjectURL(target.files[0])

		$label.css('background-image', 'url(' + image + ')')

		$('.remover').show()
		$('#upload_icon').hide()
	}

	handleClick(e, self) {
        const $input = $($(e.currentTarget).data('for'))
        const label = $input.data('label')

        $input.wrap('<form>').closest('form').get(0).reset()
        $input.unwrap()

        $(label).css('background-image', 'none')

        $('.remover').hide()
		$('#upload_icon').show()
	}

}

module.exports = ImageUpload
