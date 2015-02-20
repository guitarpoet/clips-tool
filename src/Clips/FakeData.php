<?php namespace Clips; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class FakeData {
	const FAKE_NAMES = array('Elten Sweet', 'Drew Clayton', 'Millson Stampes', 'Melvin Goodwin', 'Dwite Harding', 'Norton Atterton', 'Lindon Atherton', 'Brian Mitchell', 'Marsdon Holton', 'Jean Barney', 'Mina Eastoft', 'Kasandra Nottley', 'Christian Prescott', 'Riley Read', 'Corinne Southey', 'Kenzie Knotley', 'Bobby Snape', 'Berthe Smithies', 'Breana Nash', 'Jazmyne Smith', 'Normal Blackwood', 'Rawson Harrington', 'Wingate Benson', 'Forbes Spaulding', 'Raleigh Foy', 'Jack Burlingame', 'Waylon Altham', 'Herbert Royston', 'Quintin Sutton', 'Abraham Harding');

	public function fakeName($name = null) {
		if(!$name) {
			$name = choice(FakeData::FAKE_NAMES);
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

	function fakeDomain() {
		static $fake_domains = array('163.com', '51.la', 'about.com', 'addthis.com', 'adobe.com', 'amazon.co.uk', 'amazon.com', 'ameblo.jp', 'aol.com', 'apple.com', 'baidu.com', 'bbc.co.uk', 'bing.com', 'blogger.com', 'blogspot.com', 'clickbank.net', 'cnn.com', 'creativecommons.org', 'dailymotion.com', 'delicious.com', 'digg.com', 'ebay.com', 'etsy.com', 'europa.eu', 'facebook.com', 'fc2.com', 'feedburner.com', 'flickr.com', 'forbes.com', 'free.fr', 'geocities.com', 'gnu.org', 'go.com', 'godaddy.com', 'goo.gl', 'google.co.jp', 'google.co.uk', 'google.com', 'google.de', 'gov.uk', 'guardian.co.uk', 'homestead.com', 'huffingtonpost.com', 'hugedomains.com', 'icio.us', 'imdb.com', 'instagram.com', 'issuu.com', 'jimdo.com', 'joomla.org', 'linkedin.com', 'livejournal.com', 'macromedia.com', 'mail.ru', 'mapquest.com', 'microsoft.com', 'miibeian.gov.cn', 'mozilla.org', 'msn.com', 'myspace.com', 'networkadvertising.org', 'nih.gov', 'nytimes.com', 'photobucket.com', 'pinterest.com', 'qq.com', 'rambler.ru', 'reddit.com', 'reuters.com', 'sina.com.cn', 'slideshare.net', 'sourceforge.net', 'statcounter.com', 'stumbleupon.com', 't.co', 'taobao.com', 'technorati.com', 'theguardian.com', 'tinyurl.com', 'tripod.com', 'tumblr.com', 'twitter.com', 'typepad.com', 'vimeo.com', 'vk.com', 'w3.org', 'washingtonpost.com', 'webs.com', 'weebly.com', 'weibo.com', 'wikipedia.org', 'wix.com', 'wordpress.com', 'wordpress.org', 'wsj.com', 'yahoo.co.jp', 'yahoo.com', 'yandex.ru', 'youtu.be', 'youtube.com');
		return choice($fake_domains);
	}

	function fakeMac() {
		return \implode(':',\str_split(\substr(\md5(\mt_rand()),0,12),2));
	}

	function fakeEmail($name = null, $domain = null) {
		$n = $this->fakeName($name);
		$domain = $domain? $domain: fake_domain();
		return $n->simple_name."@".$domain;
	}

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
