//==============================================================================
//
// The events foundation, will provides the crossplatform event support
//
// @author Jack
// @version 1.0
// @date Fri Jun 26 17:17:57 2015
//
//==============================================================================

+(function(){

//==============================================================================
//
// Util Functions
//
//==============================================================================
const slice          = Array.prototype.slice
var str2arr = (s, d) => { return s.split(d || ' ') }
var isString = (o) => { return typeof o == 'string' }
var isFunction = (o) => { return typeof o == 'function' }
var selectorEngine = lilium.local('selectorEngine', (s, r) => { return r.querySelectorAll(s); });

//==============================================================================
//
// Definitions
//
//==============================================================================
const win = lilium.global();
const navigator = typeof navigator !== 'undefined' ? navigator : {};
const namespaceRegex = /[^\.]*(?=\..*)\.|.*/;
const nameRegex      = /\..*/;
const addEvent       = 'addEventListener';
const removeEvent    = 'removeEventListener';
const doc            = typeof document !== 'undefined' ? document : {};
const root           = doc.documentElement || {};
const W3C_MODEL      = root[addEvent];
const eventSupport   = W3C_MODEL ? addEvent : 'attachEvent';
const ONE            = {} // singleton for quick matching making add() do one()

const standardNativeEvents =
	'click dblclick mouseup mousedown contextmenu '                  + // mouse buttons
	'mousewheel mousemultiwheel DOMMouseScroll '                     + // mouse wheel
	'mouseover mouseout mousemove selectstart selectend '            + // mouse movement
	'keydown keypress keyup '                                        + // keyboard
	'orientationchange '                                             + // mobile
	'focus blur change reset select submit '                         + // form elements
	'load unload beforeunload resize move DOMContentLoaded '         + // window
	'readystatechange message '                                      + // window
	'error abort scroll ';                                             // misc

// element.fireEvent('onXYZ'... is not forgiving if we try to fire an event
// that doesn't actually exist, so make sure we only do these on newer browsers
const w3cNativeEvents =
	'show '                                                          + // mouse buttons
	'input invalid '                                                 + // form elements
	'touchstart touchmove touchend touchcancel '                     + // touch
	'gesturestart gesturechange gestureend '                         + // gesture
	'textinput '                                                     + // TextEvent
	'readystatechange pageshow pagehide popstate '                   + // window
	'hashchange offline online '                                     + // window
	'afterprint beforeprint '                                        + // printing
	'dragstart dragenter dragover dragleave drag drop dragend '      + // dnd
	'loadstart progress suspend emptied stalled loadmetadata '       + // media
	'loadeddata canplay canplaythrough playing waiting seeking '     + // media
	'seeked ended durationchange timeupdate play pause ratechange '  + // media
	'volumechange cuechange '                                        + // media
	'checking noupdate downloading cached updateready obsolete ';      // appcache

const nativeEvents = (function (hash, events, i) {
        for (i = 0; i < events.length; i++) events[i] && (hash[events[i]] = 1)
        return hash
      }({}, str2arr(standardNativeEvents + (W3C_MODEL ? w3cNativeEvents : ''))));

const customEvents = (function () {
	var isAncestor = 'compareDocumentPosition' in root ? function (element, container) {
		return container.compareDocumentPosition && (container.compareDocumentPosition(element) & 16) === 16
	}
	: 'contains' in root ? function (element, container) {
		container = container.nodeType === 9 || container === window ? root : container
		return container !== element && container.contains(element)
	}
	: function (element, container) {
		while (element = element.parentNode) if (element === container) return 1
		return 0
	}

	var check = function (event) {
		var related = event.relatedTarget
		return !related
			? related == null
			: (related !== this && related.prefix !== 'xul' && !/document/.test(this.toString())
				&& !isAncestor(related, this))
	}
	
	return {
		mouseenter: { base: 'mouseover', condition: check }
	  , mouseleave: { base: 'mouseout', condition: check }
	  , mousewheel: { base: /Firefox/.test(navigator.userAgent) ? 'DOMMouseScroll' : 'mousewheel' }
	}
}());

var commonProps = str2arr('altKey attrChange attrName bubbles cancelable ctrlKey currentTarget ' +
	'detail eventPhase getModifierState isTrusted metaKey relatedNode relatedTarget shiftKey '  +
	'srcElement target timeStamp type view which propertyName');
var mouseProps = commonProps.concat(str2arr('button buttons clientX clientY dataTransfer '      +
	'fromElement offsetX offsetY pageX pageY screenX screenY toElement'));
var mouseWheelProps = mouseProps.concat(str2arr('wheelDelta wheelDeltaX wheelDeltaY wheelDeltaZ ' +
'axis')); // 'axis' is FF specific
var keyProps = commonProps.concat(str2arr('char charCode key keyCode keyIdentifier '          +
	'keyLocation location'));
var textProps = commonProps.concat(str2arr('data'));
var touchProps = commonProps.concat(str2arr('touches targetTouches changedTouches scale rotation'));
var messageProps = commonProps.concat(str2arr('data origin source'));
var stateProps = commonProps.concat(str2arr('state'));
var overOutRegex = /over|out/;
// some event types need special handling and some need special properties, do that all here
var typeFixers = [
	{ // key events
		reg: /key/i
		, fix: (event, newEvent) => {
			newEvent.keyCode = event.keyCode || event.which
			return keyProps
		}
	},
	{ // mouse events
		reg: /click|mouse(?!(.*wheel|scroll))|menu|drag|drop/i
		, fix: (event, newEvent, type) => {
			newEvent.rightClick = event.which === 3 || event.button === 2;
			newEvent.pos = { x: 0, y: 0 };
			if (event.pageX || event.pageY) {
				newEvent.clientX = event.pageX
				newEvent.clientY = event.pageY
			} else if (event.clientX || event.clientY) {
				newEvent.clientX = event.clientX + doc.body.scrollLeft + root.scrollLeft
				newEvent.clientY = event.clientY + doc.body.scrollTop + root.scrollTop
			}
			if (overOutRegex.test(type)) {
				newEvent.relatedTarget = event.relatedTarget
				|| event[(type == 'mouseover' ? 'from' : 'to') + 'Element']
			}
			return mouseProps
		}
	},
	{ // mouse wheel events
		reg: /mouse.*(wheel|scroll)/i
		, fix: () => { return mouseWheelProps }
	}, 
	{ // TextEvent
		reg: /^text/i
		, fix: () => { return textProps }
	},
	{ // touch and gesture events
		reg: /^touch|^gesture/i
		, fix: () => { return touchProps }
	},
	{ // message events
		reg: /^message$/i
		, fix: () => { return messageProps }
	},
	{ // popstate events
		reg: /^popstate$/i
		, fix: () => { return stateProps }
	},
	{ // everything else
		reg: /.*/
		, fix: () => { return commonProps }
	}
];

//==============================================================================
//
// Functions
//
//==============================================================================

var targetElement = (element, isNative) => {
	return !W3C_MODEL && !isNative && (element === doc || element === win) ? root : element;
}
var wrappedHandler = (element, fn, condition, args) => {
	var call = (event, eargs) => {
		return fn.apply(element, args ? slice.call(eargs, event ? 0 : 1).concat(args) : eargs)
	}
	var findTarget = (event, eventElement) => {
		return fn.__beanDel ? fn.__beanDel.ft(event.target, element) : eventElement
	}
	var handler = condition
	? function (event) {
		var target = findTarget(event, this) // deleated event
		if (condition.apply(target, arguments)) {
			if (event)
				event.currentTarget = target
			return call(event, arguments)
		}
	}
	: function (event) {
		if (fn.__beanDel)
			event = event.clone(findTarget(event)) // delegated event, fix the fix
		return call(event, arguments)
	}
	handler.__beanDel = fn.__beanDel
	return handler
}

//==============================================================================
//
// Classes
//
//==============================================================================

class Event {
	constructor(event, element, isNative) {
		if (!arguments.length) return;

		event = event || ((element.ownerDocument || element.document || element).parentWindow || win).event;
		this.originalEvent = event;
		this.isNative = isNative;
		this.isBean = true;

		if (!event) return;

		let type = event.type;;
		let target = event.target || event.srcElement;
		let i, l, p, props, fixer;
		let typeFixerMap = {};

		this.target = target && target.nodeType === 3 ? target.parentNode : target

		if (isNative) { // we only need basic augmentation on custom events, the rest expensive & pointless
			fixer = typeFixerMap[type];
			if (!fixer) { // haven't encountered this event type before, map a fixer function for it
				for (i = 0, l = typeFixers.length; i < l; i++) {
					if (typeFixers[i].reg.test(type)) { // guaranteed to match at least one, last is .*
						typeFixerMap[type] = fixer = typeFixers[i].fix;
						break
					}
				}
			}

			props = fixer(event, this, type);
			for (i = props.length; i--;) {
				if (!((p = props[i]) in this) && p in event) this[p] = event[p];
			}
		}
	}

	preventDefault() {
		if (this.originalEvent.preventDefault)
			this.originalEvent.preventDefault();
		else 
			this.originalEvent.returnValue = false;
	}
	
	stopPropagation() {
		if (this.originalEvent.stopPropagation)
			this.originalEvent.stopPropagation();
		else 
			this.originalEvent.cancelBubble = true;
	}

	stop() {
		this.preventDefault();
		this.stopPropagation();
		this.stopped = true;
	}

	stopImmediatePropagation() {
		if (this.originalEvent.stopImmediatePropagation)
			this.originalEvent.stopImmediatePropagation();
		this.isImmediatePropagationStopped = () => { return true }
	}

	isImmediatePropagationStopped() {
		return this.originalEvent.isImmediatePropagationStopped && this.originalEvent.isImmediatePropagationStopped()
	}

	clone(currentTarget) {
		var ne = new Event(this, this.element, this.isNative);
		ne.currentTarget = currentTarget;
		return ne;
	}
}

class RegEntry {
	constructor(element, type, handler, original, namespaces, args, root) {
		var customType = customEvents[type]
		var isNative = false;
		if (type == 'unload') {
			// self clean-up
			handler = once(removeListener, element, type, handler, original)
		}

		if (customType) {
			if (customType.condition) {
				handler = wrappedHandler(element, handler, customType.condition, args)
			}
			type = customType.base || type
		}

		this.isNative      = isNative = nativeEvents[type] && !!element[eventSupport]
		this.customType    = !W3C_MODEL && !isNative && type
		this.element       = element
		this.type          = type
		this.original      = original
		this.namespaces    = namespaces
		this.eventType     = W3C_MODEL || isNative ? type : 'propertychange'
		this.target        = targetElement(element, isNative)
		this[eventSupport] = !!this.target[eventSupport]
		this.root          = root
		this.handler       = wrappedHandler(element, handler, null, args)
	}

	inNamespaces(checkNamespaces) {
		var i, j, c = 0;
		if (!checkNamespaces) return true;
		if (!this.namespaces) return false;
		for (i = checkNamespaces.length; i--;) {
			for (j = this.namespaces.length; j--;) {
				if (checkNamespaces[i] == this.namespaces[j])
					c++;
			}
		}
		return checkNamespaces.length === c;
	}

	matches(checkElement, checkOriginal, checkHandler) {
		return this.element === checkElement &&
			(!checkOriginal || this.original === checkOriginal) &&
			(!checkHandler || this.handler === checkHandler)
	}
}

class Registry {
	constructor() {
        // our map stores arrays by event type, just because it's better than storing
        // everything in a single array.
        // uses '$' as a prefix for the keys for safety and 'r' as a special prefix for
        // rootListeners so we can look them up fast
		this.map = {};
	}

	// generic functional search of our registry for matching listeners,
	// `fn` returns false to break out of the loop
	forAll(element, type, original, handler, root, fn) {
		let pfx = root ? 'r' : '$'
		if (!type || type == '*') {
			// search the whole registry
			for (let t in this.map) {
				if (t.charAt(0) == pfx) {
					this.forAll(element, t.substr(1), original, handler, root, fn)
				}
			}
		} else {
			let i = 0, l, list = this.map[pfx + type], all = element == '*';
			if (!list) return;
			for (l = list.length; i < l; i++) {
				if ((all || list[i].matches(element, original, handler)) && !fn(list[i], list, i, type))
					return;
			}
		}
	}

	has(element, type, original, root) {
		// we're not using forAll here simply because it's a bit slower and this
		// needs to be fast
		var i, list = this.map[(root ? 'r' : '$') + type];
		if (list) {
			for (i = list.length; i--;) {
				if (!list[i].root && list[i].matches(element, original, null))
					return true;
			}
		}
		return false;
	}

	get(element, type, original, root) {
		var entries = [];
		this.forAll(element, type, original, null, root, (entry) => {
			return entries.push(entry);
		})
		return entries;
	}

	put(entry) {
		var has = !entry.root && !this.has(entry.element, entry.type, null, false);
		var key = (entry.root ? 'r' : '$') + entry.type;
		(this.map[key] || (this.map[key] = [])).push(entry);
		return has;
	}
	del(entry) {
		this.forAll(entry.element, entry.type, null, entry.handler, entry.root, (entry, list, i) => {
			list.splice(i, 1);
			entry.removed = true;
			if (list.length === 0) delete this.map[(entry.root ? 'r' : '$') + entry.type]
				return false;
		});
	}

	entries() {
		var t, entries = [];
		for (t in this.map) {
			if (t.charAt(0) == '$')
				entries = entries.concat(this.map[t]);
		}
		return entries;
	}
}

class EventSource {
	addListener(event, func) {
		events.on(this, event, func);
	}

	removeListener(event, func) {
		events.off(this, event, func);
	}
	
	fire(event, args) {
		events.fire(this, event, args);
	}
}

//==============================================================================
//
// Variables
//
//==============================================================================

var registry = new Registry();

var rootListener = function(event, type) {
	if (!W3C_MODEL && type && event && event.propertyName != '_on' + type)
		return;

	var listeners = registry.get(this, type || event.type, null, false);
	var l = listeners.length;
	var i = 0;

	event = new Event(event, this, true);
	if (type)
		event.type = type;

	// iterate through all handlers registered for this type, calling them unless they have
	// been removed by a previous handler or stopImmediatePropagation() has been called
	for(let item of listeners) {
		if(event.isImmediatePropagationStopped()) {
			break;
		}
		if(!item.removed) {
			item.handler.call(this, event);
		}
	}
};

var listener = W3C_MODEL
? (element, type, add) => {
	// new browsers
	element[add ? addEvent : removeEvent](type, rootListener, false)
}
: (element, type, add, custom) => {
	// IE8 and below, use attachEvent/detachEvent and we have to piggy-back propertychange events
	// to simulate event bubbling etc.
	var entry;
	if (add) {
		registry.put(entry = new RegEntry(
			element
			, custom || type
			, (event) => { // handler
				rootListener.call(element, event, custom)
			}
			, rootListener
			, null
			, null
			, true // is root
		));
		if (custom && element['_on' + custom] == null)
			element['_on' + custom] = 0;
		entry.target.attachEvent('on' + entry.eventType, entry.handler)
	} 
	else {
		entry = registry.get(element, custom || type, rootListener, true)[0];
		if (entry) {
			entry.target.detachEvent('on' + entry.eventType, entry.handler)
			registry.del(entry)
		}
	}
}

var once = (rm, element, type, fn, originalFn) => {
	// wrap the handler in a handler that does a remove as well
	return () => {
		fn.apply(this, arguments)
		rm(element, type, originalFn)
	}
}

var removeListener = (element, orgType, handler, namespaces) => {
	let type = orgType && orgType.replace(nameRegex, '');
	let handlers = registry.get(element, type, null, false);
	let removed  = {};

	for(let item of handlers) {
		if((!handler || item.original === handler) && item.inNamespaces(namespaces)) {
			// TODO: this is problematic, we have a registry.get() and registry.del() that
			// both do registry searches so we waste cycles doing this. Needs to be rolled into
			// a single registry.forAll(fn) that removes while finding, but the catch is that
			// we'll be splicing the arrays that we're iterating over. Needs extra tests to
			// make sure we don't screw it up. @rvagg
			registry.del(item);
			if (!removed[item.eventType] && item[eventSupport]) {
				removed[item.eventType] = { t: item.eventType, c: item.type };
			}
		}
	}

	// check each type/element for removed listeners and remove the rootListener where it's no longer needed
	for (let i in removed) {
		if (!registry.has(element, removed[i].t, null, false)) {
			// last listener of this type, remove the rootListener
			listener(element, removed[i].t, false, removed[i].c)
		}
	}
}

var delegate = (selector, fn) => {
	//TODO: findTarget (therefore $) is called twice, once for match and once for
	// setting e.currentTarget, fix this so it's only needed once
	var findTarget = (target, root) => {
		var i, array = isString(selector) ? selectorEngine(selector, root) : selector
		for (; target && target !== root; target = target.parentNode) {
			for (i = array.length; i--;) {
				if (array[i] === target)
					return target;
			}
		}
	}

	var handler = (e) => {
		var match = findTarget(e.target, this)
		if (match)
			fn.apply(match, arguments)
	}

	// __beanDel isn't pleasant but it's a private function, not exposed outside of Bean
	handler.__beanDel = {
		ft : findTarget // attach it here for customEvents to use too
		, selector : selector
	}
	return handler;
}

var fireListener = W3C_MODEL ? (isNative, type, element) => {
	// modern browsers, do a proper dispatchEvent()
	var evt = doc.createEvent(isNative ? 'HTMLEvents' : 'UIEvents');
	evt[isNative ? 'initEvent' : 'initUIEvent'](type, true, true, win, 1);
	element.dispatchEvent(evt);
} 
: (isNative, type, element) => {
	// old browser use onpropertychange, just increment a custom property to trigger the event
	element = targetElement(element, isNative);
	isNative ? element.fireEvent('on' + type, doc.createEventObject()) : element['_on' + type]++;
}


/**
 * off(element[, eventType(s)[, handler ]])
 */
var off = (element, typeSpec, fn) => {
	var isTypeStr = isString(typeSpec);
	var k, type, namespaces, i;

	if (isTypeStr && typeSpec.indexOf(' ') > 0) {
		// off(el, 't1 t2 t3', fn) or off(el, 't1 t2 t3')
		for(let item of str2arr(typeSpec)) {
			off(element, item, fn)
		}
		return element;
	}

	type = isTypeStr && typeSpec.replace(nameRegex, '');
	if (type && customEvents[type])
		type = customEvents[type].base;

	if (!typeSpec || isTypeStr) {
		// off(el) or off(el, t1.ns) or off(el, .ns) or off(el, .ns1.ns2.ns3)
		if (namespaces = isTypeStr && typeSpec.replace(namespaceRegex, ''))
			namespaces = str2arr(namespaces, '.');
		removeListener(element, type, fn, namespaces);
	}
	else if (isFunction(typeSpec)) {
		// off(el, fn)
		removeListener(element, null, typeSpec);
	}
	else {
		// off(el, { t1: fn1, t2, fn2 })
		for (k in typeSpec) {
			if (typeSpec.hasOwnProperty(k))
				off(element, k, typeSpec[k]);
		}
	}

	return element;
}

/**
* on(element, eventType(s)[, selector], handler[, args ])
*/
var on = (element, events, selector, fn) => {
	var originalFn, type, types, i, args, entry, first;

	//TODO: the undefined check means you can't pass an 'args' argument, fix this perhaps?
	if (selector === undefined && typeof events == 'object') {
		//TODO: this can't handle delegated events
		for (type in events) {
			if (events.hasOwnProperty(type)) {
				on.call(this, element, type, events[type])
			}
		}
		return;
	}

	if (!isFunction(selector)) {
		// delegated event
		originalFn = fn;
		args       = slice.call(arguments, 4);
		fn         = delegate(selector, originalFn, selectorEngine);
	}
	else {
		args       = slice.call(arguments, 3);
		fn         = originalFn = selector;
	}

	types = str2arr(events)

	// special case for one(), wrap in a self-removing handler
	if (this === ONE) {
		fn = once(off, element, events, fn, originalFn);
	}

	for (i = types.length; i--;) {
		// add new handler to the registry and check if it's the first for this element/type
		first = registry.put(entry = new RegEntry(
			element
			, types[i].replace(nameRegex, '') // event type
			, fn
			, originalFn
			, str2arr(types[i].replace(namespaceRegex, ''), '.') // namespaces
			, args
			, false // not root
		));
		if (entry[eventSupport] && first) {
			// first event of this type on this element, add root listener
			listener(element, entry.eventType, true, entry.customType);
		}
	}

	return element;
}

/**
 * one(element, eventType(s)[, selector], handler[, args ])
 */
var one = () => {
	return on.apply(ONE, arguments);
}

/**
 * fire(element, eventType(s)[, args ])
 *
 * The optional 'args' argument must be an array, if no 'args' argument is provided
 * then we can use the browser's DOM event system, otherwise we trigger handlers manually
 */
var fire = (element, type, args) => {
	var types = str2arr(type);
	var i, j, l, names, handlers;

	for (i = types.length; i--;) {
		type = types[i].replace(nameRegex, '');
		if (names = types[i].replace(namespaceRegex, ''))
			names = str2arr(names, '.');

		if (!names && !args && element[eventSupport]) {
			fireListener(nativeEvents[type], type, element)
		}
		else {
			// non-native event, either because of a namespace, arguments or a non DOM element
			// iterate over all listeners and manually 'fire'
			handlers = registry.get(element, type, null, false)
			args = [false].concat(args)
			for(let handler of handlers) {
				if (handler.inNamespaces(names)) {
					handler.handler.apply(element, args);
				}
			}
		}
	}
	return element;
}

var clone = (element, from, type) => {
	var handlers = registry.get(from, type, null, false)
	var l = handlers.length;
	var i = 0;
	var args, beanDel;

	for(let handler of handlers) {
		if (handler.original) {
			args = [ element, handler.type ]
			if (beanDel = handler.handler.__beanDel)
				args.push(beanDel.selector)
			args.push(handler.original)
			on.apply(null, args)
		}
	}
	return element;
}

var events = {
	  'on'                : on
	, 'one'               : one
	, 'off'               : off
	, 'remove'            : off
	, 'clone'             : clone
	, 'fire'              : fire
	, 'Event'             : Event
}

// for IE, clean up on unload to avoid leaks
if (win.attachEvent) {
	var cleanup = () => {
		var i, entries = registry.entries()
		for (i in entries) {
			if (entries[i].type && entries[i].type !== 'unload')
				off(entries[i].element, entries[i].type);
		}
		win.detachEvent('onunload', cleanup)
		win.CollectGarbage && win.CollectGarbage()
	}
	win.attachEvent('onunload', cleanup)
}

lilium.global('events', events);
lilium.events = events;

provides([EventSource], 'core');

})();
