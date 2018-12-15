This plugin allows you to configure automatic relationships between users using a CSV file.  

THIS PLUGIN IS IN BETA STATUS! BE CAREFULL WITH PRODUCTION ENVIRONMENT! Use at own risk!

Plugin has been tested on Moodle 3.4, 3.5, 3.6.

This plugin was first developed by Penny Leach <penny@catalyst.net.nz> for Moodle 1.9
by modified by Max Pelletier to work with Moodle 2.3. Modified by https://github.com/mfuhrmeisterDM for 2.9 and Madhu Avasarala for 3.4.
It was originally using External DB method and this was modified by MA for CSV file.

Use this piece of code at your own risk :), absolutely no warranty!

In the configuration, "Subject" represent the parent/mentor, and "Object" represent the student, desired role "parent" or "mentor"
In your CSV file the 1st row must have column headings. Thsese should correspond to settings in your plugin.
The student and parent information corresponds to unique Moodle information about them such as username or email of user id etc.
For example:
parentname,studentname,desiredrole
parent1username,student1username,parent
parent2username,student2username,parent

It is possible for the file to be located in Google Drive. It should be published to web as CSV file (readable by anyone who has link).
Use the full link as the file name.

HOW TO INSTALL
==============
Prerequisis
a. CSV file containing data of users and desired role association
c. parents and students must already be in Moodle (having user accounts that is)
d. desired role must be already exist in Moodle

1. Download all the files in the directory {MOODLE_DIR}/enrol/csvparentrole (using git, GitHub website, or anything else)
2. Go to http://{MOODLE_URL}/admin to complete the installation
3. Fill all parameters using Moodle plugin administration interface (http://{MOODLE_URL}/admin/settings.php?section=enrolsettingscsvparentrole
4. Setup a cron job to execute {MOODLE_DIR}/enrol/csvparentrole/cli/sync.php (add -v for more output, and redirecte output to log file)

Feel free to send me any comments/suggestions

Madhu Avasarala, SriToni Learning Services, info@headstart.edu.in
