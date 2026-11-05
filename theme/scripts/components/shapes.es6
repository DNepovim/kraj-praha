var Component = require('./component')

var spriteInserted = false

/**
 * Shapes component class
 *
 * - injects SVG sprite into body
 * - caches the sprite in localStorage (keyed by its versioned url) so page
 *   navigations render the shapes immediately instead of waiting for the fetch
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

		const cacheKey = 'sprite:' + this.data.url
		let cached = null

		try {
			cached = localStorage.getItem(cacheKey)
		} catch (e) {}

		if (cached) {
			document.body.insertAdjacentHTML('afterbegin', cached)
			return
		}

		fetch(this.data.url)
			.then((response) => {
				if (!response.ok) throw new Error(response.statusText)
				return response.text()
			})
			.then((sprite) => {
				document.body.insertAdjacentHTML('afterbegin', sprite)

				try {
					localStorage.setItem(cacheKey, sprite)
				} catch (e) {}
			})
			.catch(() => {
				spriteInserted = false
				this.injectSprite()
			})
	}

}


module.exports = Shapes
