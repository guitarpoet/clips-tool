[{{#.}}{{^first}},{{/first}}
	{
		"field": "{{field}}",
		"label": "{{label}}",
		{{#state}}
		"state": "{{state}}",
		{{/state}}
		"rules": [{{#rules}}{{^ffirst}},{{/ffirst}}
			{{#value}}{ "{{key}}":"{{value}}" }{{/value}}{{^value}}"{{key}}"{{/value}}{{/rules}}
		]
	}{{/.}}
]
