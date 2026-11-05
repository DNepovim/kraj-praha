var Component = require('./component')

class ScrollToAnchor extends Component {

	get listeners() {
		return {
			'click a[href^="#"]': 'handleClick'
		}
	}

	handleClick(e, target) {
		const id = decodeURIComponent(target.hash.slice(1))
		const destination = id && document.getElementById(id)
		if (!destination) return

		e.preventDefault()
		destination.scrollIntoView({ behavior: 'smooth' })
	}
}

module.exports = ScrollToAnchor
