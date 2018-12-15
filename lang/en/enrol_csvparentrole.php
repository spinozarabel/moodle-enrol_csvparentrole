<?php
$string['pluginname'] = 'csvparentrole';


$string['csvfilefullpath'] = 'CSV filename';
$string['csvfilefullpath_desc'] = 'Filename of CSV with full path-can also give Google CSV file if published, as CSV with full link';

$string['description'] = 'You can use an external CSV file to control your relationships between users. Your CSV file line contains two user IDs, and a Role ID';
$string['enrolname'] = 'CSV file (User relationships)';
$string['localrolefield'] = 'Local role field';
$string['localsubjectuserfield'] = 'Local subject field';
$string['localobjectuserfield'] = 'Local object field';
$string['localrolefield_desc'] = 'The ID type of the role in Moodle roles table that we are using (eg shortname).';
$string['localsubjectuserfield_desc'] = 'The name of the field in the user table that we are using to match entries in the CSV (eg username) for the <i>subject</i> role assignment';
$string['localobjectuserfield_desc'] = 'The name of the field in the user table that we are using to match entries in the CSV (eg username) for the <i>object</i> role assignment';
$string['remote_fields_mapping'] = 'CSV file Column headings map';
$string['remoterolefield'] = 'CSV file role heading';
$string['remotesubjectuserfield'] = 'Column heading in CSV file of subject (parent/mentor)';
$string['remoteobjectuserfield'] = 'Column heading in CSV file of object (Student)';
$string['remoterolefield_desc'] = 'Column heading in the CSV file for desired user context role assignment';
$string['remotesubjectuserfield_desc'] = 'The colum heading in CSV file containing the <i>subject</i> (parent/mentor)';
$string['remoteobjectuserfield_desc'] = 'The colum heading in CSV file containing the <i>object</i> (student)';

$string['pluginname_desc'] = 'This method will check for and process a CSV text file in the location that you specify.
The file is a comma separated file assumed to have three fields per line:

    parent userID, student userID, desired role

where:

* parent userID - is the ID of a user who needs to be assigned role parent
* student userID - is the ID of user on to whose user context this parent role is assigned
* desired role - is the desired role to abe assigned (either parent or mentor)

Note that the 1st row must be a heading row. These should correspond to values in the settings.
The ID itself depends on type specified in settings: example: username, email, ID, etc.
In following example we have chosen username as ID type.

It could look something like this:
<pre class="informationbox">
   parentusername,studentusername,desiredrole
   tejas.avasarala,surya.avasarala,parent
   tejaswini.avasarala,espirit.avasarala,parent
</pre>';
$string['settingscsvfile'] = 'External CSV file';
$string['remoteenroltable'] = 'ignore';
$string['remoteenroltable_desc'] = 'ignore';
