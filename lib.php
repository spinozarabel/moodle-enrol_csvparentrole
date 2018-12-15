<?php  // $Id$
/**
 * User role assignment plugin. ver 0.2
 *
 * This plugin synchronises user roles with external CSV file.
 *
 * @package    enrol
 * @subpackage csvparentrole
 * @copyright  Madhu Avasarala, SriToni Learning Services <info@headstart.edu.in>
 * @copyright  Penny Leach <penny@catalyst.net.nz>
 * @copyright  Maxime Pelletier <maxime.pelletier@educsa.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class enrol_csvparentrole_plugin extends enrol_plugin {

    var $log;

    /**
     * Is it possible to delete enrol instance via standard UI?
     *
     * @param object $instance
     * @return bool
     */
    // the function below had been deprecated and replaced with new function name can_delete_instance
    public function can_delete_instance($instance) {
        if (!enrol_is_enabled('csvparentrole')) {
            return true;
			}
        if (!$this->get_config('csvfilefullpath') or !$this->get_config('remoteenroltable') or !$this->get_config('remoteuserfield')) {
            return true;
			}

        //TODO: connect to external system and make sure no users are to be enrolled in this course
        return false;
    }

    /**
     * Does this plugin allow manual unenrolment of a specific user?
     * Yes, but only if user suspended...
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $ue record from user_enrolments table
     *
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol this user, false means nobody may touch this user enrolment
     */
    public function allow_unenrol_user(stdClass $instance) {
   
        return true;
		}

/*
 * MAIN FUNCTION
 * The algorithm is as follows
 * The CSV file is read into an associative array. The array consists of subarrays equal to total number of lines (minus 1 for the header)
 * Each subarray consists of 3 elements keyed by the column headings. These headings are mapped in the config settings of plugin
 * The moodle user role assignments are read using SQL. These are only those already established by this plugin and none others.
 * We form an array of these records with key as desiredrole | parent | student and call this $existing
 * Next a unique key is formed consisting of desiredrole | parent | student for each line of the CSV file. 
 * We look to see if this key is present in Moodle already, that is in $existing. If so we ignore, if not we add this user role assignment.
 * All user role assignments in $existing (that is already in Moodle) BUT NOT in the CSV file will be unassigned, at the end.
 * These role assignments can only be unassigned using this plugin. Manually doing this from UI is not possible because in the
 * role_assign statement we write out the component name, 'enrol_csvparentrole'. If this were not so unassign using UI would've been possible
 * 
 * @param bool $verbose
 * @return int 0 means success, 1 db connect failure, 2 db read failure
 */
function setup_enrolments($verbose = false, &$user=null) {
    global $CFG, $DB;

    if ($verbose) {
		mtrace('Starting user enrolment synchronisation...');
		}

    if ($verbose) {
		mtrace("Starting CSV file access");
		}
	$file = $this->get_config('csvfilefullpath');  // get the CSV file name and location from config settings for this plugin
    
	// read and parse this file into an associative array using the heading row as keys. The heading row itself is otherwise ignored
	// $csv[0] correspomds to line 2 of the CSV file.
		
    $csv = csv_to_associative_array($file);
    
    // we may need a lot of memory here
    // the time limit statement below replaces the old @set_time_limit(0)
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_HUGE);

    // Store the field values in some shorter variable names to ease reading of the code.
    $flocalsubject  = strtolower($this->get_config('localsubjectuserfield'));
    $flocalobject   = strtolower($this->get_config('localobjectuserfield'));
    $flocalrole     = strtolower($this->get_config('localrolefield'));
    $fremotesubject = strtolower($this->get_config('remotesubjectuserfield')); 
	$fremotesubject_proper = $this->get_config('remotesubjectuserfield'); 
	//strtolower was messing up column references, so use "proper" casing and replace usages below that refer to columns (generally $row['whatever'])
	$fremoteobject_proper  = $this->get_config('remoteobjectuserfield');
    $fremoteobject  = strtolower($this->get_config('remoteobjectuserfield'));
    $fremoterole    = strtolower($this->get_config('remoterolefield'));
    $dbtable        = $this->get_config('remoteenroltable');

    if ($verbose) {
		mtrace(count($csv) ." entries in the CSV file: should be total lines in CSV minus one for the headings row");
        }

	// Unique identifier of the role assignment that we look for later in
	$uniqfield = $DB->sql_concat("r.$flocalrole", "'|'", "u1.$flocalsubject", "'|'", "u2.$flocalobject");
		
	// Query to retreive all user role assignments from Moodle using this plugin
	$sql = "SELECT $uniqfield AS uniq,
            ra.*, r.{$flocalrole} ,
            u1.{$flocalsubject} AS subjectid,
            u2.{$flocalobject} AS objectid
            FROM {role_assignments} ra
            JOIN {role} r ON ra.roleid = r.id
            JOIN {context} c ON c.id = ra.contextid
            JOIN {user} u1 ON ra.userid = u1.id
            JOIN {user} u2 ON c.instanceid = u2.id
            WHERE ra.component = 'enrol_csvparentrole' 
			AND c.contextlevel = " . CONTEXT_USER;
            //(!empty($user) ?  " AND c.instanceid = {$user->id} OR ra.userid = {$user->id}" : '');

		// are there any user role assigments from this plugin in Moodle?
		// The first column is used as the key
	if (!$existing = $DB->get_records_sql($sql)) {
		$existing = array();
        }

	if ($verbose) {
		mtrace(sizeof($existing)." role assignement entries from enrol plugin: csvparentrole, found in Moodle DB");
        }

	// Is there something in the CSV file process only of records found otherwise exit?
	if (count($csv)) {

        // get an array of all the roles existing in Moodle using for ex: their shortname (this is selectable in plugin config settings)
		$roles = $DB->get_records('role', array(), '', "$flocalrole, id", 0, 0);
	
		if ($verbose) {
			mtrace(sizeof($roles)." roles found in Moodle DB");
			}

		$subjectusers = array(); // cache of mapping of localsubjectuserfield to mdl_user.id (for get_context_instance)
		$objectusers = array(); // cache of mapping of localsubjectuserfield to mdl_user.id (for get_context_instance)
		$contexts = array(); // cache
		$rels = array();
			
			
            // We loop through all the records of the remote CSV
		foreach ($csv as $rownumber => $row ) {		

			if ($verbose) {
				//print_r($row);
				//mtrace("Role:".$row[$fremoterole]);
				}

			// TODO: Handle coma seperated values in remoteobject field
			// either we're assigning ON the current user, or TO the current user
			$key = $row[$fremoterole] . '|' . $row[$fremotesubject] . '|' . $row[$fremoteobject];							
				
			// Check if the role is already assigned
			if (array_key_exists($key, $existing)) {
				// exists in moodle db already, unset it (so we can delete everything left)
				unset($existing[$key]);
				error_log("Warning: [$key] exists in moodle already");
				if ($verbose) {
					mtrace("already exists, skipping: " .$key);
					}
				continue;
				}

			// Check if the role specified in CSV file record exists in Moodle. If not skip this CSV record
			if (!array_key_exists($row[$fremoterole], $roles)) {
				// role doesn't exist in moodle. skip.
				error_log("Warning: role " . $row[$fremoterole] . " wasn't found in moodle.  skipping $key");
				if ($verbose) {
					mtrace("Warning: role " . $row[$fremoterole] . " wasn't found in moodle.  skipping $key");
					}
				continue;
				}
				
			// Fill the subject array: subject is parent or mentor usually
			if (!array_key_exists($row[$fremotesubject_proper], $subjectusers)) {
				$subjectusers[$row[$fremotesubject_proper]] = $DB->get_field('user', 'id', array($flocalsubject => $row[$fremotesubject_proper]) );
				}
				
			// Check if subject specified in CSV record, exists in Moodle: if not skip this CSV record
			if ($subjectusers[$row[$fremotesubject_proper]] == false) {
				error_log("Warning: [" . $row[$fremotesubject_proper] . "] couldn't find subject user -- skipping $key");
				if ($verbose) {
					mtrace("Warning: [" . $row[$fremotesubject_proper] . "] couldn't find parent/mentor user -- skipping $key");
					}
				continue;
				}

			// Fill the object array, sually the student
			if (!array_key_exists($row[$fremoteobject_proper], $objectusers)) {
				$objectusers[$row[$fremoteobject_proper]] = $DB->get_field('user', 'id', array($flocalobject => $row[$fremoteobject_proper]) );
				}
				
			// Check if object exist in Moodle, if not also skip this record
			if ($objectusers[$row[$fremoteobject_proper]] == false) {
				// couldn't find user, skip
				error_log("Warning: [" . $row[$fremoteobject_proper] . "] couldn't find object user --  skipping $key");
				if ($verbose) {
					mtrace("Warning: [" . $row[$fremoteobject_proper] . "] couldn't find student user --  skipping $key");
					}
				continue;
				}
				
			// Get the context of the object (usually student user)
			$context = context_user::instance($objectusers[$row[$fremoteobject_proper]]);
			if ($verbose) {
				mtrace("Information: [" . $row[$fremotesubject_proper] . "] assigning " . $row[$fremoterole] . " to remote user " . $row[$fremotesubject_proper]
						   . " on " . $row[$fremoteobject_proper]);
				}

			// MOODLE 2.X => role_assign($roleid, $userid, $contextid, $component = '', $itemid = 0, $timemodified = '')
			// we are writing the component value 'enrol_csvparentrole' in the following.
			// This means that we will be unable to unassign this role by manually or by anyother plugin other than this.
			role_assign($roles[$row[$fremoterole]]->id, $subjectusers[$row[$fremotesubject_proper]], $context->id, 'enrol_csvparentrole', 0, '');

			}  // end foreach,loope through all rows from remote csv

	if ($verbose) {
		mtrace("Deleting existing user role assignations in Moodle not found in CSV file");
		}
	// delete everything left in $existing
	// Note that you will not get here if count($csv) =0. So you must have atleast one record other than the heading row
	// It is advisable to always maintain one dummy record that triggers this role_unassign
	foreach ($existing as $key => $assignment) {
		if ($assignment->component == 'enrol_csvparentrole') {
			if ($verbose) {
				mtrace("Information: [$key] unassigning $key");
				}
                    // MOODLE 1.X => role_unassign($assignment->roleid, $assignment->userid, 0, $assignment->contextid);
			role_unassign($assignment->roleid, $assignment->userid, $assignment->contextid, 'enrol_csvparentrole', 0);
			}
        }
	} else {
		error_log('Warning: [ENROL_CSVPARENTROLE] Couldn\'t get rows from CSV file -- no relationships to assign');
		if ($verbose) {
			mtrace('Warning: Couldn\'t get rows from CSV file -- no relationships to assign');
			}
		}
    }


  /**
   * Forces synchronisation of user enrolments with external database,
   * does not create new courses.
   *
   * @param stdClass $user user record
   * @return void
   *
  public function sync_user_enrolments($user) {
    $this->setup_enrolments(false, $user);
  }
  */

} // end of class ------------------------------------------------

/**
 * This routine is attributed to https://github.com/rap2hpoutre/csv-to-associative-array
  *
 * The items in the 1st line (column headers) become the fields of the array
 * each line of the CSV file is parsed into a sub-array using these fields
 * The 1st index of the array is an integer pointing to these sub arrays
 * The 1st row of the CSV file is ignored and index 0 points to 2nd line of CSV file
 * This is the example data:
 *
 * parentname,studentname,desiredrole
 * sritoni4,sritoni2,parent
 * sritoni5,sritoni3,parent
 *
 * This is the associative array
 * Array
 *(
 *  [0] => Array
 *      (
 *          [parentname] => sritoni4
 *          [studentname] => sritoni2
 *          [desiredrole] => parent
 *      )
 *  [1] => Array
 *      (
 *          [parentname] => sritoni5
 *          [studentname] => sritoni3
 *          [desiredrole] => parent
 *      )
 */
function csv_to_associative_array($file, $delimiter = ',', $enclosure = '"') {
    if (($handle = fopen($file, "r")) !== false) {
        $headers = fgetcsv($handle, 0, $delimiter, $enclosure);
        $lines = [];
        while (($data = fgetcsv($handle, 0, $delimiter, $enclosure)) !== false) {
            $current = [];
            $i = 0;
            foreach ($headers as $header) {
                $current[$header] = $data[$i++];
            }
            $lines[] = $current;
        }
        fclose($handle);
        return $lines;
		}
	}
