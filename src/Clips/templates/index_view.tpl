{extends file="bootstrap-layout.tpl"}
							{block name="toolbar"}
								{a class="btn btn-primary" uri="{{name}}/create"}
									{lang}add{/lang}
								{/a}
								{a class="btn btn-info" datatable-for="{{name}}" uri="{{name}}/show"}
									{lang}show{/lang}
								{/a}
								{a class="btn btn-warning" datatable-for="{{name}}" uri="{{name}}/edit"}
									{lang}edit{/lang}
								{/a}
								{action class="btn btn-danger" datatable-for="{{name}}" uri="{{name}}/delete"}delete{/action}
							{/block}
									{block name="workbench"}
										{datatable name="{{name}}"}
									{/block}
