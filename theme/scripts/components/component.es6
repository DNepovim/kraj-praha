var eventSplitter = /^(\S+)\s*(.*)$/

/**
 * Abstract component class
 *
 * - use for creating own components with standartized API
 * - uses native event delegation for attaching listeners
 *
 * @abstract
 * @class
 * @module component
 *
 * @author Matěj Šimek <email@matejsimek.com> (http://www.matejsimek.com)
 */
class Component {

	/**
	 * @constructor
	 * @param {HTMLElement} element
	 * @param {object} data
	 */
	constructor(element, data = {}) {
		/** @type {HTMLElement} */
		this.el = element
		/** @type {object|null} */
		this.data = data
		/** @type {Array<{type: string, listener: Function}>} */
		this.attached = []

		this.attachListeners()
	}

	/**
	 * Component listeners
	 *
	 * Format:
	 * 	- "type": "handlerName"
	 * 	- "type<space>.selector": "handlerName"
	 *
	 * @param {Component~eventHandler} event handler which is a component method
	 */
	get listeners() {
		return {
			// 'click .example-child': 'handleClick'
		}
	}

	/**
	 * Assign event handlers from this.listeners property
	 */
	attachListeners() {
		const listeners = this.listeners

		for (const event in listeners) {
			const callback = this[listeners[event]]
			const [, type, selector] = event.trim().match(eventSplitter)

			/**
			 * Handler called when an event occured
			 *
			 * @callback Component~eventHandler
			 * @param {Event} event - an event object
			 * @param {Element} target - an element matching the selector (the component element when there is no selector)
			 * @this {Component} - current instance
			 */
			const listener = (e) => {
				if (!selector) return callback.call(this, e, this.el)

				const target = e.target instanceof Element ? e.target.closest(selector) : null
				if (target && this.el.contains(target)) callback.call(this, e, target)
			}

			this.el.addEventListener(type, listener)
			this.attached.push({ type, listener })
		}
	}

	/**
	 * Remove event listeners
	 */
	detachListeners() {
		this.attached.forEach(({ type, listener }) => this.el.removeEventListener(type, listener))
		this.attached = []
	}

	/**
	 * Gracefully destroy current instance properties
	 */
	destroy() {
		this.detachListeners()

		for (const prop in this) {
			this[prop] = null
		}
	}

	/**
	 * Returns a child
	 * @param  {string} CSS selector
	 * @return {Element|null}
	 */
	child(selector) {
		return this.el.querySelector(selector)
	}

}

module.exports = Component
