Clips Tool (A tool based on rule engine [Clips](http://clipsrules.sourceforge.net))
==================================================================================

Requirements
----------------------------------------------------------------------------------

**Required**

 * PHP >= 5.3.0
 * [php-clips](http://github.com/guitarpoet/php-clips): Clips Rule Engine's PHP port
 * [php-sass](http://github.com/guitarpoet/php-sass): A better SASS port for PHP based on [libsass](http://libsass.org)
 

**Optional**

 * [php-mmseg](http://github.com/guitarpoet/php-mmseg): The Chinese word tokenizer
 * [php-imagick](https://github.com/mkoppanen/imagick): The image processing framework for PHP, if you want to use widget like auto Figure or Picture, this is the recommended plugin
 * [php-gd](http://php.net/manual/en/ref.image.php): The image processing framework for PHP

Installation
----------------------------------------------------------------------------------

If you just want to use the command line part of the Clips Tool.
You'll just need [php-clips](http://github.com/guitarpoet/php-clips) installed first.

If you want to use web development framework. You'll need [php-sass](http://github.com/guitarpoet/php-sass) and at least 1 PHP image processing framework installed.

Then, add this code to your composer.json

	"require": {
		"php": ">=5.3.0",
        "guitarpoet/clips-tool": "*"
	}
	
Then, you can use composer to download it.

What is CLIPS? And why this framework?
----------------------------------------------------------------------------------

The reason of writting this framework (part of it, the command line part), can be found
at [here](http://thinkingcloud.info/2015/01/why-we-needs-another-data-processing-framework/).

This framework came out from a small framework to processing data using PHP.

It use CLIPS as its fundamental part, it try to create a flexiable environment like [CI](http://www.codeigniter.com) did, but honor the new standards in PHP 5.* and the [PHP-FIG](http://php-fig.org) designed.

So, what is CLIPS then? Why I use it as the core of the framework?

For details of CLIPS, you can get it [here](http://clipsrules.sourceforge.net/WhatIsCLIPS.html).

CLIPS for me is a fast(yes, very fast) RETE based rule engine written in C, it use a dialect of lisp as its grammar.

You can use it to write very smart rule based program, I'll take a very simple example from the demo code of this framework.

Suppose you have a little blog website that has a dashboard to manage it. You don't really want anyone but you and your cowriters of this blog can view the dashboard.

So, you want the dashboard can be viewed only when logged in.

Here is the rules code to make this:

	(defrule only-users-logged-in-can-view-dashboard
		(Clips\SecurityItem (type "action") (content ?content&:(str-match "dashboard/.*")))
		(test (not (php_call "Demo\\user_logged_in")))
		=>
		(reject "Must logged in to view DashBoard!")
	)

Let me explain it a little(for the details of CLIPS, you can get them [here](http://clipsrules.sourceforge.net/OnlineDocs.html)):

1. defrule: This command will define a rule and name it by the name you given it as the first parameter, for the upper example, I have a rule named only-users-logged-in-can-view-dashboard
2. Clips\SecurityItem: This is the framework object that framework generated automaticly. For this example, it is the request that user's browser(so the type is an action), and the uri of this action.
3. test: Test if the fact matches this rule, in this example, it will let PHP function do this work, to test if the user is logged in or not
4. reject: This is the framework function, just let framework to know you want to reject this action with the reason of "Must logged in to view DashBoard!" 

CLIPS can do lots more than this.

In fact, the core part of this framework, loading, configuration guessing, request routing, security checking are all done by rules.

Hello World? - Command Line
----------------------------------------------------------------------------------

Since this framework can be framework of command line or web. Let's start from commandline.

clips-tool begin with a simple command line tool first, so using clips-tool to write a 
command line program is very easy.

Here is the steps:

1. Clone the clips-tool's code from [here](https://github.com/guitarpoet/clips-tool.git)
2. Install the dependencies using composer
3. Add the path of clips (a bash script, window user can use cygwin to run it) to PATH, or create a soft link of that file to you bin folder
4. Run command `clips version` to test for it
5. Go to any folder, run clips generate command and follow the wizzard

After the steps above, you can get a folder named commands, and a file, say HelloCommand.php.

It should be something like this:

cwd\commands\HelloCommand.php

The content of the file should be something like this:

	<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

	use Clips\Command;

	/**
	 * This is a simple command
	 * 
	 * @author Jack
	 * @version 1.0
	 * @date Sun Mar  8 21:31:40 2015
	 */
	class HelloCommand extends Command {
		public function execute($args) {
		}
	}

And, yes, this is a very very simple command. But it has all the thing a command line command must have,
the commandline args, and all the methods it get from base class command.

Pretty simple, say if we just want to print a welcome message (hello world). So, you can change the 
code to this;

	<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

	use Clips\Command;

	/**
	 * This is a simple command
	 * 
	 * @author Jack
	 * @version 1.0
	 * @date Sun Mar  8 21:31:40 2015
	 */
	class HelloCommand extends Command {
		public function execute($args) {
			$this->output("Hello World!");
		}
	}

Then using command clips hello to run it.

Pretty simple, huh?

Let's begin with a little template thing.

Say you want to transfer a name to the hello world command, then you can change the code to this:

	<?php in_array(__FILE__, get_included_files()) or exit("exitNo direct sript access allowed");

	use Clips\Command;

	/**
	 * This iterates a simple command
	 * 
	 * @author Jack
	 * @version 1.0
	 * @date Sun Mar  8 21:31:40 2015
	 */
	class HelloCommand extends Command {
		public function execute($args) {
			$this->output(Clips\clips_out("string://Hello {{name}}", array('name' => Clips\get_default( $args, 0, "World")), false));
		}
	}

This needs some explaination.

Clips uses mustache as its default template engine. So you can just using clips_out function located in Clips namespace to use it.

But, how mustache find the template?

Clips tool uses a simple Resource scheme based resource framework to find the resource.

If there is no scheme set for clips_out, it will use tpl:// by default(which will try every template path).

And in above example, the resource scheme is string://, so mustache will just use the string as the input resource. This is a little like PHP's resource handling frameowork, but more flexiable.

The second thing to explain is Clips\get_default.

This is a handy function, it will try the first argument (object or array), if it has the key(second argument), if so, it will return the value of that key, if not, it will return the default value (third argument).


Getting Enhanced - Command Line
----------------------------------------------------------------------------------

So, how to start from here? For example, how we access the libraries that framework provided?

For example, what if you want to get the rule engine?

Let's start from a HelloRules Command, like this:

	<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

	use Clips\Command;
	use Clips\Interfaces\ClipsAware;

	/**
	 * The simple hello world rules command.
	 * 
	 * @author Jack
	 * @version 1.0
	 * @date Sun Mar  8 21:53:50 2015
	 */
	class HelloRulesCommand extends Command implements ClipsAware {
		public function setClips($clips) {
			$this->clips = $clips;
		}

		public function execute($args) {
			$this->clips->command('(defrule hello-world-rule (name ?name) => (printout t "Hello " ?name crlf))');
			$this->clips->assertFacts(array('name', Clips\get_default($args, 0, 'world')));
			$this->clips->run();
			var_dump($this->clips->queryFacts());
		}
	}

Let's look at the code in more details.

1. If you want clips to enhance your command, you just let your command implements the ClipsAware interface, and framework will set the clips reference to your command automaticly
2. You can just run the clips command through clips's command method
3. You can interact to clips' facts just using assertFacts and queryFacts methods, the detail for these methods, you can view it [here](http://github.com/guitarpoet/php-clips)

The output of the command clips helloRules will be like this:

	Hello world
	array(1) {
	  [0]=>
	  array(2) {
		[0]=>
		string(5) "world"
		["__template__"]=>
		string(4) "name"
	  }
	}

The same function like clips(request you to implement an interface to get the service you wanted) is:

 1. Clips: Rule engine
 2. Logger: The PSR-3 complaint logger
 3. Tool: The clips tool itself

Getting More Enhanced - Command Line
----------------------------------------------------------------------------------

It is cool to have your dependency just have a interface to implement.

But there is lots lots of objects in the framework, so how can you acces them? Implement interfaces one by one seems not be an great idea.

So, how can you get the depenency of your command?

It should be something like this:

	<?php in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

	use Clips\Command;

	/**
	 * A little demo command for annotation
	 * 
	 * @author Jack
	 * @version 1.0
	 * @date Sun Mar  8 22:05:16 2015
	 *
	 * @Clips\Library("markup")
	 */
	class HelloAnnoCommand extends Command {
		public function execute($args) {
			$this->output($this->markup->render("#This is h1"));
		}
	}

I used a framework library Markup as this exmple.

As you can see, the library dependency of this command, is just declared as an annotation of this command ( @Clips\Library("markup") ).

And after your declearation, framework will create and give the reference of that object to you as the name(lowercased) of the class name you have provided.

All the name you provided will be uppercase first before find the class, so you don't need to make the first letter uppercase. But if your class name is in camel case, **DO** preserve the upper case other than first letter.

For example, MyModel, should be myModel, not mymodel, or in file name case sensitive system like linux you won't get this class.

All the reference name of the object will be lowercase though (say MyModel will be mymodel).

Using only lowercase for reference name has 3 meanings:

1. The reference name is easy to identify and have small chances to override your command's fields (other fields are camel based)
2. Decrease the chances of miss typing for upper or lower cases
3. Auto fields are not means to read, but to use, if you don't like the name, you can just take a temp variable to reference it

Everything located in Libraries(no matter Clips or yours) will be load and created. If you wants to use namespace, look next chapter.

Other than Clips\Library, you can also use Clips\Model, Clips\Object to get the dependency you want.

Of cause, you shouldn't worry about the dependency of you dependency.

Command dependencies - Command Line
----------------------------------------------------------------------------------

OK, now you know what you should do if you want to add dependency to your command. But wait, what if
your command is based on another command?

Say, your package command will depends fetch command?

You can use @Clips\Depends to rescue. Use is just like the other annotations, and you can get the command executed right before your command.

How about configuration? - Command line
----------------------------------------------------------------------------------

Our command is very simple for now, what if we want to connect to some database?

Or try to locate some file in some folder? How can we configure our command?

That's the power of clips-tool.

You can add your configuration in:

 * cwd/
 * cwd/config
 * /etc/clips/
 * /etc/
 * /etc/rules

with name clips_tool.json. And if you have multiple of these, don't worry, clips-tool will get all
the configurations to you(say, you have a /etc/clips_tools.json as system wide configuration, and a project's own configuration).

And the configuration should be something like this(a simple configuration from a demo site):

	{
		"table_prefix":"cms_",
		"namespace": ["Clips\\Cms\\"],
		"route_rules": ["/application/rules/route.rules", "/rules/route.rules"],
		"filters": ["Security", "Rules", "Form", "Scss", "Css", "SmartyView", "MustacheView", "JsonView", "DirectView"],
		"helpers":["web", "html"],
		"default_view": "Smarty",
		"debug_sass": true,
		"sass_preload": ["variables"],
		"models": {
			"demo": {
				"datasource": "mysql"
			}
		},
		"logger": {
			"handlers": {
				"Stream" : ["php://stdin", "debug"],
				"FirePHP" : ["debug"],
				"ChromePHP": ["debug"],
				"RotatingFile": ["/tmp/clips_cms.log", 4, "debug"]
			},
			"processors": ["Git", "PsrLogMessage", "Introspection", "Tag"]
		},
		"datasources": {
			"mysql": {
				"table_prefix":"cms_",
				"type": "mysqli",
				"database": "cms_dev"
			}
		}
	}

As you can see, the configuration is no more than key => value json object.

And you can access any of your configuration at anywhere using Clips\config() function.

The Logging, Mate - Command line
----------------------------------------------------------------------------------

Maybe you can see the logging configuration above(logging is using [Monolog](https://github.com/Seldaek/monolog) as its implementation).

The logger and handlers is same as monolog, but without useless words.

It is quite useful to use FirePHP or ChromePHP to debug, believe me.


How about namespaces? - Command line
----------------------------------------------------------------------------------

Clips Tool is [PSR-4](http://www.php-fig.org/psr/psr-4/) complaint framework, and it prefers you to write application using it.

So, how can Clips Tool can know what namespace you are using(or namespaces, because you can use as many plugins as possible).

The configuration comes to rescure. See the example above:

	"namespace": ["Clips\\Cms\\"]

This configuration will told clips-tool, that you wants to use the classes located in Clips\\Cms\\ namespace.

So, for example, you want a model of Clips\\Cms\\Models\\UserModel, you just can get the reference using @Clips\Model("user").

Same thing, if you have a Object like Clips\\Cms\\Libraries\\Ldap, you can just get it using @Clips\\Library("ldap").

All tne classes in clips-tools follow the (Models, Libraries rule), you can save lots of typing follow these rules.
