Clips.RuleEngine = function(server) {
	this.server = server;
	this.commands = [];
}

Clips.Command = function(command, data) {
	this.command = command;
	this.data = data;
}

Clips.RuleEngine.prototype = {
	
	command: function(command, data) {
		this.commands.push(new Clips.Command(command, data));
		return this;
	},
	clear: function() {
		this.commands = [];
	},
	assert: function(data) {
		return this.command('assert', data);
	},
	load: function(rules) {
		return this.command('load', rules);
	},
	filter: function(filter) {
		return this.command('filter', filter);
	},
	run: function(callback) {
		$.post(this.server, {'commands': JSON.stringify(this.commands)}, function(data){
			callback.call(Clips.rules, data);
		}, 'json');
	}
}
