var Component = require('./component')
var $ = window.jQuery

class ColorSwitcher extends Component {

	constructor(element, data) {
		super(element, data)

		$('#color-switcher').addClass('is-active')

	}

	get listeners() {
		return {
			'click .color-switcher-button': 'handleClick'
		}
	}

	handleClick(e, self) {
		const $target = $(e.currentTarget)
		self.updateLink($target.data('stylesheet'), 'stylesheet')
		self.updateLink($target.data('icon'), 'icon')
		self.createCookie('style', $target.attr('title'), 365)
		$('.color-switcher-button').removeClass('active')
		$target.addClass('active')
	}

	createCookie(name,value,days) {
	  if (days) {
		var date = new Date()
		date.setTime(date.getTime()+(days*24*60*60*1000))
		var expires = ' expires='+date.toGMTString()
	  }
	  else expires = ''
	  document.cookie = name + '=' + value + '' + expires + '; path=/'
	}

	updateLink(url, rel) {
		$(`link[rel*="${rel}"]`).attr('href', url)
	}

}

module.exports = ColorSwitcher
