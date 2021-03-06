<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The fake data generator
 *
 * @author Jack
 * @date Mon Feb 23 15:30:39 2015
 */
class FakeData {
	public $fakeNames = array('Steve Jobs', 'Bill Gates', 'Bill Cliton', 'George Bush', 'Barack Obama', 'Elten Sweet', 'Drew Clayton', 'Millson Stampes', 'Melvin Goodwin', 'Dwite Harding', 'Norton Atterton', 'Lindon Atherton', 'Brian Mitchell', 'Marsdon Holton', 'Jean Barney', 'Mina Eastoft', 'Kasandra Nottley', 'Christian Prescott', 'Riley Read', 'Corinne Southey', 'Kenzie Knotley', 'Bobby Snape', 'Berthe Smithies', 'Breana Nash', 'Jazmyne Smith', 'Normal Blackwood', 'Rawson Harrington', 'Wingate Benson', 'Forbes Spaulding', 'Raleigh Foy', 'Jack Burlingame', 'Waylon Altham', 'Herbert Royston', 'Quintin Sutton', 'Abraham Harding', 'Arthur Pendragon', 'Ambrosius Aurelianus', 'Lancelot Lac', 'Frodo Baggins', 'Bilbo Baggins');


	public function fakeMobile() {
		$i = 8;
		$heads = array(array(1,3,4), array(1,5,9), array(1, 8, 9));
		$digits = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

		$arr = choice($heads);
		while($i--) {
			$arr []= choice($digits);	
		}
		return implode('', $arr);
	}

	/**
	 * Generate the fake name for test
	 *
	 * @param name (default null)
	 * 	The name should be something like this:
	 *
	 * 	Steve Jobs
	 *
	 * 	If no name is supplied, will using the fake name collection to generate.
	 *
	 * @return Object with first name, last name, simple name(like steve_jobs)
	 */
	public function fakeName($name = null) {
		if(!$name) {
			$name = choice($this->fakeNames);
		}

		$fake = new \stdclass();
		$array = explode(' ', $name);

		if($array) {
			$fake->first_name = $array[0];
			if(isset($array[1])) {
				$fake->last_name = $array[1];
			}
		}
		$fake->name = $name;
		$fake->simple_name = strtolower(str_replace(' ', '_', $name));
		return $fake;
	}

	/**
	 * Generate a fake domain
	 *
	 * @author Jack
	 * @date Mon Feb 23 15:38:53 2015
	 */
	function fakeDomain() {
		static $fake_domains = array('163.com', '51.la', 'about.com', 'addthis.com', 'adobe.com', 'amazon.co.uk', 'amazon.com', 'ameblo.jp', 'aol.com', 'apple.com', 'baidu.com', 'bbc.co.uk', 'bing.com', 'blogger.com', 'blogspot.com', 'clickbank.net', 'cnn.com', 'creativecommons.org', 'dailymotion.com', 'delicious.com', 'digg.com', 'ebay.com', 'etsy.com', 'europa.eu', 'facebook.com', 'fc2.com', 'feedburner.com', 'flickr.com', 'forbes.com', 'free.fr', 'geocities.com', 'gnu.org', 'go.com', 'godaddy.com', 'goo.gl', 'google.co.jp', 'google.co.uk', 'google.com', 'google.de', 'gov.uk', 'guardian.co.uk', 'homestead.com', 'huffingtonpost.com', 'hugedomains.com', 'icio.us', 'imdb.com', 'instagram.com', 'issuu.com', 'jimdo.com', 'joomla.org', 'linkedin.com', 'livejournal.com', 'macromedia.com', 'mail.ru', 'mapquest.com', 'microsoft.com', 'miibeian.gov.cn', 'mozilla.org', 'msn.com', 'myspace.com', 'networkadvertising.org', 'nih.gov', 'nytimes.com', 'photobucket.com', 'pinterest.com', 'qq.com', 'rambler.ru', 'reddit.com', 'reuters.com', 'sina.com.cn', 'slideshare.net', 'sourceforge.net', 'statcounter.com', 'stumbleupon.com', 't.co', 'taobao.com', 'technorati.com', 'theguardian.com', 'tinyurl.com', 'tripod.com', 'tumblr.com', 'twitter.com', 'typepad.com', 'vimeo.com', 'vk.com', 'w3.org', 'washingtonpost.com', 'webs.com', 'weebly.com', 'weibo.com', 'wikipedia.org', 'wix.com', 'wordpress.com', 'wordpress.org', 'wsj.com', 'yahoo.co.jp', 'yahoo.com', 'yandex.ru', 'youtu.be', 'youtube.com');
		return choice($fake_domains);
	}

	/**
	 * Generate a fake mac address
	 *
	 * @author Jack
	 * @date Mon Feb 23 15:39:08 2015
	 */
	function fakeMac() {
		return \implode(':',\str_split(\substr(\md5(\mt_rand()),0,12),2));
	}

	/**
	 * Generate a fake email address
	 *
	 * @author Jack
	 * @date Mon Feb 23 15:39:23 2015
	 */
	function fakeEmail($name = null, $domain = null) {
		$n = $this->fakeName($name);
		$domain = $domain? $domain: $this->fakeDomain();
		return $n->simple_name."@".$domain;
	}

	/**
	 * Generate a fake IP address
	 *
	 * @author Jack
	 * @date Mon Feb 23 15:39:45 2015
	 */
	function fakeIP($start = '192.168.22.1', $end = '192.168.22.200') {
		if (strcmp($start, $end) > 0) {
			return false;
		}

		$arrStart = explode('.',$start);
		$arrEnd = explode('.', $end);

		// First
		$arrIp[0] = rand($arrStart[0], $arrEnd[0]);

		// Second
		if ($arrIp[0] == $arrStart[0] && $arrIp[0] == $arrEnd[0]) {
			$arrIp[1] = rand($arrStart[1], $arrEnd[1]);
		} elseif ($arrIp[0] == $arrStart[0]) {
			$arrIp[1] = rand($arrStart[1], 255);
		} elseif ($arrIp[0] == $arrEnd[0]) {
			$arrIp[1] = rand(0, $arrEnd[1]);
		} else {
			$arrIp[1] = rand(0, 255);
		}

		// Third
		if ($arrIp[1] == $arrStart[1] && $arrIp[1] == $arrEnd[1]) {
			$arrIp[2] = rand($arrStart[2], $arrEnd[2]);
		} elseif ($arrIp[1] == $arrStart[1]) {
			$arrIp[2] = rand($arrStart[2], 255);
		} elseif ($arrIp[1] == $arrEnd[1]) {
			$arrIp[2] = rand(0, $arrEnd[2]);
		} else {
			$arrIp[2] = rand(0, 255);
		}

		// Fourth
		if ($arrIp[2] == $arrStart[2] && $arrIp[02] == $arrEnd[2]) {
			$arrIp[3] = rand($arrStart[3], $arrEnd[3]);
		} elseif ($arrIp[2] == $arrStart[2]) {
			$arrIp[3] = rand($arrStart[3], 255);
		} elseif ($arrIp[2] == $arrEnd[2]) {
			$arrIp[3] = rand(0, $arrEnd[3]);
		} else {
			$arrIp[3] = rand(0, 255);
		}

		return implode(".", $arrIp);
	}
}
