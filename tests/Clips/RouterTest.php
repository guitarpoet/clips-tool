<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * @Clips\Object("router")
 */
class RouterTest extends Clips\TestCase {
	public function testRouteResult() {
		$result = ($this->router->routeResult('responsive/size/400/a.jpg'));
		$this->assertEquals(count($result), 1);
		$result = $result[0];
		$this->assertTrue(Clips\valid_obj($result, 'Clips\\RouteResult'));
		$this->assertEquals($result->controller, 'Clips\\Controllers\\ResponsiveController');
		$this->assertEquals($result->method, 'size');
		$this->assertEquals($result->args, array('400', 'a.jpg'));
	}

	public function testRouteError() {
		$result = $this->router->routeResult('no/this/controller/');
		$this->assertEquals(count($result), 1);
		$result = $result[0];
		$result = $result['__template__'];
		$this->assertEquals($result, 'RouteError');
	}
}
