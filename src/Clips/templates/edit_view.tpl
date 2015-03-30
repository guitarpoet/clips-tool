{extends file="bootstrap-layout.tpl"}
							{block name="toolbar"}
								{a class="btn btn-primary" form-for="{{name}}_edit"}
									{lang}update{/lang}
								{/a}
							{/block}
										{block name="workbench"}
											{form name="{{name}}_edit" state="readonly"}
												{field field="id" state="hidden"}{/field}
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
