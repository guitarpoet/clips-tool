[{{#.}}{{^first}},{{/first}}
	{
		"field": "{{field}}",
		"label": "{{label}}",
		{{#state}}
		"state": "{{state}}",
		{{/state}}
		"rules": [{{#rules}}{{^ffirst}},{{/ffirst}}
			{{#if value}}{ "{{key}}":"{{value}}" }{{else}}"{{key}}"{{/if}}{{/rules}}
		]
	}{{/.}}
]
