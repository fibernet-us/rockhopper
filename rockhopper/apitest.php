<?php

require_once 'connect.php';
require_once 'user.php';
require_once 'project.php';
require_once 'team.php';

function printUserTable() {
    global $dbh;
    $users = User::getAllUsers($dbh);
    //var_dump($users);
    foreach($users as $user) {
        echo "<p>" . $user . "</p>";
    }
}

function printProjectTable() {
    global $dbh;
    $projs = Project::getAllProjects($dbh);
    //var_dump($users);
    foreach($projs as $proj) {
        echo "<p>" . $proj . "</p>";
    }
}

function printTeamTable() {
    global $dbh;
    $teams = Team::getAllTeams($dbh);
    //var_dump($users);
    foreach($teams as $team) {
        echo "<p>" . $team . "</p>";
    }
}

function printTeamMemberTable( $teamObj) {
    global $dbh;
    $users = Team::getTeamMembers($dbh, $teamObj);
    //var_dump($users);
    foreach($users as $user) {
        echo "<p>" . $user . "</p>";
    }
}


echo '<html>';
echo '<body>';

#
# test Class User
#

/****************************************************************************
 * Class User
 * 
 * public functions
 * ================
 * 
 * getId()  
 * getUsername()        
 * getFullname() 
 * getPassword()  
 * getEmail()  
 * getType()      
 * getStatus()    
 * getTimezone()   
 * getLocation()    
 * getIconUrl()     
 * getEnabled()  
 * getTypeString() 
 * getStatusString()
 * 
 * setDbHandle($dbh)   
 * setUsername($u)      
 * setFullname($f)      
 * setPassword($p)       
 * setEmail($e)          
 * setType($t)           
 * setStatus($s)         
 * setTimezone($z)       
 * setLocation($l)       
 * setIconUrl($i)        
 * setEnabled($b)        
 * 
 * 
 * public static functions
 * =======================
 * 
 * isUsernameRegistered($dbh, $username)
 * isEmailRegistered($dbh, $email) 
 * getUser($dbh, $username, $password) 
 * getUserByEmail($dbh, $email, $password) 
 * getAllUsers($dbh)
 * addUser($dbh, $username, $fullname, $password, $email, $timezone, $type, $status, $location, $iconurl) 
 * removeUser($dbh, $username) 
 * removeAllUsers($dbh)
 * 
 ***************************************************************************/


echo "<p style=\"color:red;\"><b>User table in the begining:</b></p>";
printUserTable();

//echo "<p><b>User table after truncation (should be empty):</b></p>";
//User::removeAllUsers($dbh);
//printUserTable();

echo "<p style=\"color:red;\"><b>User table after adding user1, user2, user3:</b></p>";
User::addUser($dbh, "user1", "user one", "pass1", "user1@mail.com", -6);
User::addUser($dbh, "user2", "user two", "pass2", "user2@mail.com", 0);
User::addUser($dbh, "user3", "user san", "pass3", "user3@mail.com", 8, 0, 0, "Beijing", "img/user3.jpg");
printUserTable();

echo "<p><b>Get 'user1' by username:</b></p>";
$username = 'user1';
$password = 'pass1';
$user = User::getUser($dbh, $username, $password);
echo "<p>" . $user . "</p>";

echo "<p><b>Get 'user1' by email 'user1@mail.com':</b></p>";
$email = 'user1@mail.com';
$password = 'pass1';
$user = User::getUserByEmail($dbh, $email, $password);
echo "<p>" . $user . "</p>";

echo "<p><b>User 1 after full name and type change:</b></p>";
$user->setFullname("user uno");
$user->setType(User::TYPE_RH_ADMIN);
$user->setStatus(User::STATUS_WORKING);
echo "<p>" . $user;

echo "<p style=\"color:red;\"><b>User table after user2 removed:</b></p>";
User::removeUser($dbh, 'user2');
printUserTable();

echo "<p><b>Check if username \"user1\" is registered:</b></p>";
if(User::isUsernameRegistered($dbh, "user1"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Check if username \"user2\" is registered:</b></p>";
if(User::isUsernameRegistered($dbh, "user2"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Check if username \"user3\" is registered:</b></p>";
if(User::isUsernameRegistered($dbh, "user3"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Add new user with username \"user3\":</b></p>";
if(User::addUser($dbh, "user3", "user t", "pass9", "usert@mail.com", 0))
	echo "<p>Successful</p>";
else
	echo "<p>Failed</p>";

echo "<p><b>Check if email \"user1@mail.com\" is registered: </b></p>";
if(User::isEmailRegistered($dbh, "user1@mail.com"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";

echo "<p><b>Check if email \"user2@mail.com\" is registered: </b></p>";
if(User::isEmailRegistered($dbh, "user2@mail.com"))
	echo "<p>Yes</p>";
else
	echo "<p>No</p>";


#
# test Class Team
#
/****************************************************************************
* Class Team
*
*  public function getId()
*  public function getName()
*  public function getDescription()
*  public function getStatus()
*  public function getIconUrl()
*  public function getStatusString()
*
*  public function setDbHandle($dbh)
*  public function setName($n)
*  public function setDescription($d)
*  public function setStatus($s)
*  public function setIconUrl($i)
*
*  public static function getTeamById($dbh, $id)
*  public static function getTeamByName($dbh, $name)
*  public static function getAllTeams($dbh)
*  public static function isNameUsed($dbh, $name)
*  public static function addTeam($dbh, $name, $description)
*  public static function removeTeamById($dbh, $id)
*  public static function removeTeamByname($dbh, $name)
*  public static function removeAllTeams($dbh)
*
*  // team member operations
*
*  public static function getTeamMemberIds($dbh, $teamObj)
*  public static function getTeamMembers($dbh, $teamObj)
*  public static function addMember($dbh, $teamObj, $userObj)
*  public static function removeMember($dbh, $teamObj, $userObj)
*  public static function removeAllMembers($dbh, $teamObj)
*
****************************************************************************/

echo "<p style=\"color:red;\"><b>Team table in the begining:</b></p>";
printTeamTable();

echo "<p style=\"color:red;\"><b>Team table after adding team 1 and 2:</b></p>";
Team::addTeam($dbh, "team1", "team one");
Team::addTeam($dbh, "team2", "team two");
printTeamTable();

echo "<p><b>Team1 members after adding two:</b></p>";
$team1 = Team::getTeamByName($dbh, "team1");
Team::addMember($dbh, $team1, User::getUserById($dbh, "1"));
Team::addMember($dbh, $team1, User::getUserByUsername($dbh, "user1"));
printTeamMemberTable($team1);


#
# test Class Project
#
/*****************************************************************************
*  Class Project
*
*  public function getId()
*  public function getName()
*  public function getDescription()
*  public function getStatus()
*  public function getStatusString()
*  public function getStartDate()
*  public function getDeadline()
*  public function getLud()
*  public function getIconUrl()
*  public function getOwnerId()
*
*  public function setDbHandle($dbh)
*  public function setName($n)
*  public function setDescription($d)
*  public function setStatus($s)
*  public function setStartDate($S){ return $this->update('S', $S);  }
*  public function setDeadline($D) { return $this->update('D', $D);  }
*  public function setLud($L)  { return $this->update('L', $L);  }
*  public function setIconUrl($i)  { return $this->update('i', $i);  }
*  public function setOwnerId($o)  { return $this->update('o', $o);  }
*
*  public static function getProjectById($dbh, $id)
*  public static function getProjectByName($dbh, $name)
*  public static function getAllProjects($dbh)
*  public static function isNameUsed($dbh, $name)
*  public static function addProject($dbh, $name, $description, $owner_id)
*  public static function removeProjectById($dbh, $id)
*  public static function removeProjectByname($dbh, $name)
*  public static function removeAllProjects($dbh)
*
****************************************************************************/

echo "<p style=\"color:red;\"><b>Project table in the begining:</b></p>";
printProjectTable();
echo "<p style=\"color:red;\"><b>Project table after adding two:</b></p>";
Project::addProject($dbh, "projecta", "project admin", "1");
Project::addProject($dbh, "project2", "project two", "2");
printProjectTable();

echo '</body>';
echo '</html>';


