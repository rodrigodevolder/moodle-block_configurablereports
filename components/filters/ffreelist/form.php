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

/** Configurable Reports - Component Filter Freelist
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
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class ffreelist_form extends moodleform {

    function definition() {
        global $remotedb;

        $mform = & $this->_form;

        $mform->addElement('header', 'crformheader', $this->lang_string('ffreelist'), '');

        $this->_customdata['compclass']->add_form_elements($mform, $this);
        
        $mform->addElement('text', 'label', $this->lang_string('label'));
        $mform->setType('label', PARAM_RAW);

        $mform->addElement('textarea', 'field', get_string('field', 'block_configurable_reports'));

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }
	
	private function lang_string($str) {
        global $CFG;

		$file_lang = $CFG->dirroot .'/blocks/configurable_reports/components/filters/ffreelist/language/'. current_language() .'.php';
		if(!file_exists($file_lang)) $file_lang = $CFG->dirroot .'/blocks/configurable_reports/components/filters/ffreelist/language/en.php';
		include($file_lang);

		return $string[$str];
	}
}
