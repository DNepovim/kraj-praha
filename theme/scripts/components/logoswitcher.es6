var Component = require('./component')
var $ = window.jQuery

class LogoSwitcher extends Component {

	get listeners() {
		return {
			'click #logotype-toggler': 'handleClick'
		}
	}

	handleClick(e, self) {
		e.preventDefault()

		const $logotyp = $('#logotyp')
		const currentShape = $logotyp.find('use').attr('xlink:href')

		if (currentShape === '#shape-logotype-skaut') {
			$logotyp.find('use').attr('xlink:href', '#shape-logotype-junak')
			self.createCookie('logotype', 'logotype-junak', 365)
		} else {
			$logotyp.find('use').attr('xlink:href', '#shape-logotype-skaut')
			self.createCookie('logotype', 'logotype-skaut', 365)
		}
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

}

module.exports = LogoSwitcher
