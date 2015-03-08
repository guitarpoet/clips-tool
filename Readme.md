Clips Tool (A too based on rule engine [Clips](http://clipsrules.sourceforge.net))
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
5. Go to any folder, and create a folder named commands there
