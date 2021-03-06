<?php in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

class User {
	/** @Symbol */
	public $name;

	public $age;

	/** @Multi */
	public $friends;

	public function test() {
	}
}

class Dummy {
	public $hello;
	public $world;
	public function __construct() {
		$this->hello = 1;
		$this->world = '2';
	}
}

class EngineTest extends Clips\TestCase {

    public function doSetUp() {
		$this->clips = new Clips\Engine();
    }

	public function doTearDown() {
		$this->clips->clear();
    }
	
	public function testAssertFactsUsingOneObject() {
		$clips = $this->clips;
		$d = new Dummy();
		$clips->assertFacts($d);
		$facts = $clips->queryFacts('Dummy');
		$this->assertEquals(count($facts), 1);
		$fact = $facts[0];
		$this->assertEquals($fact->hello, $d->hello);
		$this->clips->listTemplates();
		$this->clips->printTemplate('Dummy');
	}

	public function testAssertFactsUsingObjectVarArgs() {
		$clips = $this->clips;
		$d = new Dummy();
		$d2 = new Dummy();
		$d2->hello = 2;
		$clips->assertFacts($d, $d2);
		$facts = $clips->queryFacts('Dummy');
		$this->assertEquals(count($facts), 2);
		$fact = $facts[0];
		$this->assertEquals($fact->hello, $d->hello);
		$fact = $facts[1];
		$this->assertEquals($fact->hello, $d2->hello);
	}

	public function testAssertFactsUsingObjectArrayArgs() {
		$clips = $this->clips;
		$d = new Dummy();
		$d2 = new Dummy();
		$d2->hello = 2;
		$clips->assertFacts(array($d, $d2));
		$facts = $clips->queryFacts('Dummy');
		$this->assertEquals(count($facts), 2);
		$fact = $facts[0];
		$this->assertEquals($fact->hello, $d->hello);
		$fact = $facts[1];
		$this->assertEquals($fact->hello, $d2->hello);
	}

	public function testAssertFactsUsingArrayArgs() {
		$clips = $this->clips;
		$clips->assertFacts(array('hello', 'world'));
		$facts = $clips->queryFacts('hello');
		$this->assertEquals(count($facts), 1);
		$fact = $facts[0];
		$this->assertEquals($fact['__template__'], 'hello');
		$this->assertEquals($fact[0], 'world');
	}

	public function testAssertFactsUsingVarArrayArgs() {
		$clips = $this->clips;
		$clips->assertFacts(array('hello', 'world'), array('hello', 'jack'));
		$facts = $clips->queryFacts('hello');
		$this->assertEquals(count($facts), 2);
		$fact = $facts[0];
		$this->assertEquals($fact['__template__'], 'hello');
	}

	public function testAssertFactsUsingTemplateStringArrayArgs() {
		$clips = $this->clips;
		$clips->switchMain();
		$d = array('__template__'=>'Dummy', 'hello' => 1);
		$clips->template('Dummy');
		$clips->assertFacts(
			$d, array('hello', 'jack'));
		$facts = $clips->queryFacts();
		$clips->facts();
		$this->assertEquals(count($facts), 2);
		$fact = $facts[0];
		$this->assertEquals($d['hello'], $fact->hello);
	}

	public function testAssertFactsWithJson() {
		$clips = $this->clips;
		$clips->switchMain();
		$dummy = new Dummy();
		$dummy->hello = json_encode($dummy);
		$clips->assertFacts($dummy);
		$facts = $clips->queryFacts('Dummy');
		$this->assertEquals(count($facts), 1);
		$clips->facts();
	}

	public function testAssertFactsUsingArrayArrayArgs() {
		$clips = $this->clips;
		$clips->assertFacts(array(array('hello', 'world'), array('hello', 'jack')));
		$facts = $clips->queryFacts('hello');
		$this->assertEquals(count($facts), 2);
		$fact = $facts[0];
		$this->assertEquals($fact['__template__'], 'hello');
	}

	public function testContextAcess() {
		$this->assertNotNull($this->clips);
		$this->clips->hello = "world";
	}

	public function testSymbolInArray() {
		$clips = $this->clips;
		$clips->assertFacts(array('hello', Clips\Symbol::symbol('world')));
		$clips->facts();
	}

	public function testSymbolInClass() {
		$clips = $this->clips;
		$u = new User();
		$u->name = 'Jack';
		$u->friends = array('Jane', 'Lena');
		$clips->assertFacts($u);
		$clips->facts();
	}

	public function testTemplateExists() {
		$this->clips->assertFacts(new Dummy());
		$this->assertTrue($this->clips->templateExists('Dummy'));
	}

	public function testDefineSlot() {
		echo $this->clips->defineSlot('hello', 'slot', '1',
			array(
				array(
					'type' => 'type',
					'value' => 'INTEGER'
				),
				array(
					'type' => 'range',
					'begin' => 1,
					'end' => 10
				),
				array(
					'type' => 'allowed-integers',
					'value' => '1|2|3|4|5|6'
				)
		));
		echo "\n";
	}

	public function testEnvExists() {
		$this->assertTrue($this->clips->isEnvExists(CLIPS_MAIN_ENV));
	}

	public function testCreateEnv() {
		$this->clips->createEnv('TEST');
		$this->assertTrue($this->clips->isEnvExists('TEST'));
	}

	public function testSwitchEnv() {
		$this->testCreateEnv();
		$this->clips->switchEnv('TEST');
		$meta = $this->clips->getMeta();
		print_r($meta);
		$this->assertEquals($meta['current'], "TEST");
		$this->assertEquals($this->clips->current_env, "TEST");
	}

	public function testGetMeta() {
		$this->clips->switchMain();
		$meta = $this->clips->getMeta();
		$this->assertEquals($meta['current'], CLIPS_MAIN_ENV);
		$this->clips->command('(defrule hahahahha (no one) => (printout t "SHIT"))');
		$this->clips->printRule('hahahahha');
	}

	public function testClipsRunWithEnv() {
		$self = $this;
		$env = $this->clips->current_env;
		$this->assertTrue($this->clips->runWithEnv(CLIPS_CORE_ENV, function($clips) {
			if($clips->current_env != CLIPS_CORE_ENV) {
				trigger_error('The clips environment not changed!!!!!');
				return false;
			}
			return true;
		}));
		$this->assertEquals($env, $this->clips->current_env);
		$this->assertTrue($this->clips->runWithEnv(CLIPS_MAIN_ENV, function($clips) {
			if($clips->current_env != CLIPS_MAIN_ENV) {
				trigger_error('The clips environment not changed!!!!!');
				return false;
			}
			return true;
		}));
		$this->assertEquals($env, $this->clips->current_env);
	}

	public function testClipsLoadRules() {
		Clips\clips_load_rules(array(dirname(__FILE__).'/test.rules'));
		$facts = $this->clips->queryFacts();
		$this->assertEquals($facts, array(array('rules', '__template__' => 'hello')));
	}

	public function testDefineClass() {
		echo $this->clips->defineClass('PHP_OBJECT', 
			array('USER', 'OBJECT'), false, array(
				'name', 'age', 
				array(
					'type' => 'multislot', 'name' => 'friends',
					'constraints' => array(
						array(
							'type' => 'range',
							'begin' => 1,
							'end' => 10
						),
						array(
							'type' => 'type',
							'value' => 'INTEGER'
						)
					)
				)
			)
		);
		echo "\n";
	}
}
