{
	"from": "{{from}}",
	"columns": [{{#columns}}{{^first}},{{/first}}
		{ {{#fields}}{{^ffirst}}, {{/ffirst}}"{{key}}"{{#value}}:"{{value}}"{{/value}}{{/fields}} }{{/columns}}
	]{{#joins_count}},
	"join": [{{#joins}}{{^first}},{{/first}}
		["{{table}}", {"{{left}}":"{{right}}"}]
	{{/joins}}]
{{/joins_count}}
}
