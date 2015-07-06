//==============================================================================
//
// The base definitions for DataStore
//
// @author Jack
// @version 1.0
// @date Sat Jun 27 21:03:32 2015
//
//==============================================================================

+(function(){

/**
 * The DataSource is a tree property source, and it can copy the value
 * of a standard JavaScript object to the destination path.
 */
class DataStore extends lilium.core.TreePropertySource {
	constructor(data) {
		super();
		if(data) {
			if(lilium.isArray(data)) {
				for(let i = 0; i < data.length; i++) {
					this.copy(data[i], i + '');
				}
			}
			else if(lilium.isObject(data)) {
				this.copy(data);
			}
		}
	}
	
	/**
	 * Iterate all the entries in this DataStore using depth or spread
	 * algorithm. You can call the key and value function of the entry
	 * to get the key and the value
	 */
	entries(alg) {
		this[Symbol.iterator] = () => {
			return new lilium.core.TreeNodeIterator(this, alg);
		}
		return this;
	}

	keys(alg) {
		var r = [];
		for(let e of this.entries(alg)) {
			r.push(e.key());
		}
	}

	copy(data, path) {
		var n = path? this.childTree(path): null;

		if(n) {
			n.value = data;
		}

		if(lilium.isObject(data)) {
			for(let k in data) {
				let v = data[k];
				let p = path ? path + '.' + k : k;
				if(lilium.isObject(v) && !lilium.isArray(v)) {
					this.copy(v, p);
				}
				else {
					this.set(p, v);
				}
			}
		}
	}
}

provides([DataStore], 'ds');

})();
