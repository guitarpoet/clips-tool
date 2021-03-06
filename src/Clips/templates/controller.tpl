<?php namespace {{namespace}}Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

/**
 * Auto generated controller by Scaffold for table {{table}}.
 *
 * @author {{author}}
 * @version {{version}}
 * @date {{date}}
 *
 * @Clips\Widget({"html", "lang", "grid", "scaffold"})
 * @Clips\Model({ {{#models}}{{^first}}, {{/first}}"{{model}}"{{/models}} });
 */
class {{controller_name}} extends Controller {

	/**
	 * @Clips\Widgets\DataTable("{{table_name}}")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function index() {
		$this->title("{{title_name}} List", true);
		return $this->render('{{table_name}}/index');
	}

	/**
	 * @Clips\Form("{{table_name}}_edit")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function show($id) {
		$this->title("{{title_name}} Details for {{refer_name}} $id", true);
		$data = $this->{{refer_name}}->load($id);
		$this->formData("{{table_name}}_edit", $data);
		$args = array({{#refers}}{{^first}}, {{/first}}'{{key}}' => $this->{{model}}->get(){{/refers}});
		$args['data'] = $data;
		$args['id'] = $id;
		return $this->render('{{table_name}}/show', $args);
	}

	/**
	 * @Clips\Form("{{table_name}}_create")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function create() {
		$this->title("Create {{title_name}}", true);
		return $this->render('{{table_name}}/create', array({{#refers}}{{^first}}, {{/first}}'{{key}}' => $this->{{model}}->get(){{/refers}}));
	}

	/**
	 * @Clips\Form("{{table_name}}_create")
	 */
	public function create_form() {
		$this->{{refer_name}}->insert($this->{{refer_name}}->cleanFields('{{table}}', $this->post()));
		return $this->redirect(\Clips\site_url('{{refer_name}}/index'));
	}

	/**
	 * @Clips\Form("{{table_name}}_edit")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function edit($id) {
		$this->title("Edit {{title_name}} for {{refer_name}} $id", true);
		$data = $this->{{refer_name}}->load($id);
		$this->formData("{{refer_name}}_edit", $data);
		return $this->render('{{table_name}}/edit', array({{#refers}}{{^first}}, {{/first}}'{{key}}' => $this->{{model}}->get(){{/refers}}));
	}

	/**
	 * @Clips\Form("{{table_name}}_edit")
	 */
	public function edit_form() {
		$data = $this->{{refer_name}}->cleanFields('{{table}}', $this->post());
		$result = $this->{{refer_name}}->update((Object)$data);
		if ($result) {
			return $this->redirect(\Clips\site_url("{{refer_name}}/index"));
		} else {
			$this->error('Error in updating {{refer_name}}.', 'update');
		}
	}

	public function delete($id = null) {
		if($id) {
			$this->{{refer_name}}->delete($id);
		}
		else {
			 $this->{{refer_name}}->delete($this->post('ids'));
		}
		return $this->redirect(\Clips\site_url('{{refer_name}}/index'));
	}
}
