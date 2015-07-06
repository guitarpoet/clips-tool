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

/**
 * The simple property source base class to provde the base get and set functions
 */
class PropertySource extends lilium.core.EventSource {
	get(name, default_value) {
		return this[name]? this[name]: default_value;
	}

	set(name, value) {
		this.fire('change', {
			'target': this,
			'propertyName': name,
			'value': value,	
			'orig_value': this[name]
		});
		this[name] = value;
	}
}

class TreeNodeIterator {
	constructor(node, type) {
		if(!type || type == 'depth') {
			this.type = 'depth';
		}
		else {
			this.type = 'spread';
		}
		this.node = node;
		this.current = node;
		this.stack = [this.current];
	}

	path() {
		return this.current.path();
	}

	next() {
		var orig = this.current;
		while(this.stack.length && orig == this.current) {
			if(this.type == 'spread') {
				this.current = this.stack.shift();
				if(this.current.children) {
					for(let child of this.current.children) {
						this.stack.push(child);
					}
				}
			}		
			else {
				this.current = this.stack.pop();
				if(this.current.children) { // Add current ndoe's children to stack
					for(let i = this.current.children.length - 1; i >= 0; i--) {
						this.stack.push(this.current.children[i]);
					}
				}
			}
		}
		var done = false;
		if(orig == this.current) {
			this.current = null;
			done = true;
		}
		return {
			value: this.current,
			done: done
		};
	}
}

class TreePropertySourceNode extends lilium.core.EventSource {
	constructor(name, parent, value) {
		super();
		this.parent = parent;
		if(parent && parent instanceof TreePropertySourceNode) {
			parent.appendChild(this);
		}
		this.name = name;
		if(value)
			this.value = value;

	}

	clear() {
		if(this.children) {
			for(let child of this.children) {
				if(child) {
					child.clear(); // Clear the children of child to remove all the event handlers
				}
				this.removeChild(child);
			}
		}
	}

	nextSibling() {
		if(this.parent) {
			let i = this.parent.children.indexOf(this);
			if(i != -1 && i + 1 < this.parent.children.length) {
				return this.parent.children[i + 1];
			}
		}
		return null;
	}

	prevSibling() {
		if(this.parent) {
			let i = this.parent.children.indexOf(this);
			if(i != -1 && i > 0) {
				return this.parent.children[i - 1];
			}
		}
		return null;
	}

	removeChild(child) {
		if(child && this.children) {
			child.parent = null;
			lilium.removeElement(this.children, child);	
			events.off(child, 'change'); // Remove all the change listeners
		}
	}

	appendChild(child) {
		if(!this.children)
			this.children = [];
		this.children.push(child);
		child.parent = this;
	}

	path() {
		let path = [];
		let n = this;
		while(n) {
		if(n.name) // Ignore the node that has no name(usually the root node)
				path.unshift(n.name);	
			n = n.parent;
		}
		return path.join('.');
	}

	key() {
		return this.path();
	}

	/**
	 * Generate the children tree
	 */
	childTree(names) {
		if(lilium.isArray(names)) {
			var cs = [];
			var child = null;
			while(names.length) {
				child = this.child(lilium.clone(names));
				if(child) {
					break;
				}

				var n = names.pop();
				if(n) {
					cs.unshift(n);
				}
			}

			var p = child? child: this;
			for(let c of cs) {
				child = new TreePropertySourceNode(c, p);	
				p = child;
			}
			return child;
		}
		else {
			return this.childTree(names.split("."));
		}
	}

	/**
	 * Get the child by its name
	 */
	child(names) {
		if(lilium.isArray(names)) {
			let node = this;
			while(names.length) {
				let n = names.shift();
				if(n) {
					node = node.child(n);		
					if(!node)
						break;
				}
			}
			if(node != this)
				return node;
		}
		else {
			if(this.children) {
				for(let child of this.children) {
					if(child.name == names)
						return child;
				}
			}
		}
		return null;
	}
}

class TreePropertySource extends TreePropertySourceNode {
	get(name, default_value) {
		let n = this.childTree(name);
		return n? n.value: default_value;	
	}

	watch(name, func) {
		this.change(name, func);
	}

	change(name, func) {
		var n = this.childTree(name);
		if(n) {
			n.addListener('change', func);
		}
		return this;
	}

	set(name, value) {
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
		while(p && !event.stop) {
			let e = lilium.clone(event);
			e.currentTarget = p;
			p.fire('change', e);
			p = p.parent;
		}

		n.value = value;
	}
}

provides([PropertySource, TreePropertySourceNode, TreePropertySource, TreeNodeIterator], 'core');

})();
