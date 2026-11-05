var Component = require('./component')
var createCookie = require('../utils/cookie')

class ColorSwitcher extends Component {

	constructor(element, data) {
		super(element, data)

		document.getElementById('color-switcher').classList.add('is-active')
	}

	get listeners() {
		return {
			'click .color-switcher-button': 'handleClick'
		}
	}

	handleClick(e, target) {
		this.updateLink(target.dataset.stylesheet, 'stylesheet')
		this.updateLink(target.dataset.icon, 'icon')
		createCookie('style', target.getAttribute('title'), 365)
		document.querySelectorAll('.color-switcher-button').forEach((button) => button.classList.remove('active'))
		target.classList.add('active')
	}

	updateLink(url, rel) {
		document.getElementById(`main-${rel}`).setAttribute('href', url)
	}

}

module.exports = ColorSwitcher
