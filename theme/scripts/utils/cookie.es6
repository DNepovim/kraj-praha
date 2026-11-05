/**
 * Stores a cookie for the whole site
 *
 * @param {string} name
 * @param {string} value
 * @param {number} days - lifetime in days, omit for a session cookie
 */
module.exports = function createCookie(name, value, days) {
	let expires = ''

	if (days) {
		const date = new Date()
		date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000)
		expires = ' expires=' + date.toUTCString()
	}

	document.cookie = name + '=' + value + '' + expires + '; path=/'
}
