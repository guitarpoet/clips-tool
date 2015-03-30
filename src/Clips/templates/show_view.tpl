{extends file="bootstrap-layout.tpl"}
							{block name="toolbar"}
								{a class="btn btn-primary" uri="{{name}}/edit/$id"}
									{lang}edit{/lang}
								{/a}
							{/block}
										{block name="workbench"}
											{form name="{{name}}_edit" state="readonly"}
											{{#fields}}
												{{#join_table}}
												{field field="{{field}}"}
													{select options=${{join_table}} label-field="{{label_field}}" value-field="id"}
													{/select}
												{/field}
												{{/join_table}}
												{{^join_table}}
												{field field="{{field}}"}{/field}
												{{/join_table}}
											{{/fields}}
											{/form}
										{/block}
