{extends file="bootstrap-layout.tpl"}
							{block name="toolbar"}
								{a class="btn btn-primary" uri="{{refer_name}}/create"}
									{lang}add{/lang}
								{/a}
								{a class="btn btn-info" datatable-for="{{name}}" uri="{{refer_name}}/show"}
									{lang}show{/lang}
								{/a}
								{a class="btn btn-warning" datatable-for="{{name}}" uri="{{refer_name}}/edit"}
									{lang}edit{/lang}
								{/a}
								{action class="btn btn-danger" datatable-for="{{name}}" uri="{{refer_name}}/delete"}delete{/action}
							{/block}
									{block name="workbench"}
										{datatable name="{{name}}"}
									{/block}
