<?php namespace Clips\Libraries\Sass; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 *	Author: andy
 *	Date: Three  1/14 11:22:25 2015
 *
 * 	Three support resolutions type
 *	$resolutions = array(320, 480, 640);
 *	$resolutions = array(320=>'device1', 480, 640);
 *	$resolutions = array('device1'=>320, 480, 640);
 */

class AutoResolution extends SassPlugin {

    public function prefix($compiler) {
        $this->appendVariables($this->getResolutions($compiler),
            $compiler);
    }

    protected function getSortedResolutions($compiler) {
        $resolutions = $this->getResolutions($compiler);
        $ret = $this->analyzeResolutions($resolutions);
        usort($ret[0], function($a, $b) {
            if($a['value'] == $b['value'])
                return 0;
            if($a['value'] > $b['value'])
                return 1;
            if($a['value'] < $b['value'])
                return -1;
            return false;
        });
        $result = $ret[0];
        $resolutions = array();
        $prev_value = null;
        foreach($result as $r) {
            $value = $r['value'];

            if($prev_value) {
                $t = $r;
                $t['value'] = $t['value'] - 1;

                $res = array(
                    'value' => $t,
                    'prev_value' => $prev_value,
                    'section' => true
                );

                $this->_process('before_section', $compiler->content, $res, $prev_value['value'].'_'.$value);
                $this->_process('prepend_section', $compiler->content, $res, $prev_value['value'].'_'.$value);
                $this->_process('append_section', $compiler->content, $res, $prev_value['value'].'_'.$value);
                $this->_process('after_section', $compiler->content, $res, $prev_value['value'].'_'.$value);
                $resolutions []= $res;
            }

            $res = array(
                'value' => $r,
                'resolution' => true
            );

            $this->_process('before_resolution', $compiler->content, $res, $value);
            $this->_process('prepend_resolution', $compiler->content, $res, $value);
            $this->_process('append_resolution', $compiler->content, $res, $value);
            $this->_process('after_resolution', $compiler->content, $res, $value);

            $resolutions []= $res;
            $prev_value = $r;
        }
        return $resolutions;
    }

    protected function _process($name, $content, $res, $value = null) {
        $n = $name;
        if($value) {
            $n = $name.'_'.$value;
        }
        if(strpos($content, $n) !== FALSE) {
            $res[$name] = $n;
            return true;
        }
        return false;
    }

    protected function getSasses($sasses, $content) {
        $ret = array();
        foreach($sasses as $s) {
            // Get the script name
            $s = str_replace('.scss', '', $s);
            $basename = basename($s);
            $name = str_replace('/', '_', $s);
            if($basename != $name) {
                $names = explode('/', $s);
                $index = array_search('scss', $names);
                if ($index) {
                    array_splice($names, 0, $index+1);
                    $name = implode('_', $names);
                }
            }

            if(strpos($content, 'responsive_'.$name) !== FALSE) {
                $ret []['responsive_con']= 'responsive_'.$name;
            }

            if(strpos($content, 'section_'.$name) !== FALSE) {
                $ret []['section_con'] = 'section_'.$name;
            }

            if(strpos($content, 'module_'.$name) !== FALSE) {
                $ret []['module_con'] = 'module_'.$name;
            }
        }
        return $ret;
    }

    public function suffix($compiler) {
        $data = array(
            'before' => strpos($compiler->content, 'before_responsive') !== FALSE,
            'after' => strpos($compiler->content, 'after_responsive') !== FALSE,
            'resolutions' => $this->getSortedResolutions($compiler),
            'sasses' => $this->getSasses($compiler->sasses, $compiler->content)
        );

        $compiler->suffix .= \Clips\clips_out('media', $data, false);
    }

    protected function analyzeResolutions($resolutions) {
        $result = array();
        $min = 0;
        $max = 0;
        foreach($resolutions as $k => $v) {
            $arr = null;
            if(is_string($k)) {
                $arr = array('alias' => $k, 'value' => $v);
            }
            else {
                if(is_numeric($v)) {
                    $arr = array('alias' => 0, 'value' => $v);
                }
                else {
                    $arr = array('alias' => $v, 'value' => $k);
                }
            }
            $result []= $arr;
            if($min > $arr['value'])
                $min = $arr['value'];

            if($max < $arr['value'])
                $max = $arr['value'];
        }
        return array($result, $min, $max);
    }

    protected function appendVariables($resolutions, $compiler) {
        if (is_array($resolutions)) {
            $ret = $this->analyzeResolutions($resolutions);
            $result = $ret[0];
            $min = $ret[1];
            $max = $ret[2];

            $str = 'string://$min-screen-width: {{min}};
	$max-screen-width: {{max}};
	$init-min-screen-width: {{min}};
	$init-max-screen-width: {{max}};

	$pinet-resolutions: (
{{#resolutions}}
{{#if alias}}
	( {{alias}} : {{value}} )
{{else}}
	{{value}}
{{/if}},
{{/resolutions}}
	);
	$pinet-alias: (
{{#resolutions}}
	{{alias}},
{{/resolutions}}
	);
	$pinet-no-alias-resolutions: (
{{#resolutions}}
	{{value}},
{{/resolutions}}
	);
';

            $compiler->prefix .= \Clips\clips_out($str, array(
                'min' => $min,
                'max' => $max,
                'resolutions' => $result
            ), false);
        }
    }

    protected function getResolutions($compiler) {
        if (\Clips\context('resolutions') && is_array(\Clips\context('resolutions'))) {
            return \Clips\context('resolutions');
        } else {
            foreach(array('\Clips\get_default', '\Clips\config') as $func) {
                $res = null;
                if($func == '\Clips\get_default') {
                    $res = \Clips\get_default($compiler, 'resolutions', null);
                }
                else {
                    $res = call_user_func_array($func, array('resolutions', null));
                }
            }
            if($res) {
                return $res;
            }
        }
        trigger_error('you must need resolutions, you can put in clips_config or ci_config or complier');
        return false;
    }
}
