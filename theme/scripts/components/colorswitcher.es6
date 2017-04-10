var Component = require('./component')
var $ = window.jQuery

class ColorSwitcher extends Component {

	constructor(element, data) {
		super(element, data)

		$('#color-switcher').show()

	}

	get listeners() {
		return {
			'click .color-switcher-button': 'handleClick'
		}
	}

	handleClick(e, self) {
		const $target = $(e.currentTarget)
		self.getCSS($target.data('stylesheet'))
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
	  document.cookie = name + '=' + value + '' + expires + ' path=/'
	}

	getCSS( url, callback ) {
		$(document.createElement('link')).attr({
			href: url,
			media: 'screen',
			type: 'text/css',
			rel: 'stylesheet'
		}).appendTo('head')

		$(document.createElement('img')).attr('src', url)
	}

}

module.exports = ColorSwitcher
