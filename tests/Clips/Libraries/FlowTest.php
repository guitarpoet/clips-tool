<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;

/**
 * @Clips\Library('flow')
 */
class FlowTest extends TestCase {
	public function testFlow() {
		$this->flow->load('workflow.rules');
		$this->flow->status('uploaded');
		$this->flow->action('process');
		$this->assertEquals($this->flow->status(), 'processing');
		$this->assertEquals($this->flow->actions(), array('process_complete'));
		$this->flow->action('process_complete');
		$this->assertEquals($this->flow->actions(), array());
		$this->assertEquals($this->flow->status(), 'processed');
	}
}
