<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

use Clips\Libraries\ObjectQuery;

class ObjectQueryTest extends Clips\TestCase {

	public function testSimpleQuery() {
		$this->query = new ObjectQuery('Clips\\Object[name = ?]');
		$this->assertNotNull($this->query);
		$result = $this->query->match_Expr();
		$result = $result['expr'];
		$this->assertTrue(!!$result && is_array($result));
		$this->assertTrue(isset($result['layers']));
		$layers = $result['layers'];
		$this->assertTrue(!!$layers && is_array($layers));
		$this->assertEquals(count($layers), 1);
		$layer = $layers[0];

		$this->assertTrue(!!$layer && is_array($layer));
		$this->assertTrue(isset($layer['selectors']));

		$selectors = $layer['selectors'];
		$this->assertTrue(!!$selectors && is_array($selectors));
		$this->assertEquals(count($selectors), 1);

		$selector = $selectors[0];
		$this->assertTrue(!!$selector && is_array($selector));
		$this->assertTrue(isset($selector['conditions']));
		$this->assertEquals($selector['type'], 'Clips\\Object');

		$conditions = $selector['conditions'];
		$this->assertEquals(count($conditions), 1);
		$condition = $conditions[0];

		$this->assertEquals($condition, array(
			'_matchrule' => 'Condition',
			'name' => 'Condition',
			'text' => 'name = ?',
			'op' => '=',
			'var' => 'name',
			'val' => '?'
		));
	}

	public function testFailQuery() {
		$this->query = new ObjectQuery('Clips\\Object[name = asdf]');
		$result = $this->query->match_Expr();
		$result = $result['expr'];
		$this->assertTrue(!!$result && is_array($result));
		$this->assertTrue(isset($result['layers']));
		$layers = $result['layers'];
		$this->assertTrue(!!$layers && is_array($layers));
		$this->assertEquals(count($layers), 1);
		$layer = $layers[0];

		$this->assertTrue(!!$layer && is_array($layer));
		$this->assertTrue(isset($layer['selectors']));

		$selectors = $layer['selectors'];
		$this->assertTrue(!!$selectors && is_array($selectors));
		$this->assertEquals(count($selectors), 1);

		$selector = $selectors[0];
		$this->assertTrue(!!$selector && is_array($selector));
		$this->assertFalse(isset($selector['conditions']));
	}

	public function testAliasQuery() {
		$this->query = new ObjectQuery('$obj[name = ?]');
		$this->assertNotNull($this->query);
		$result = $this->query->match_Expr();
		$result = $result['expr'];
		$this->assertTrue(!!$result && is_array($result));
		$this->assertTrue(isset($result['layers']));
		$layers = $result['layers'];
		$this->assertTrue(!!$layers && is_array($layers));
		$this->assertEquals(count($layers), 1);
		$layer = $layers[0];

		$this->assertTrue(!!$layer && is_array($layer));
		$this->assertTrue(isset($layer['selectors']));

		$selectors = $layer['selectors'];
		$this->assertTrue(!!$selectors && is_array($selectors));
		$this->assertEquals(count($selectors), 1);

		$selector = $selectors[0];
		$this->assertTrue(!!$selector && is_array($selector));
		$this->assertTrue(isset($selector['conditions']));
		$this->assertEquals($selector['type'], '$obj');

		$conditions = $selector['conditions'];
		$this->assertEquals(count($conditions), 1);
		$condition = $conditions[0];

		$this->assertEquals($condition, array(
			'_matchrule' => 'Condition',
			'name' => 'Condition',
			'text' => 'name = ?',
			'var' => 'name',
			'op' => '=',
			'val' => '?'
		));
	}

	public function testComplexQuery() {
		$this->query = new ObjectQuery('$obj[ name = ? ] > * > $action[ status = $active ], $action[ disabled = $true ]');
		$result = $this->query->match_Expr();
		$this->assertTrue(!!$result);
		$result = $result['expr'];
		print_r($result);
	}
}
