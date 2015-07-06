//==============================================================================
//
// The fundation part, must at the very first of core, so name this using 0
//
// @author Jack
// @version 1.0
// @date Thu Jun 25 23:12:50 2015
//
//==============================================================================


+(function(){

class Lilium {

	sourceMap() {
		if(this.inNode()) {
			require('source-map-support').install();
		}
	}

	split(s, sep) {
		return s.split(sep || ' ');
	}

	isString(o) {
		return typeof o === 'string' || o instanceof String;
	}

	isObject(o) {
		return o && (typeof o === "object");
	}

	inNode() {
		return typeof GLOBAL == 'object';
	}

	global(name, value) {
		let w = null;
		if(typeof window === 'undefined') { // Add global support for nodejs
			w = GLOBAL;
		}
		else {
			w = window;
		}
		
		if(name) {
			if(typeof value === 'undefined') {
				return w[name];
			}
			else {
				w[name] = value;
			}
		}
		return w;
	}

	local(name, func) {
		let f = this.global(name);
		if(typeof f === 'function') {
			f.alias = name;
			return f;
		}
		if(func)
			func.alias = name;
		return func;
	}

	/**
	 * Test if the function is exists, if not exists then define it
	 * NOTE: This will define the function into the global environment
	 */
	defineIfNotExists(name, func) {
		let f = this.global(name);
		if(typeof f === 'function') {
			f.alias = name;
			return f;
		}
		this.global(name, func);
		if(func)
			func.alias = name;
		return func;
	}

	removeElement(a, e) {
		if(this.isArray(a)) {
			let index = a.indexOf(e);
			if(index != -1)
				return a.splice(index, 1);
		}
		return [];
	}

	clone(a) {
		if(this.isArray(a))
			return a.slice(0);	
		if(this.isObject(a)) {
			let ret = {};
			for(let p in a) {
				ret[p] = a[p];
			}
			return ret;
		}
		return null;
	}

	isArray(o) {
		return Object.prototype.toString.call(o) === '[object Array]';
	}

	getName(o) {
		if(o) {
			if(typeof o.alias !== 'undefined') {
				return o.alias;
			}
			if(typeof o.name !== 'undefined') {
				return o.name;
			}
			let ret = o.toString();
			ret = ret.substr('function '.length);
			ret = ret.substr(0, ret.indexOf('('));
			return ret;
		}
		return null;
	}
}

var lilium = new Lilium();

lilium.sourceMap(); // Map the source codes to ease the debug process.

lilium.defineIfNotExists('def', (name, func) => { return lilium.defineIfNotExists(name, func); });

def('lilium', lilium);
def('require', () => {}); // TODO: Define require if no require is defined
def('embed', (module) => {
	if(lilium[module]) {
		return lilium[module];
	}

	let m = require(module);
	if(m) {
		return m;
	}
	return null;
});

/**
 * Provide the widget and functions for module. This function will direct provide the Classes to the global lilium namespace
 * And if in browser context, won't provide any access to these exports other than global lilium namespace.
 */
def('provides', (widgets, module, global) => {
	var e = null;

	if(!module) // lilium widgets are the default module
		module = 'widgets';

	if(typeof exports !== 'undefined') { // Prefer nodejs environment
		e = exports;
	}
	else if(typeof window !== 'undefined') {
		e = {};	
	}

	if(lilium.isArray(widgets)) {
		for(let widget of widgets) {
			e[lilium.getName(widget)] = widget;
		}
	}
	else {
		e[lilium.getName(widgets)] = widgets;
	}
	
	lilium[module] = lilium[module] || {};

	if(global) {
		var g = lilium.global(module) || {};
	}

	for(let k in e) {
		lilium[module][k] = e[k];
		if(global) {
			g[k] = e[k];
		}
	}

	if(global) {
		lilium.global(module, g);
	}
});

provides([Lilium, def], 'core');

})();
