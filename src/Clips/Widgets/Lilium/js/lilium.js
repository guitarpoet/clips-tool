//==============================================================================
//
// The fundation part, must at the very first of core, so name this using 0
//
// @author Jack
// @version 1.0
// @date Thu Jun 25 23:12:50 2015
//
//==============================================================================

"use strict";

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

+(function () {
	var Lilium = (function () {
		function Lilium() {
			_classCallCheck(this, Lilium);
		}

		_createClass(Lilium, [{
			key: "sourceMap",
			value: function sourceMap() {
				if (this.inNode()) {
					require("source-map-support").install();
				}
			}
		}, {
			key: "getCookie",
			value: function getCookie(name) {
				var value = "; " + document.cookie;
				var parts = this.split(value, "; " + name + "=");
				if (parts.length == 2) return this.split(parts.pop(), ";").shift();
			}
		}, {
			key: "split",
			value: function split(s, sep) {
				return s.split(sep || " ");
			}
		}, {
			key: "isString",
			value: function isString(o) {
				return typeof o === "string" || o instanceof String;
			}
		}, {
			key: "isObject",
			value: function isObject(o) {
				return o && typeof o === "object";
			}
		}, {
			key: "inNode",
			value: function inNode() {
				return typeof GLOBAL == "object";
			}
		}, {
			key: "global",
			value: function global(name, value) {
				var w = null;
				if (typeof window === "undefined") {
					// Add global support for nodejs
					w = GLOBAL;
				} else {
					w = window;
				}

				if (name) {
					if (typeof value === "undefined") {
						return w[name];
					} else {
						w[name] = value;
					}
				}
				return w;
			}
		}, {
			key: "local",
			value: function local(name, func) {
				var f = this.global(name);
				if (typeof f === "function") {
					f.alias = name;
					return f;
				}
				if (func) func.alias = name;
				return func;
			}
		}, {
			key: "defineIfNotExists",

			/**
    * Test if the function is exists, if not exists then define it
    * NOTE: This will define the function into the global environment
    */
			value: function defineIfNotExists(name, func) {
				var f = this.global(name);
				if (typeof f === "function") {
					f.alias = name;
					return f;
				}
				this.global(name, func);
				if (func) func.alias = name;
				return func;
			}
		}, {
			key: "removeElement",
			value: function removeElement(a, e) {
				if (this.isArray(a)) {
					var index = a.indexOf(e);
					if (index != -1) return a.splice(index, 1);
				}
				return [];
			}
		}, {
			key: "clone",
			value: function clone(a) {
				if (this.isArray(a)) return a.slice(0);
				if (this.isObject(a)) {
					var ret = {};
					for (var p in a) {
						ret[p] = a[p];
					}
					return ret;
				}
				return null;
			}
		}, {
			key: "isArray",
			value: function isArray(o) {
				return Object.prototype.toString.call(o) === "[object Array]";
			}
		}, {
			key: "getName",
			value: function getName(o) {
				if (o) {
					if (typeof o.alias !== "undefined") {
						return o.alias;
					}
					if (typeof o.name !== "undefined") {
						return o.name;
					}
					var ret = o.toString();
					ret = ret.substr("function ".length);
					ret = ret.substr(0, ret.indexOf("("));
					return ret;
				}
				return null;
			}
		}]);

		return Lilium;
	})();

	var lilium = new Lilium();

	lilium.sourceMap(); // Map the source codes to ease the debug process.

	lilium.defineIfNotExists("def", function (name, func) {
		return lilium.defineIfNotExists(name, func);
	});

	def("lilium", lilium);
	def("require", function () {}); // TODO: Define require if no require is defined
	def("embed", function (module) {
		if (lilium[module]) {
			return lilium[module];
		}

		var m = require(module);
		if (m) {
			return m;
		}
		return null;
	});

	/**
  * Provide the widget and functions for module. This function will direct provide the Classes to the global lilium namespace
  * And if in browser context, won't provide any access to these exports other than global lilium namespace.
  */
	def("provides", function (widgets, module, global) {
		var e = null;

		if (!module) // lilium widgets are the default module
			module = "widgets";

		if (typeof exports !== "undefined") {
			// Prefer nodejs environment
			e = exports;
		} else if (typeof window !== "undefined") {
			e = {};
		}

		if (lilium.isArray(widgets)) {
			var _iteratorNormalCompletion = true;
			var _didIteratorError = false;
			var _iteratorError = undefined;

			try {
				for (var _iterator = widgets[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
					var widget = _step.value;

					e[lilium.getName(widget)] = widget;
				}
			} catch (err) {
				_didIteratorError = true;
				_iteratorError = err;
			} finally {
				try {
					if (!_iteratorNormalCompletion && _iterator["return"]) {
						_iterator["return"]();
					}
				} finally {
					if (_didIteratorError) {
						throw _iteratorError;
					}
				}
			}
		} else {
			e[lilium.getName(widgets)] = widgets;
		}

		lilium[module] = lilium[module] || {};

		if (global) {
			var g = lilium.global(module) || {};
		}

		for (var k in e) {
			lilium[module][k] = e[k];
			if (global) {
				g[k] = e[k];
			}
		}

		if (global) {
			lilium.global(module, g);
		}
	});

	provides([Lilium, def], "core");
})();
//==============================================================================
//
// The events foundation, will provides the crossplatform event support
//
// @author Jack
// @version 1.0
// @date Fri Jun 26 17:17:57 2015
//
//==============================================================================

'use strict';

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

+(function () {
	var _this2 = this,
	    _arguments = arguments;

	//==============================================================================
	//
	// Util Functions
	//
	//==============================================================================
	var slice = Array.prototype.slice;
	var str2arr = function str2arr(s, d) {
		return s.split(d || ' ');
	};
	var isString = function isString(o) {
		return typeof o == 'string';
	};
	var isFunction = function isFunction(o) {
		return typeof o == 'function';
	};
	var selectorEngine = lilium.local('selectorEngine', function (s, r) {
		return r.querySelectorAll(s);
	});

	//==============================================================================
	//
	// Definitions
	//
	//==============================================================================
	var win = lilium.global();
	var navigator = typeof navigator !== 'undefined' ? navigator : {};
	var namespaceRegex = /[^\.]*(?=\..*)\.|.*/;
	var nameRegex = /\..*/;
	var addEvent = 'addEventListener';
	var removeEvent = 'removeEventListener';
	var doc = typeof document !== 'undefined' ? document : {};
	var root = doc.documentElement || {};
	var W3C_MODEL = root[addEvent];
	var eventSupport = W3C_MODEL ? addEvent : 'attachEvent';
	var ONE = {}; // singleton for quick matching making add() do one()

	var standardNativeEvents = 'click dblclick mouseup mousedown contextmenu ' + // mouse buttons
	'mousewheel mousemultiwheel DOMMouseScroll ' + // mouse wheel
	'mouseover mouseout mousemove selectstart selectend ' + // mouse movement
	'keydown keypress keyup ' + // keyboard
	'orientationchange ' + // mobile
	'focus blur change reset select submit ' + // form elements
	'load unload beforeunload resize move DOMContentLoaded ' + // window
	'readystatechange message ' + // window
	'error abort scroll '; // misc

	// element.fireEvent('onXYZ'... is not forgiving if we try to fire an event
	// that doesn't actually exist, so make sure we only do these on newer browsers
	var w3cNativeEvents = 'show ' + // mouse buttons
	'input invalid ' + // form elements
	'touchstart touchmove touchend touchcancel ' + // touch
	'gesturestart gesturechange gestureend ' + // gesture
	'textinput ' + // TextEvent
	'readystatechange pageshow pagehide popstate ' + // window
	'hashchange offline online ' + // window
	'afterprint beforeprint ' + // printing
	'dragstart dragenter dragover dragleave drag drop dragend ' + // dnd
	'loadstart progress suspend emptied stalled loadmetadata ' + // media
	'loadeddata canplay canplaythrough playing waiting seeking ' + // media
	'seeked ended durationchange timeupdate play pause ratechange ' + // media
	'volumechange cuechange ' + // media
	'checking noupdate downloading cached updateready obsolete '; // appcache

	var nativeEvents = (function (hash, events, i) {
		for (i = 0; i < events.length; i++) events[i] && (hash[events[i]] = 1);
		return hash;
	})({}, str2arr(standardNativeEvents + (W3C_MODEL ? w3cNativeEvents : '')));

	var customEvents = (function () {
		var isAncestor = 'compareDocumentPosition' in root ? function (element, container) {
			return container.compareDocumentPosition && (container.compareDocumentPosition(element) & 16) === 16;
		} : 'contains' in root ? function (element, container) {
			container = container.nodeType === 9 || container === window ? root : container;
			return container !== element && container.contains(element);
		} : function (element, container) {
			while (element = element.parentNode) if (element === container) return 1;
			return 0;
		};

		var check = function check(event) {
			var related = event.relatedTarget;
			return !related ? related == null : related !== this && related.prefix !== 'xul' && !/document/.test(this.toString()) && !isAncestor(related, this);
		};

		return {
			mouseenter: { base: 'mouseover', condition: check },
			mouseleave: { base: 'mouseout', condition: check },
			mousewheel: { base: /Firefox/.test(navigator.userAgent) ? 'DOMMouseScroll' : 'mousewheel' }
		};
	})();

	var commonProps = str2arr('altKey attrChange attrName bubbles cancelable ctrlKey currentTarget ' + 'detail eventPhase getModifierState isTrusted metaKey relatedNode relatedTarget shiftKey ' + 'srcElement target timeStamp type view which propertyName');
	var mouseProps = commonProps.concat(str2arr('button buttons clientX clientY dataTransfer ' + 'fromElement offsetX offsetY pageX pageY screenX screenY toElement'));
	var mouseWheelProps = mouseProps.concat(str2arr('wheelDelta wheelDeltaX wheelDeltaY wheelDeltaZ ' + 'axis')); // 'axis' is FF specific
	var keyProps = commonProps.concat(str2arr('char charCode key keyCode keyIdentifier ' + 'keyLocation location'));
	var textProps = commonProps.concat(str2arr('data'));
	var touchProps = commonProps.concat(str2arr('touches targetTouches changedTouches scale rotation'));
	var messageProps = commonProps.concat(str2arr('data origin source'));
	var stateProps = commonProps.concat(str2arr('state'));
	var overOutRegex = /over|out/;
	// some event types need special handling and some need special properties, do that all here
	var typeFixers = [{ // key events
		reg: /key/i,
		fix: function fix(event, newEvent) {
			newEvent.keyCode = event.keyCode || event.which;
			return keyProps;
		}
	}, { // mouse events
		reg: /click|mouse(?!(.*wheel|scroll))|menu|drag|drop/i,
		fix: function fix(event, newEvent, type) {
			newEvent.rightClick = event.which === 3 || event.button === 2;
			newEvent.pos = { x: 0, y: 0 };
			if (event.pageX || event.pageY) {
				newEvent.clientX = event.pageX;
				newEvent.clientY = event.pageY;
			} else if (event.clientX || event.clientY) {
				newEvent.clientX = event.clientX + doc.body.scrollLeft + root.scrollLeft;
				newEvent.clientY = event.clientY + doc.body.scrollTop + root.scrollTop;
			}
			if (overOutRegex.test(type)) {
				newEvent.relatedTarget = event.relatedTarget || event[(type == 'mouseover' ? 'from' : 'to') + 'Element'];
			}
			return mouseProps;
		}
	}, { // mouse wheel events
		reg: /mouse.*(wheel|scroll)/i,
		fix: function fix() {
			return mouseWheelProps;
		}
	}, { // TextEvent
		reg: /^text/i,
		fix: function fix() {
			return textProps;
		}
	}, { // touch and gesture events
		reg: /^touch|^gesture/i,
		fix: function fix() {
			return touchProps;
		}
	}, { // message events
		reg: /^message$/i,
		fix: function fix() {
			return messageProps;
		}
	}, { // popstate events
		reg: /^popstate$/i,
		fix: function fix() {
			return stateProps;
		}
	}, { // everything else
		reg: /.*/,
		fix: function fix() {
			return commonProps;
		}
	}];

	//==============================================================================
	//
	// Functions
	//
	//==============================================================================

	var targetElement = function targetElement(element, isNative) {
		return !W3C_MODEL && !isNative && (element === doc || element === win) ? root : element;
	};
	var wrappedHandler = function wrappedHandler(element, fn, condition, args) {
		var call = function call(event, eargs) {
			return fn.apply(element, args ? slice.call(eargs, event ? 0 : 1).concat(args) : eargs);
		};
		var findTarget = function findTarget(event, eventElement) {
			return fn.__beanDel ? fn.__beanDel.ft(event.target, element) : eventElement;
		};
		var handler = condition ? function (event) {
			var target = findTarget(event, this); // deleated event
			if (condition.apply(target, arguments)) {
				if (event) event.currentTarget = target;
				return call(event, arguments);
			}
		} : function (event) {
			if (fn.__beanDel) event = event.clone(findTarget(event)); // delegated event, fix the fix
			return call(event, arguments);
		};
		handler.__beanDel = fn.__beanDel;
		return handler;
	};

	//==============================================================================
	//
	// Classes
	//
	//==============================================================================

	var Event = (function () {
		function Event(event, element, isNative) {
			_classCallCheck(this, Event);

			if (!arguments.length) return;

			event = event || ((element.ownerDocument || element.document || element).parentWindow || win).event;
			this.originalEvent = event;
			this.isNative = isNative;
			this.isBean = true;

			if (!event) return;

			var type = event.type;;
			var target = event.target || event.srcElement;
			var i = undefined,
			    l = undefined,
			    p = undefined,
			    props = undefined,
			    fixer = undefined;
			var typeFixerMap = {};

			this.target = target && target.nodeType === 3 ? target.parentNode : target;

			if (isNative) {
				// we only need basic augmentation on custom events, the rest expensive & pointless
				fixer = typeFixerMap[type];
				if (!fixer) {
					// haven't encountered this event type before, map a fixer function for it
					for (i = 0, l = typeFixers.length; i < l; i++) {
						if (typeFixers[i].reg.test(type)) {
							// guaranteed to match at least one, last is .*
							typeFixerMap[type] = fixer = typeFixers[i].fix;
							break;
						}
					}
				}

				props = fixer(event, this, type);
				for (i = props.length; i--;) {
					if (!((p = props[i]) in this) && p in event) this[p] = event[p];
				}
			}
		}

		_createClass(Event, [{
			key: 'preventDefault',
			value: function preventDefault() {
				if (this.originalEvent.preventDefault) this.originalEvent.preventDefault();else this.originalEvent.returnValue = false;
			}
		}, {
			key: 'stopPropagation',
			value: function stopPropagation() {
				if (this.originalEvent.stopPropagation) this.originalEvent.stopPropagation();else this.originalEvent.cancelBubble = true;
			}
		}, {
			key: 'stop',
			value: function stop() {
				this.preventDefault();
				this.stopPropagation();
				this.stopped = true;
			}
		}, {
			key: 'stopImmediatePropagation',
			value: function stopImmediatePropagation() {
				if (this.originalEvent.stopImmediatePropagation) this.originalEvent.stopImmediatePropagation();
				this.isImmediatePropagationStopped = function () {
					return true;
				};
			}
		}, {
			key: 'isImmediatePropagationStopped',
			value: function isImmediatePropagationStopped() {
				return this.originalEvent.isImmediatePropagationStopped && this.originalEvent.isImmediatePropagationStopped();
			}
		}, {
			key: 'clone',
			value: function clone(currentTarget) {
				var ne = new Event(this, this.element, this.isNative);
				ne.currentTarget = currentTarget;
				return ne;
			}
		}]);

		return Event;
	})();

	var RegEntry = (function () {
		function RegEntry(element, type, handler, original, namespaces, args, root) {
			_classCallCheck(this, RegEntry);

			var customType = customEvents[type];
			var isNative = false;
			if (type == 'unload') {
				// self clean-up
				handler = once(removeListener, element, type, handler, original);
			}

			if (customType) {
				if (customType.condition) {
					handler = wrappedHandler(element, handler, customType.condition, args);
				}
				type = customType.base || type;
			}

			this.isNative = isNative = nativeEvents[type] && !!element[eventSupport];
			this.customType = !W3C_MODEL && !isNative && type;
			this.element = element;
			this.type = type;
			this.original = original;
			this.namespaces = namespaces;
			this.eventType = W3C_MODEL || isNative ? type : 'propertychange';
			this.target = targetElement(element, isNative);
			this[eventSupport] = !!this.target[eventSupport];
			this.root = root;
			this.handler = wrappedHandler(element, handler, null, args);
		}

		_createClass(RegEntry, [{
			key: 'inNamespaces',
			value: function inNamespaces(checkNamespaces) {
				var i,
				    j,
				    c = 0;
				if (!checkNamespaces) return true;
				if (!this.namespaces) return false;
				for (i = checkNamespaces.length; i--;) {
					for (j = this.namespaces.length; j--;) {
						if (checkNamespaces[i] == this.namespaces[j]) c++;
					}
				}
				return checkNamespaces.length === c;
			}
		}, {
			key: 'matches',
			value: function matches(checkElement, checkOriginal, checkHandler) {
				return this.element === checkElement && (!checkOriginal || this.original === checkOriginal) && (!checkHandler || this.handler === checkHandler);
			}
		}]);

		return RegEntry;
	})();

	var Registry = (function () {
		function Registry() {
			_classCallCheck(this, Registry);

			// our map stores arrays by event type, just because it's better than storing
			// everything in a single array.
			// uses '$' as a prefix for the keys for safety and 'r' as a special prefix for
			// rootListeners so we can look them up fast
			this.map = {};
		}

		_createClass(Registry, [{
			key: 'forAll',

			// generic functional search of our registry for matching listeners,
			// `fn` returns false to break out of the loop
			value: function forAll(element, type, original, handler, root, fn) {
				var pfx = root ? 'r' : '$';
				if (!type || type == '*') {
					// search the whole registry
					for (var t in this.map) {
						if (t.charAt(0) == pfx) {
							this.forAll(element, t.substr(1), original, handler, root, fn);
						}
					}
				} else {
					var i = 0,
					    l = undefined,
					    list = this.map[pfx + type],
					    all = element == '*';
					if (!list) return;
					for (l = list.length; i < l; i++) {
						if ((all || list[i].matches(element, original, handler)) && !fn(list[i], list, i, type)) return;
					}
				}
			}
		}, {
			key: 'has',
			value: function has(element, type, original, root) {
				// we're not using forAll here simply because it's a bit slower and this
				// needs to be fast
				var i,
				    list = this.map[(root ? 'r' : '$') + type];
				if (list) {
					for (i = list.length; i--;) {
						if (!list[i].root && list[i].matches(element, original, null)) return true;
					}
				}
				return false;
			}
		}, {
			key: 'get',
			value: function get(element, type, original, root) {
				var entries = [];
				this.forAll(element, type, original, null, root, function (entry) {
					return entries.push(entry);
				});
				return entries;
			}
		}, {
			key: 'put',
			value: function put(entry) {
				var has = !entry.root && !this.has(entry.element, entry.type, null, false);
				var key = (entry.root ? 'r' : '$') + entry.type;
				(this.map[key] || (this.map[key] = [])).push(entry);
				return has;
			}
		}, {
			key: 'del',
			value: function del(entry) {
				var _this = this;

				this.forAll(entry.element, entry.type, null, entry.handler, entry.root, function (entry, list, i) {
					list.splice(i, 1);
					entry.removed = true;
					if (list.length === 0) delete _this.map[(entry.root ? 'r' : '$') + entry.type];
					return false;
				});
			}
		}, {
			key: 'entries',
			value: function entries() {
				var t,
				    entries = [];
				for (t in this.map) {
					if (t.charAt(0) == '$') entries = entries.concat(this.map[t]);
				}
				return entries;
			}
		}]);

		return Registry;
	})();

	var EventSource = (function () {
		function EventSource() {
			_classCallCheck(this, EventSource);
		}

		_createClass(EventSource, [{
			key: 'addListener',
			value: function addListener(event, func) {
				events.on(this, event, func);
			}
		}, {
			key: 'removeListener',
			value: function removeListener(event, func) {
				events.off(this, event, func);
			}
		}, {
			key: 'fire',
			value: function fire(event, args) {
				events.fire(this, event, args);
			}
		}]);

		return EventSource;
	})();

	//==============================================================================
	//
	// Variables
	//
	//==============================================================================

	var registry = new Registry();

	var rootListener = function rootListener(event, type) {
		if (!W3C_MODEL && type && event && event.propertyName != '_on' + type) return;

		var listeners = registry.get(this, type || event.type, null, false);
		var l = listeners.length;
		var i = 0;

		event = new Event(event, this, true);
		if (type) event.type = type;

		// iterate through all handlers registered for this type, calling them unless they have
		// been removed by a previous handler or stopImmediatePropagation() has been called
		var _iteratorNormalCompletion = true;
		var _didIteratorError = false;
		var _iteratorError = undefined;

		try {
			for (var _iterator = listeners[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
				var item = _step.value;

				if (event.isImmediatePropagationStopped()) {
					break;
				}
				if (!item.removed) {
					item.handler.call(this, event);
				}
			}
		} catch (err) {
			_didIteratorError = true;
			_iteratorError = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion && _iterator['return']) {
					_iterator['return']();
				}
			} finally {
				if (_didIteratorError) {
					throw _iteratorError;
				}
			}
		}
	};

	var listener = W3C_MODEL ? function (element, type, add) {
		// new browsers
		element[add ? addEvent : removeEvent](type, rootListener, false);
	} : function (element, type, add, custom) {
		// IE8 and below, use attachEvent/detachEvent and we have to piggy-back propertychange events
		// to simulate event bubbling etc.
		var entry;
		if (add) {
			registry.put(entry = new RegEntry(element, custom || type, function (event) {
				// handler
				rootListener.call(element, event, custom);
			}, rootListener, null, null, true // is root
			));
			if (custom && element['_on' + custom] == null) element['_on' + custom] = 0;
			entry.target.attachEvent('on' + entry.eventType, entry.handler);
		} else {
			entry = registry.get(element, custom || type, rootListener, true)[0];
			if (entry) {
				entry.target.detachEvent('on' + entry.eventType, entry.handler);
				registry.del(entry);
			}
		}
	};

	var once = function once(rm, element, type, fn, originalFn) {
		// wrap the handler in a handler that does a remove as well
		return function () {
			fn.apply(_this2, _arguments);
			rm(element, type, originalFn);
		};
	};

	var removeListener = function removeListener(element, orgType, handler, namespaces) {
		var type = orgType && orgType.replace(nameRegex, '');
		var handlers = registry.get(element, type, null, false);
		var removed = {};

		var _iteratorNormalCompletion2 = true;
		var _didIteratorError2 = false;
		var _iteratorError2 = undefined;

		try {
			for (var _iterator2 = handlers[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
				var item = _step2.value;

				if ((!handler || item.original === handler) && item.inNamespaces(namespaces)) {
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
		} catch (err) {
			_didIteratorError2 = true;
			_iteratorError2 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion2 && _iterator2['return']) {
					_iterator2['return']();
				}
			} finally {
				if (_didIteratorError2) {
					throw _iteratorError2;
				}
			}
		}

		// check each type/element for removed listeners and remove the rootListener where it's no longer needed
		for (var i in removed) {
			if (!registry.has(element, removed[i].t, null, false)) {
				// last listener of this type, remove the rootListener
				listener(element, removed[i].t, false, removed[i].c);
			}
		}
	};

	var delegate = function delegate(selector, fn) {
		//TODO: findTarget (therefore $) is called twice, once for match and once for
		// setting e.currentTarget, fix this so it's only needed once
		var findTarget = function findTarget(target, root) {
			var i,
			    array = isString(selector) ? selectorEngine(selector, root) : selector;
			for (; target && target !== root; target = target.parentNode) {
				for (i = array.length; i--;) {
					if (array[i] === target) return target;
				}
			}
		};

		var handler = function handler(e) {
			var match = findTarget(e.target, _this2);
			if (match) fn.apply(match, _arguments);
		};

		// __beanDel isn't pleasant but it's a private function, not exposed outside of Bean
		handler.__beanDel = {
			ft: findTarget // attach it here for customEvents to use too
			, selector: selector
		};
		return handler;
	};

	var fireListener = W3C_MODEL ? function (isNative, type, element) {
		// modern browsers, do a proper dispatchEvent()
		var evt = doc.createEvent(isNative ? 'HTMLEvents' : 'UIEvents');
		evt[isNative ? 'initEvent' : 'initUIEvent'](type, true, true, win, 1);
		element.dispatchEvent(evt);
	} : function (isNative, type, element) {
		// old browser use onpropertychange, just increment a custom property to trigger the event
		element = targetElement(element, isNative);
		isNative ? element.fireEvent('on' + type, doc.createEventObject()) : element['_on' + type]++;
	};

	/**
  * off(element[, eventType(s)[, handler ]])
  */
	var off = function off(element, typeSpec, fn) {
		var isTypeStr = isString(typeSpec);
		var k, type, namespaces, i;

		if (isTypeStr && typeSpec.indexOf(' ') > 0) {
			// off(el, 't1 t2 t3', fn) or off(el, 't1 t2 t3')
			var _iteratorNormalCompletion3 = true;
			var _didIteratorError3 = false;
			var _iteratorError3 = undefined;

			try {
				for (var _iterator3 = str2arr(typeSpec)[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
					var item = _step3.value;

					off(element, item, fn);
				}
			} catch (err) {
				_didIteratorError3 = true;
				_iteratorError3 = err;
			} finally {
				try {
					if (!_iteratorNormalCompletion3 && _iterator3['return']) {
						_iterator3['return']();
					}
				} finally {
					if (_didIteratorError3) {
						throw _iteratorError3;
					}
				}
			}

			return element;
		}

		type = isTypeStr && typeSpec.replace(nameRegex, '');
		if (type && customEvents[type]) type = customEvents[type].base;

		if (!typeSpec || isTypeStr) {
			// off(el) or off(el, t1.ns) or off(el, .ns) or off(el, .ns1.ns2.ns3)
			if (namespaces = isTypeStr && typeSpec.replace(namespaceRegex, '')) namespaces = str2arr(namespaces, '.');
			removeListener(element, type, fn, namespaces);
		} else if (isFunction(typeSpec)) {
			// off(el, fn)
			removeListener(element, null, typeSpec);
		} else {
			// off(el, { t1: fn1, t2, fn2 })
			for (k in typeSpec) {
				if (typeSpec.hasOwnProperty(k)) off(element, k, typeSpec[k]);
			}
		}

		return element;
	};

	/**
 * on(element, eventType(s)[, selector], handler[, args ])
 */
	var on = function on(element, events, selector, fn) {
		var originalFn, type, types, i, args, entry, first;

		//TODO: the undefined check means you can't pass an 'args' argument, fix this perhaps?
		if (selector === undefined && typeof events == 'object') {
			//TODO: this can't handle delegated events
			for (type in events) {
				if (events.hasOwnProperty(type)) {
					on.call(_this2, element, type, events[type]);
				}
			}
			return;
		}

		if (!isFunction(selector)) {
			// delegated event
			originalFn = fn;
			args = slice.call(_arguments, 4);
			fn = delegate(selector, originalFn, selectorEngine);
		} else {
			args = slice.call(_arguments, 3);
			fn = originalFn = selector;
		}

		types = str2arr(events);

		// special case for one(), wrap in a self-removing handler
		if (_this2 === ONE) {
			fn = once(off, element, events, fn, originalFn);
		}

		for (i = types.length; i--;) {
			// add new handler to the registry and check if it's the first for this element/type
			first = registry.put(entry = new RegEntry(element, types[i].replace(nameRegex, ''), // event type
			fn, originalFn, str2arr(types[i].replace(namespaceRegex, ''), '.'), // namespaces
			args, false // not root
			));
			if (entry[eventSupport] && first) {
				// first event of this type on this element, add root listener
				listener(element, entry.eventType, true, entry.customType);
			}
		}

		return element;
	};

	/**
  * one(element, eventType(s)[, selector], handler[, args ])
  */
	var one = function one() {
		return on.apply(ONE, _arguments);
	};

	/**
  * fire(element, eventType(s)[, args ])
  *
  * The optional 'args' argument must be an array, if no 'args' argument is provided
  * then we can use the browser's DOM event system, otherwise we trigger handlers manually
  */
	var fire = function fire(element, type, args) {
		var types = str2arr(type);
		var i, j, l, names, handlers;

		for (i = types.length; i--;) {
			type = types[i].replace(nameRegex, '');
			if (names = types[i].replace(namespaceRegex, '')) names = str2arr(names, '.');

			if (!names && !args && element[eventSupport]) {
				fireListener(nativeEvents[type], type, element);
			} else {
				// non-native event, either because of a namespace, arguments or a non DOM element
				// iterate over all listeners and manually 'fire'
				handlers = registry.get(element, type, null, false);
				args = [false].concat(args);
				var _iteratorNormalCompletion4 = true;
				var _didIteratorError4 = false;
				var _iteratorError4 = undefined;

				try {
					for (var _iterator4 = handlers[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
						var handler = _step4.value;

						if (handler.inNamespaces(names)) {
							handler.handler.apply(element, args);
						}
					}
				} catch (err) {
					_didIteratorError4 = true;
					_iteratorError4 = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion4 && _iterator4['return']) {
							_iterator4['return']();
						}
					} finally {
						if (_didIteratorError4) {
							throw _iteratorError4;
						}
					}
				}
			}
		}
		return element;
	};

	var clone = function clone(element, from, type) {
		var handlers = registry.get(from, type, null, false);
		var l = handlers.length;
		var i = 0;
		var args, beanDel;

		var _iteratorNormalCompletion5 = true;
		var _didIteratorError5 = false;
		var _iteratorError5 = undefined;

		try {
			for (var _iterator5 = handlers[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
				var handler = _step5.value;

				if (handler.original) {
					args = [element, handler.type];
					if (beanDel = handler.handler.__beanDel) args.push(beanDel.selector);
					args.push(handler.original);
					on.apply(null, args);
				}
			}
		} catch (err) {
			_didIteratorError5 = true;
			_iteratorError5 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion5 && _iterator5['return']) {
					_iterator5['return']();
				}
			} finally {
				if (_didIteratorError5) {
					throw _iteratorError5;
				}
			}
		}

		return element;
	};

	var events = {
		'on': on,
		'one': one,
		'off': off,
		'remove': off,
		'clone': clone,
		'fire': fire,
		'Event': Event
	};

	// for IE, clean up on unload to avoid leaks
	if (win.attachEvent) {
		var cleanup = function cleanup() {
			var i,
			    entries = registry.entries();
			for (i in entries) {
				if (entries[i].type && entries[i].type !== 'unload') off(entries[i].element, entries[i].type);
			}
			win.detachEvent('onunload', cleanup);
			win.CollectGarbage && win.CollectGarbage();
		};
		win.attachEvent('onunload', cleanup);
	}

	lilium.global('events', events);
	lilium.events = events;

	provides([EventSource], 'core');
})();
//==============================================================================
//
// The fundation part, must at the very first of core, so name this using 0
//
// @author Jack
// @version 1.0
// @date Thu Jun 25 23:12:50 2015
//
//==============================================================================

'use strict';

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; desc = parent = getter = undefined; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; }

+(function () {

	/**
  * The simple property source base class to provde the base get and set functions
  */

	var PropertySource = (function (_lilium$core$EventSource) {
		function PropertySource() {
			_classCallCheck(this, PropertySource);

			_get(Object.getPrototypeOf(PropertySource.prototype), 'constructor', this).apply(this, arguments);
		}

		_inherits(PropertySource, _lilium$core$EventSource);

		_createClass(PropertySource, [{
			key: 'get',
			value: function get(name, default_value) {
				return this[name] ? this[name] : default_value;
			}
		}, {
			key: 'set',
			value: function set(name, value) {
				this.fire('change', {
					'target': this,
					'propertyName': name,
					'value': value,
					'orig_value': this[name]
				});
				this[name] = value;
			}
		}]);

		return PropertySource;
	})(lilium.core.EventSource);

	var TreeNodeIterator = (function () {
		function TreeNodeIterator(node, type) {
			_classCallCheck(this, TreeNodeIterator);

			if (!type || type == 'depth') {
				this.type = 'depth';
			} else {
				this.type = 'spread';
			}
			this.node = node;
			this.current = node;
			this.stack = [this.current];
		}

		_createClass(TreeNodeIterator, [{
			key: 'path',
			value: function path() {
				return this.current.path();
			}
		}, {
			key: 'next',
			value: function next() {
				var orig = this.current;
				while (this.stack.length && orig == this.current) {
					if (this.type == 'spread') {
						this.current = this.stack.shift();
						if (this.current.children) {
							var _iteratorNormalCompletion = true;
							var _didIteratorError = false;
							var _iteratorError = undefined;

							try {
								for (var _iterator = this.current.children[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
									var child = _step.value;

									this.stack.push(child);
								}
							} catch (err) {
								_didIteratorError = true;
								_iteratorError = err;
							} finally {
								try {
									if (!_iteratorNormalCompletion && _iterator['return']) {
										_iterator['return']();
									}
								} finally {
									if (_didIteratorError) {
										throw _iteratorError;
									}
								}
							}
						}
					} else {
						this.current = this.stack.pop();
						if (this.current.children) {
							// Add current ndoe's children to stack
							for (var i = this.current.children.length - 1; i >= 0; i--) {
								this.stack.push(this.current.children[i]);
							}
						}
					}
				}
				var done = false;
				if (orig == this.current) {
					this.current = null;
					done = true;
				}
				return {
					value: this.current,
					done: done
				};
			}
		}]);

		return TreeNodeIterator;
	})();

	var TreePropertySourceNode = (function (_lilium$core$EventSource2) {
		function TreePropertySourceNode(name, parent, value) {
			_classCallCheck(this, TreePropertySourceNode);

			_get(Object.getPrototypeOf(TreePropertySourceNode.prototype), 'constructor', this).call(this);
			this.parent = parent;
			if (parent && parent instanceof TreePropertySourceNode) {
				parent.appendChild(this);
			}
			this.name = name;
			if (value) this.value = value;
		}

		_inherits(TreePropertySourceNode, _lilium$core$EventSource2);

		_createClass(TreePropertySourceNode, [{
			key: 'clear',
			value: function clear() {
				if (this.children) {
					var _iteratorNormalCompletion2 = true;
					var _didIteratorError2 = false;
					var _iteratorError2 = undefined;

					try {
						for (var _iterator2 = this.children[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
							var child = _step2.value;

							if (child) {
								child.clear(); // Clear the children of child to remove all the event handlers
							}
							this.removeChild(child);
						}
					} catch (err) {
						_didIteratorError2 = true;
						_iteratorError2 = err;
					} finally {
						try {
							if (!_iteratorNormalCompletion2 && _iterator2['return']) {
								_iterator2['return']();
							}
						} finally {
							if (_didIteratorError2) {
								throw _iteratorError2;
							}
						}
					}
				}
			}
		}, {
			key: 'nextSibling',
			value: function nextSibling() {
				if (this.parent) {
					var i = this.parent.children.indexOf(this);
					if (i != -1 && i + 1 < this.parent.children.length) {
						return this.parent.children[i + 1];
					}
				}
				return null;
			}
		}, {
			key: 'prevSibling',
			value: function prevSibling() {
				if (this.parent) {
					var i = this.parent.children.indexOf(this);
					if (i != -1 && i > 0) {
						return this.parent.children[i - 1];
					}
				}
				return null;
			}
		}, {
			key: 'removeChild',
			value: function removeChild(child) {
				if (child && this.children) {
					child.parent = null;
					lilium.removeElement(this.children, child);
					events.off(child, 'change'); // Remove all the change listeners
				}
			}
		}, {
			key: 'appendChild',
			value: function appendChild(child) {
				if (!this.children) this.children = [];
				this.children.push(child);
				child.parent = this;
			}
		}, {
			key: 'path',
			value: function path() {
				var path = [];
				var n = this;
				while (n) {
					if (n.name) // Ignore the node that has no name(usually the root node)
						path.unshift(n.name);
					n = n.parent;
				}
				return path.join('.');
			}
		}, {
			key: 'key',
			value: function key() {
				return this.path();
			}
		}, {
			key: 'childTree',

			/**
    * Generate the children tree
    */
			value: function childTree(names) {
				if (lilium.isArray(names)) {
					var cs = [];
					var child = null;
					while (names.length) {
						child = this.child(lilium.clone(names));
						if (child) {
							break;
						}

						var n = names.pop();
						if (n) {
							cs.unshift(n);
						}
					}

					var p = child ? child : this;
					var _iteratorNormalCompletion3 = true;
					var _didIteratorError3 = false;
					var _iteratorError3 = undefined;

					try {
						for (var _iterator3 = cs[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
							var c = _step3.value;

							child = new TreePropertySourceNode(c, p);
							p = child;
						}
					} catch (err) {
						_didIteratorError3 = true;
						_iteratorError3 = err;
					} finally {
						try {
							if (!_iteratorNormalCompletion3 && _iterator3['return']) {
								_iterator3['return']();
							}
						} finally {
							if (_didIteratorError3) {
								throw _iteratorError3;
							}
						}
					}

					return child;
				} else {
					return this.childTree(names.split('.'));
				}
			}
		}, {
			key: 'child',

			/**
    * Get the child by its name
    */
			value: function child(names) {
				if (lilium.isArray(names)) {
					var node = this;
					while (names.length) {
						var n = names.shift();
						if (n) {
							node = node.child(n);
							if (!node) break;
						}
					}
					if (node != this) return node;
				} else {
					if (this.children) {
						var _iteratorNormalCompletion4 = true;
						var _didIteratorError4 = false;
						var _iteratorError4 = undefined;

						try {
							for (var _iterator4 = this.children[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
								var child = _step4.value;

								if (child.name == names) return child;
							}
						} catch (err) {
							_didIteratorError4 = true;
							_iteratorError4 = err;
						} finally {
							try {
								if (!_iteratorNormalCompletion4 && _iterator4['return']) {
									_iterator4['return']();
								}
							} finally {
								if (_didIteratorError4) {
									throw _iteratorError4;
								}
							}
						}
					}
				}
				return null;
			}
		}]);

		return TreePropertySourceNode;
	})(lilium.core.EventSource);

	var TreePropertySource = (function (_TreePropertySourceNode) {
		function TreePropertySource() {
			_classCallCheck(this, TreePropertySource);

			_get(Object.getPrototypeOf(TreePropertySource.prototype), 'constructor', this).apply(this, arguments);
		}

		_inherits(TreePropertySource, _TreePropertySourceNode);

		_createClass(TreePropertySource, [{
			key: 'get',
			value: function get(name, default_value) {
				var n = this.childTree(name);
				return n ? n.value : default_value;
			}
		}, {
			key: 'watch',
			value: function watch(name, func) {
				this.change(name, func);
			}
		}, {
			key: 'change',
			value: function change(name, func) {
				var n = this.childTree(name);
				if (n) {
					n.addListener('change', func);
				}
				return this;
			}
		}, {
			key: 'set',
			value: function set(name, value) {
				var n = this.childTree(name);
				var event = {
					'target': n,
					'currentTarget': n,
					'propertyName': name,
					'value': value,
					'orig_value': n.value
				};

				var p = n;
				// balloon event
				while (p && !event.stop) {
					var e = lilium.clone(event);
					e.currentTarget = p;
					p.fire('change', e);
					p = p.parent;
				}

				n.value = value;
			}
		}]);

		return TreePropertySource;
	})(TreePropertySourceNode);

	provides([PropertySource, TreePropertySourceNode, TreePropertySource, TreeNodeIterator], 'core');
})();
//==============================================================================
//
// The cross platform ajax support part. This part will support nodejs and
// all the mainstream browsers.
//
// Will use XMLHttpRequest as default implementation, and will use nodejs's
// xmlhttprequest as the implmenentation in nodejs.
//
// @author Jack
// @version 1.0
// @date Sat Jun 27 13:29:22 2015
//
//==============================================================================

'use strict';

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

+(function () {

	/**
  * The Ajax api that uses ES6 Promise, you can use as this:
  * 
  * <code>
  * 		Ajax.get('/a.json')
  * 			.then(function(data) { console.info(data); })
  * 			.catch(function(error) { console.info(error); });
  * </code>
  */

	var Ajax = (function () {
		function Ajax() {
			_classCallCheck(this, Ajax);

			this.xhr = this.getXhr();
		}

		_createClass(Ajax, [{
			key: 'get',
			value: function get(url, data, headers) {
				return this.exec('GET', url, data, headers);
			}
		}, {
			key: 'post',
			value: function post(url, data, headers) {
				return this.exec('POST', url, data, headers);
			}
		}, {
			key: 'put',
			value: function put(url, data, headers) {
				return this.exec('PUT', url, data, headers);
			}
		}, {
			key: 'delete',
			value: function _delete(url, headers) {
				return this.exec('DELETE', url, null, headers);
			}
		}, {
			key: 'head',
			value: function head(url, data, headers) {
				return this.exec('HEAD', url, data, headers);
			}
		}, {
			key: 'exec',
			value: function exec(method, url, data, headers) {
				var _this = this;

				return new Promise(function (resolve, reject) {
					var payload = undefined;
					var data = data || {};
					var headers = headers || {};

					// Generate payload
					payload = _this._encode(data);
					if (method === 'GET' && payload) {
						url += '?' + payload;
						payload = null;
					}

					// open xhr
					_this.xhr.open(method, url);

					// Setting headers
					var content_type = _this.content_type || 'application/x-www-form-urlencoded';
					for (var h in headers) {
						if (headers.hasOwnProperty(h)) {
							if (h.toLowerCase() === 'content-type') content_type = headers[h];else _this.xhr.setRequestHeader(h, headers[h]);
						}
					}
					_this.xhr.setRequestHeader('Content-type', content_type);

					// Handle timeout
					if (_this.timeout && _this.timeout > 0) {
						var xhr = _this.xhr;
						_this.timeout_handle = setTimeout(function () {
							reject('TIMEOUT', 'Ajax request to url ' + url + ' has timeout!');
							xhr.abort();
						}, _this.timeout);
					}

					// Handle response
					_this.xhr.onreadystatechange = function () {
						if (_this.xhr.readyState === 4) {
							if (_this.timeout_handle) {
								clearTimeout(_this.timeout_handle);
							}
							if (!_this.xhr.status || (_this.xhr.status < 200 || _this.xhr.status >= 300) && _this.xhr.status !== 304) {
								reject(_this.xhr.status, _this.xhr.responseText, xhr);
							} else {
								resolve(_this.xhr.responseText, xhr);
							}
						}
					};

					_this.xhr.send(payload);
				});
			}
		}, {
			key: '_encode',
			value: function _encode(data) {
				var payload = '';
				if (typeof data === 'string') {
					payload = data;
				} else {
					var e = encodeURIComponent;
					var params = [];

					for (var k in data) {
						if (data.hasOwnProperty(k)) {
							params.push(e(k) + '=' + e(data[k]));
						}
					}
					payload = params.join('&');
				}
				return payload;
			}
		}, {
			key: 'getXhr',
			value: function getXhr() {
				if (typeof XMLHttpRequest !== 'undefined') {
					return new XMLHttpRequest();
				} else {
					if (typeof ActiveXObject !== 'undefined') return new ActiveXObject('Microsoft.XMLHTTP');else {
						var x = embed('xmlhttprequest').XMLHttpRequest;
						return new x();
					}
				}
			}
		}]);

		return Ajax;
	})();

	provides([Ajax], 'core');
})();
//==============================================================================
//
// The base definitions for DataStore
//
// @author Jack
// @version 1.0
// @date Sat Jun 27 21:03:32 2015
//
//==============================================================================

'use strict';

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; desc = parent = getter = undefined; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) subClass.__proto__ = superClass; }

+(function () {

	/**
  * The DataSource is a tree property source, and it can copy the value
  * of a standard JavaScript object to the destination path.
  */

	var DataStore = (function (_lilium$core$TreePropertySource) {
		function DataStore(data) {
			_classCallCheck(this, DataStore);

			_get(Object.getPrototypeOf(DataStore.prototype), 'constructor', this).call(this);
			if (data) {
				if (lilium.isArray(data)) {
					for (var i = 0; i < data.length; i++) {
						this.copy(data[i], i + '');
					}
				} else if (lilium.isObject(data)) {
					this.copy(data);
				}
			}
		}

		_inherits(DataStore, _lilium$core$TreePropertySource);

		_createClass(DataStore, [{
			key: 'entries',

			/**
    * Iterate all the entries in this DataStore using depth or spread
    * algorithm. You can call the key and value function of the entry
    * to get the key and the value
    */
			value: function entries(alg) {
				var _this = this;

				this[Symbol.iterator] = function () {
					return new lilium.core.TreeNodeIterator(_this, alg);
				};
				return this;
			}
		}, {
			key: 'keys',
			value: function keys(alg) {
				var r = [];
				var _iteratorNormalCompletion = true;
				var _didIteratorError = false;
				var _iteratorError = undefined;

				try {
					for (var _iterator = this.entries(alg)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
						var e = _step.value;

						r.push(e.key());
					}
				} catch (err) {
					_didIteratorError = true;
					_iteratorError = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion && _iterator['return']) {
							_iterator['return']();
						}
					} finally {
						if (_didIteratorError) {
							throw _iteratorError;
						}
					}
				}
			}
		}, {
			key: 'copy',
			value: function copy(data, path) {
				var n = path ? this.childTree(path) : null;

				if (n) {
					n.value = data;
				}

				if (lilium.isObject(data)) {
					for (var k in data) {
						var v = data[k];
						var p = path ? path + '.' + k : k;
						if (lilium.isObject(v) && !lilium.isArray(v)) {
							this.copy(v, p);
						} else {
							this.set(p, v);
						}
					}
				}
			}
		}]);

		return DataStore;
	})(lilium.core.TreePropertySource);

	provides([DataStore], 'ds');
})();

//# sourceMappingURL=lilium.js.map