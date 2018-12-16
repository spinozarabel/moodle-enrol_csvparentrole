This plugin allows you synchronize roles (relative to users such as parent, mentor) from a CSV file to Moodle.

THIS PLUGIN IS IN BETA STATUS! BE CAREFULL WITH PRODUCTION ENVIRONMENT! Use at own risk!

Plugin has been tested on Moodle 3.4, 3.5, 3.6.

Use this piece of code at your own risk :), absolutely no warranty! I am not an expert Moodle developer


In the configuration, "Subject" represents for example: parent user or mentor user, and "Object" represents the student user, desired role can be for example: parent, mentor.
In your CSV file the 1st row must have column headings. Thsese should correspond to settings in your plugin.
The student and parent user information correspond to unique Moodle user information such as: username, email, ID, system id, etc.
For example, using the defaults:
parentusername,studentusername,desiredrole
parent1username,student1username,parent
parent2username,student2username,parent

The CSV file can be located anywhere on the server provided it can be read by www-data. It is possible for the CSV file to be located in Google Drive.
If so it should be published to web as CSV file (readable by anyone who has link). Use the full link as the file name.

Note that this is a full synchronization but one way. The reference is the CSV file and Moodle follows this.

HOW TO INSTALL
==============
Prerequisites
a. CSV file containing data of users and desired role association
c. parents and students must already be in Moodle (having user accounts, that is)
d. desired role (for example: parent, mentor) must be already exist in Moodle

1. Download all the files in the directory {MOODLE_DIR}/enrol/csvparentrole (using git, GitHub website, or anything else)
2. Go to http://{MOODLE_URL}/admin to complete the installation
3. Fill all parameters using Moodle plugin administration interface (http://{MOODLE_URL}/admin/settings.php?section=enrolsettingscsvparentrole

How to synchronize:
1. Setup a cron job to execute {MOODLE_DIR}/enrol/csvparentrole/cli/sync.php (add -v for more output, -s to simulate only and redirect output to log file)
2.To do it on demand, go to csvparentrole/cli and execute command: php sync.php <-v for verbose) <-s for simulation only>

Feel free to send me any comments/suggestions

Madhu Avasarala, SriToni Learning Services, <info@headstart.edu.in>

This plugin was first developed by Penny Leach <penny@catalyst.net.nz> for Moodle 1.9
by modified by Max Pelletier to work with Moodle 2.3. Modified by https://github.com/mfuhrmeisterDM for 2.9 and Madhu Avasarala for 3.4.
It was originally using External DB method and this was modified by Madhu Avasarala for CSV file.
