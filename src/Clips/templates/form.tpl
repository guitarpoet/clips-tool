[
{{#.}}{{^first}},{{/first}}
	{
		"field": "{{field}}",
		"label": "{{label}}",
		{{#state}}
		"state": "{{state}}",
		{{/state}}
		"rules": [{{#rules}}{{^rfirst}},{{/rfirst}}
			"{{key}}"{{#value}}:"{{value}}"{{/value}}{{/rules}}
		]
	}{{/.}}
]
