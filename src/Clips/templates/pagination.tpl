{
	"from": "{{from}}",
	"columns": [{{#columns}}{{^first}},{{/first}}
		{ {{#fields}}{{^ffirst}}, {{/ffirst}}"{{key}}"{{#if value}}:"{{value}}"{{/if}}{{/fields}} }{{/columns}}
	]{{#if joins}},
	"join": [{{#joins}}{{^first}},{{/first}}
		["{{table}}", {"{{left}}":"{{right}}"}]
	{{/joins}}]
{{else}}

{{/if}}
}
