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

require_once($CFG->dirroot.'/blocks/configurable_reports/plugin.class.php');

class plugin_advancedsql extends plugin_base{

	function init(){
		$this->form = true;
		$this->unique = false;
		$this->fullname = $this->lang_string('filteradvancedsql','block_configurable_reports');
		$this->reporttypes = array('advancedsql','sql');
	}

	function summary($data){
		return $this->lang_string('filteradvancedsql_summary','block_configurable_reports');
	}

	function execute($finalelements, $data){
		if(substr($data->identifier, 0, 18) != 'FILTER_ADVANCEDSQL') print_error('erro: tipo identifier invalido.');

		$filter_advancedsql = optional_param(strtolower($data->identifier), '', PARAM_RAW);

		if(!$filter_advancedsql)
			return $finalelements;

		if ($this->report->type != 'sql') {
            return array($filter_advancedsql);
		} else {
			if (preg_match("/%%{$data->identifier}:([^%]+)%%/i",$finalelements, $output)) {
				$replace = " AND {$output[1]} = $filter_advancedsql";
				return str_replace("%%{$data->identifier}:{$output[1]}%%", $replace, $finalelements);
			}
		}

		return $finalelements;
	}

	function print_filter(&$mform, $data){
		global $remotedb, $CFG;

		$reportclassname = 'report_'.$this->report->type;
		$reportclass = new $reportclassname($this->report);

		$components = cr_unserialize($reportclass->config->components);
		$filters = (isset($components['filters']['elements']))? $components['filters']['elements'] : array();

		$finalelements = trim($data->field, ';');
		if(strpos(';', $finalelements)!== false) print_error('erro: mais de um select.');
		
		if(!empty($filters)){
			foreach($filters as $f){
				if(isset($f['formdata']->identifier) && $f['formdata']->identifier == $data->identifier) continue;
				require_once($CFG->dirroot.'/blocks/configurable_reports/components/filters/'.$f['pluginname'].'/plugin.class.php');
				$classname = 'plugin_'.$f['pluginname'];
				$class = new $classname($reportclass->config);
				$execute = $class->execute($finalelements,$f['formdata']);
				
				if(isset($f['formdata']->identifier) && $f['formdata']->identifier == $data->dependency && $execute == $finalelements) {
					$this->print_filter_end($mform, $data, Array());
					return null;
				}

				$finalelements = $execute;
			}
		}

		$arr = array();
		$results = $reportclass->execute_query($finalelements);
        foreach($results as $obj_value) {
            $arr_value = (Array)$obj_value;
			$key = array_shift($arr_value);
			$value = array_shift($arr_value);
			$arr[$key] = $value;
        }
		
		$this->print_filter_end($mform, $data, $arr);
	}

	private function print_filter_end (&$mform, $data, $arr) {
        $mform->addElement('select', strtolower($data->identifier), $data->label, $arr);
        $mform->setType(strtolower($data->identifier), PARAM_RAW);
	}
	
	public function lang_string($str) {
        global $CFG;

		$file_lang = $CFG->dirroot .'/blocks/configurable_reports/components/filters/advancedsql/language/'. current_language() .'.php';
		if(!file_exists($file_lang)) $file_lang = $CFG->dirroot .'/blocks/configurable_reports/components/filters/advancedsql/language/en.php';
		include($file_lang);

		return $string[$str];
	}
}
