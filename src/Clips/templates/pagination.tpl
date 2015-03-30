{
	"from": "{{from}}",
	"columns": [{{#columns}}{{^first}},{{/first}}
		{ {{#fields}}{{^ffirst}}, {{/ffirst}}"{{key}}"{{#value}}:"{{value}}"{{/value}}{{/fields}} }{{/columns}}
	]{{#joins}},
	"join": [{{#joins}}{{^first}},{{/first}}
		["{{table}}", {"{{left}}":"{{right}}"}]
	{{/joins}}]
	{{/joins}}
}
