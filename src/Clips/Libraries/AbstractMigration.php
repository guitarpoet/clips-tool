<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Symfony\Component\Yaml\Yaml;

class AbstractMigration extends \Phinx\Migration\AbstractMigration {
	public function init() {
		$class = explode('\\', get_class($this));
		$class = array_pop($class); // The last one
		$class = \Clips\to_flat(str_replace('Migration', '', $class));

        $this->tool = &\Clips\get_clips_tool();
        $this->template = $this->tool->library('MigrationTool');
		$this->config = (Yaml::parse(\Clips\content_relative('schemas/'.$class.'.yml', $this)));
    }

	public function up() {
   		$this->init(); 
        $this->template->up($this, $this->config);
		$this->doUp();
    }

	protected function doUp() {
	}

    public function down() {
		$this->init();
        $this->template->down($this, $this->config);
		$this->doDown();
    }

	protected function doDown() {
	}
}
