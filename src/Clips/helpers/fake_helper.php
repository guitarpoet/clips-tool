<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

function fake_name($name = null) {
	$f = new FakeData();
	return $f->fakeName($name);
}

function fake_domain() {
	$f = new FakeData();
	return $f->fakeDomain();
}

function fake_email($name = null, $domain = null) {
	$f = new FakeData();
	return $f->fakeEmail($name, $domain);
}