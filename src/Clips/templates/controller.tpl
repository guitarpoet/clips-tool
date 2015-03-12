<?php namespace {{namespace}}Controllers; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\Controller;

/**
 * Auto generated controller by Scaffold for table {{table}}.
 *
 * @author {{author}}
 * @version {{version}}
 * @date {{date}}
 *
 * @Clips\Widget({"html", "lang", "grid"})
 * @Clips\Model({ {{#models}}{{^first}}, {{/first}}"{{model}}"{{/models}} });
 */
class {{controller_name}} extends Controller {

	/**
	 * @Clips\Widgets\DataTable("{{table_name}}")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function index() {
		return $this->render('{{refer_name}}/index');
	}

	/**
	 * @Clips\Form("{{table_name}}_edit")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function show($id) {
		$data = $this->{{refer_name}}->load($id);
		$this->formData("{{table_name}}_edit", $data);
		return $this->render('{{refer_name}}/show', array({{#refers}}{{^first}}, {{/first}}'{{key}}' => $this->{{model}}->get(){{/refers}}));
	}

	/**
	 * @Clips\Form("{{table_name}}_create")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function create() {
		return $this->render('{{refer_name}}/create', array({{#refers}}{{^first}}, {{/first}}'{{key}}' => $this->{{model}}->get(){{/refers}}));
	}

	/**
	 * @Clips\Form("{{table_name}}_create")
	 */
	public function create_form() {
		$this->user->insert($this->{{refer_name}}->cleanFields('{{table}}', $this->post()));
		return $this->redirect(\Clips\site_url('{{refer_name}}/index'));
	}

	/**
	 * @Clips\Form("{{table_name}}_edit")
	 * @Clips\Actions("{{refer_name}}")
	 */
	public function edit($id) {
		$data = $this->{{refer_name}}->load($id);
		$this->formData("{{refer_name}}_edit", $data);
		return $this->render('{{refer_name}}/edit', array({{#refers}}{{^first}}, {{/first}}'{{key}}' => $this->{{model}}->get(){{/refers}}));
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
