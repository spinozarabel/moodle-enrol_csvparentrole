<?php
/**
 * User role assignment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage csvparentrole
 * @copyright  Penny Leach <penny@catalyst.net.nz>
 * @copyright  Maxime Pelletier <maxime.pelletier@educsa.org>
 * @copyright Madhu Avasarala <info@headstart.edu.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
//--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_csvparentrole_settings', '', get_string('pluginname_desc', 'enrol_csvparentrole')));

    $settings->add(new admin_setting_heading('enrol_csvparentrole_csvheader', get_string('settingscsvfile', 'enrol_csvparentrole'), ''));

//  $options = array();
//  $options = array_combine($options, $options);

//  $settings->add(new admin_setting_configselect('enrol_csvparentrole/dbtype', get_string('dbtype', 'enrol_csvparentrole'), get_string('dbtype_desc', 'enrol_csvparentrole'), 'mysqli', $options));

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/csvfilefullpath', get_string('csvfilefullpath', 'enrol_csvparentrole'), get_string('csvfilefullpath_desc', 'enrol_csvparentrole'), ''));

//  $settings->add(new admin_setting_configtext('enrol_csvparentrole/dbuser', get_string('dbuser', 'enrol_csvparentrole'), '', ''));

//  $settings->add(new admin_setting_configpasswordunmask('enrol_csvparentrole/dbpass', get_string('dbpass', 'enrol_csvparentrole'), '', ''));

//  $settings->add(new admin_setting_configtext('enrol_csvparentrole/dbname', get_string('dbname', 'enrol_csvparentrole'), '', ''));

//  $settings->add(new admin_setting_configtext('enrol_csvparentrole/dbencoding', get_string('dbencoding', 'enrol_csvparentrole'), '', 'utf-8'));
    
//  $settings->add(new admin_setting_configtext('enrol_csvparentrole/remoteenroltable', get_string('remoteenroltable', 'enrol_csvparentrole'), get_string('remoteenroltable_desc', 'enrol_csvparentrole'), ''));

    $settings->add(new admin_setting_heading('enrol_csvparentrole_remoteheader', get_string('remote_fields_mapping', 'enrol_csvparentrole'), ''));

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/localsubjectuserfield', get_string('localsubjectuserfield', 'enrol_csvparentrole'), get_string('localsubjectuserfield_desc', 'enrol_csvparentrole'), 'username'));

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/localobjectuserfield', get_string('localobjectuserfield', 'enrol_csvparentrole'), get_string('localobjectuserfield_desc', 'enrol_csvparentrole'), 'username'));		

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/localrolefield', get_string('localrolefield', 'enrol_csvparentrole'), get_string('localrolefield_desc', 'enrol_csvparentrole'), 'shortname'));	

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/remotesubjectuserfield', get_string('remotesubjectuserfield', 'enrol_csvparentrole'), get_string('remotesubjectuserfield_desc', 'enrol_csvparentrole'), 'parentusername'));	

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/remoteobjectuserfield', get_string('remoteobjectuserfield', 'enrol_csvparentrole'), get_string('remoteobjectuserfield_desc', 'enrol_csvparentrole'), 'studentusername'));		

    $settings->add(new admin_setting_configtext('enrol_csvparentrole/remoterolefield', get_string('remoterolefield', 'enrol_csvparentrole'), get_string('remoterolefield_desc', 'enrol_csvparentrole'), 'desiredrole'));
}
