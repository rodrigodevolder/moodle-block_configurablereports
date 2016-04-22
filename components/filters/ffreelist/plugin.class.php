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

/** Configurable Reports - Component Filter ffreelist
 * Este opção de filtro é baseado na lista montada pelo usuário onde cada linha
 * é um registro e o ponto e virgula (;) separa o value da escrição.
 * 
 * Tag para sql: %%FILTER_FREELIST:campo_comparação%%
 *  
 * @package component
 * @author: Rafael Soares
 * @author: Rodrigo Devolder
 * @sponsor: e-create.com.br
 * @date: 2015-2016
 */
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

class plugin_ffreelist extends plugin_base {

    function init() {
		
		$this->form = true;
        $this->unique = true;
        $this->fullname = $this->lang_string('ffreelist');
        $this->reporttypes = array('filterffreelist', 'sql');
    }

    function summary($data) {
        return $data->label . '<br>' . $data->field;
    }

    function execute($finalelements, $data) {

        $filter_ffreelist = optional_param('filter_ffreelist', 0, PARAM_INT);
        if (!$filter_ffreelist)
            return $finalelements;

        if ($this->report->type != 'sql') {
            return array($filter_ffreelist);
        } else {
            if (preg_match("/%%FILTER_FREELIST:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filter_ffreelist;
                return str_replace('%%FILTER_FREELIST:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }

    function print_filter(&$mform, $data) {
        $ffreelist = array();
        $ffreelist[0] = get_string('filter_all', 'block_configurable_reports');

        $list = explode("\n", str_replace("\r", "", $data->field));       
        foreach ($list as $key => $value) {
            $explode = explode(";", $value);                        
            $ffreelist[$explode[0]] = $explode[1];            
        }                                        
        
        $mform->addElement('select', 'filter_ffreelist', $data->label, $ffreelist);
        $mform->setType('filter_ffreelist', PARAM_INT);
    }
	
	private function lang_string($str) {
        global $CFG;
		
		$file_lang = $CFG->dirroot .'/blocks/configurable_reports/components/filters/ffreelist/language/'. current_language() .'.php';
		if(!file_exists($file_lang)) $file_lang = $CFG->dirroot .'/blocks/configurable_reports/components/filters/ffreelist/language/en.php';
		include($file_lang);
		
		return $string[$str];
	}
}
