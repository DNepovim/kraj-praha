var Component = require('./component')

class ImageUpload extends Component {

	get listeners() {
		return {
			'change .form__input--upload': 'handleChange',
			'click .remover': 'handleClick',
		}
	}

	handleChange(e, target) {
		const label = document.querySelector(target.dataset.label)
		const image = URL.createObjectURL(target.files[0])

		label.style.backgroundImage = 'url(' + image + ')'

		this.setActive(true)
	}

	handleClick(e, target) {
		const input = document.querySelector(target.dataset.for)
		const label = document.querySelector(input.dataset.label)

		input.value = ''
		label.style.backgroundImage = 'none'

		this.setActive(false)
	}

	setActive(active) {
		document.querySelectorAll('.remover').forEach((remover) => remover.classList.toggle('is-active', active))
		document.getElementById('upload_icon').style.display = active ? 'none' : ''
	}
}

module.exports = ImageUpload
