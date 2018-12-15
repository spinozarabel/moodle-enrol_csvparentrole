<?php 
/**
 * CLI sync for full external database synchronisation.
 *
 * Sample cron entry:
 * # 5 minutes past 4am
 * 5 4 * * * $sudo -u www-data /usr/bin/php /var/www/moodle/enrol/csvparentrole/cli/sync.php
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * @package    enrol
 * @subpackage csvparentrole
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @copyright  Penny Leach <penny@catalyst.net.nz>
 * @copyright  Maxime Pelletier <maxime.pelletier@educsa.org>
 * @copyright  Madhu Avasarala <info@headstart.edu.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', true);
	
require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');// global moodle config file.
require_once($CFG->libdir.'/clilib.php');
// now get cli options
list($options, $unrecognized) = cli_get_params(array('verbose'=>false, 'help'=>false, 'simulate'=>false), array('v'=>'verbose', 'h'=>'help', 's'=>'simulate'));
if ($unrecognized) {
	$unrecognized = implode("\n  ", $unrecognized);
	cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Execute csvparentrole enrol sync with external database.
The enrol_csvparentrole plugin must be enabled and properly configured.
Options:
-v, --verbose         Print verbose progess information
-h, --help            Print out this help
-s, --simulate        simulates the synchronisation
Example:
\$sudo -u www-data /usr/bin/php enrol/csvparentrole/cli/sync.php
Sample cron entry:
# 5 minutes past 4am
5 4 * * * \$sudo -u www-data /usr/bin/php /var/www/moodle/enrol/csvparentrole/cli/sync.php
";
    	echo $help;
    	die;
}

if (!enrol_is_enabled('csvparentrole')) {
	echo('enrol_csvparentrole plugin is disabled, sync is disabled'."\n");
    	exit(1);
}

if(!empty($_SERVER['GATEWAY_INTERFACE'])){
        error_log("should not be called from apache!");
        exit(1);
}
	
	
$verbose = !empty($options['verbose']);
$simulate = !empty($options['simulate']);
$enrol = enrol_get_plugin('csvparentrole');
$result = 0;
	
$result = $result | $enrol->setup_enrolments($verbose, $simulate);
exit($result);
