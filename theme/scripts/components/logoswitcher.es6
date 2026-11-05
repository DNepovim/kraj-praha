var Component = require('./component')
var createCookie = require('../utils/cookie')

class LogoSwitcher extends Component {

	get listeners() {
		return {
			'click #logotype-toggler': 'handleClick'
		}
	}

	handleClick(e, target) {
		e.preventDefault()

		const use = document.querySelector('#logotyp use')
		const isSkaut = use.getAttribute('xlink:href') === '#shape-logotype-skaut'

		use.setAttribute('xlink:href', isSkaut ? '#shape-logotype-junak' : '#shape-logotype-skaut')
		createCookie('logotype', isSkaut ? 'logotype-junak' : 'logotype-skaut', 365)
	}

}

module.exports = LogoSwitcher
