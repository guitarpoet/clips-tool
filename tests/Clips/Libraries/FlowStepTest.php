<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\TestCase;
use Clips\Libraries\FlowStep;

/**
 * @author Jack
 * @date Fri Jun 12 19:39:34 2015
 */
class FlowStepTest extends TestCase {
	public function testFlowStep() {
		$step = '[process_complete]:processing->processed';
		$flowStep = new FlowStep($step);
		$result = $flowStep->match_Rule();
		$this->assertEquals($result['action'], 'process_complete');
		$this->assertEquals($result['from'], 'processing');
		$this->assertEquals($result['to'], 'processed');
	}
}
