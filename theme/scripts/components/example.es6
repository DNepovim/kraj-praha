var Component = require('./component')

/**
 * Example component class
 *
 * - all DOM operations must be executed after creating an instance (in constructor)
 * - when defining own constructor, don't forget to call super(element, data)
 * - DOM event listeners are in Backbone style, handlers are called with the instance as `this`
 *
 */
class Example extends Component {

	get listeners() {
		return {
			'click .example-child': 'handleClick'
		}
	}

	handleClick(e, target) {
		e.preventDefault()
		alert(this.data)
	}

}

module.exports = Example
