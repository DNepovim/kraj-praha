var Component = require('./component')
var $ = window.jQuery

class ScrollToAnchor extends Component {

	get listeners() {
		return {
			'click a[href^="#"]': 'handleClick'
		}
	}

	handleClick(e, self) {
	e.preventDefault();

		$('html, body').animate({
			scrollTop: $($.attr(e.currentTarget, 'href')).offset().top
		}, 500);
	}
}

module.exports = ScrollToAnchor
