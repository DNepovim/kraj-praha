var Component = require('./component')

var spriteInserted = false

/**
 * Shapes component class
 *
 * - injects SVG sprite into body
 */
class Shapes extends Component {

	/**
	 * @param {HTMLElement} element
	 * @param {Object} data
	 */
	constructor(element, data) {
		super(element, data)

		if (!spriteInserted) this.injectSprite()
	}

	injectSprite() {
		spriteInserted = true

		fetch(this.data.url)
			.then((response) => {
				if (!response.ok) throw new Error(response.statusText)
				return response.text()
			})
			.then((sprite) => document.body.insertAdjacentHTML('afterbegin', sprite))
			.catch(() => {
				spriteInserted = false
				this.injectSprite()
			})
	}

}


module.exports = Shapes
