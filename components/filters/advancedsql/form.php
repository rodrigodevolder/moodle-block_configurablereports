<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/** Configurable Reports
  * filters/advancedsql plugin
  *
  * @package component
  * @author: Rodrigo Devolder (github.com/rodrigodevolder)
  * @sponsor: e-create.com.br
  * @date: 2016
  */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class advancedsql_form extends moodleform {
    function definition() {
        global $remotedb, $course, $CFG;
		
		$plugin = new plugin_advancedsql($this->_customdata['reportclass']->config);
		
        $mform =& $this->_form;

        $mform->addElement('header', 'crformheader', $plugin->lang_string('filteradvancedsql'), '');
		
		$mform->addElement('text', 'label', $plugin->lang_string('label'));
        $mform->setType('label', PARAM_RAW);

        $mform->addElement('select', 'identifier', $plugin->lang_string('identifier'), $this->get_arr_identifiers());
        $mform->setType('identifier', PARAM_RAW);

        $mform->addElement('select', 'dependency', $plugin->lang_string('dependency'), $this->get_arr_dependences());
        $mform->setType('dependency', PARAM_RAW);

        $mform->addElement('textarea', 'field', get_string('querysql', 'block_configurable_reports'), 'rows="15" cols="80"');
        $mform->addRule('field', get_string('required'), 'required', null, 'client');
        $mform->setType('field', PARAM_RAW);

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }
	
	function validation($data, $files) {
		$plugin = new plugin_advancedsql($this->_customdata['reportclass']->config);
		
		$num_identifier = substr($data['identifier'], -2);
		$num_dependency = substr($data['dependency'], -2);
		
		if($num_identifier * 1 <= $num_dependency * 1) return Array('dependency' => $plugin->lang_string('dependencyhigher'));
		
		return array();
    }
	
	private function get_others_identifiers () {
		$cid = optional_param('cid', '', PARAM_RAW);

		$elements = Array();
		$components = cr_unserialize($this->_customdata['report']->components);
		$filters = (isset($components['filters']['elements']))? $components['filters']['elements'] : array();
		if(!empty($filters)){
			foreach($filters as $f){
				if(isset($f['id']) && $f['id'] != $cid) {
					$key = $f['formdata']->identifier;
					$elements[$key] = $key;
				}
			}
		}

		return $elements;
	}
	
	private function get_arr_identifiers () {
		$elements = $this->get_others_identifiers();
		
		$arr = Array();
		for($i = 1; $i < 100; $i++) {
			$key = ($i < 10) ? "FILTER_ADVANCEDSQL0$i" : "FILTER_ADVANCEDSQL$i";
			if(!in_array($key, $elements)) $arr[$key] = $key;
        }

		return $arr;
	}
		
	private function get_arr_dependences () {
		$plugin = new plugin_advancedsql($this->_customdata['reportclass']->config);
		$elements = $this->get_others_identifiers();
		return array_merge(Array('nodependency' => $plugin->lang_string('nodependency')), $elements);
	}
}

