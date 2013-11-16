<?php

require_once 'user.php';

/**
 * ****************************************************************************
 *
 * Class Team 
 * 
 * Store rockhopper Team info and provide related queries and operations
 * 
 * @author   Wen Bian
 * @version  1.00
 * @history
 *   11/10/2013: added properties, getters and setters
 *   11/15/2013: added team member operations
 *                               
 */
class Team {

    private $id;
    private $name;
    private $description;
    private $status;
    private $icon_url;
    
    private $dbh;  
    private static $teamNameList = array(); // for fast name availability check
    
    const TEAM_TABLE = 'RH_TEAM';
    const TEAM_MEMBER_TABLE = 'RH_TEAM_MEMBER';
    const TEAM_CLASS = 'Team';
    
	const ICON_PATH = 'usericons/';
	const DEFAULT_ICON = 'usericons/team.jpg';
     
	const STATUS_UNKNOWN    = 0;
	const STATUS_WORKING    = 1;
	const STATUS_IDLE       = 2;
	const STATUS_DISMISSED  = 3;
	
    /**
     * A new Team is created through the static method(s) addTeam(),
     * while an existing Team is obtained through the static method getTeam().
     */
    private function __construct() {}
    
    /** magic set */
    public function  __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __toString() {
        return     "id=" .            $this->id 
               . "; name=" .          $this->name 
               . "; description=" .   $this->description 
               . "; status=" .        $this->getStatusString()          
               . "; icon_url=" .       $this->icon_url; 
    }

       
    /**************************************************************************
     * getters
     *************************************************************************/
    
    public function getId()           { return $this->id;            }
    public function getName()         { return $this->name;          }
    public function getDescription()  { return $this->description;   }
    public function getStatus()       { return $this->status;        }
    public function getIconUrl()      { return $this->icon_url;       }


    /** return string for staus */
    public function getStatusString() {
        switch($this->status) {
        	case self::STATUS_UNKNOWN:   return "unknown";
        	case self::STATUS_WORKING:   return "working";
        	case self::STATUS_IDLE:      return "idle";
        	case self::STATUS_DISMISSED: return "dismissed";
        }
    }
    
    
    /**************************************************************************
     * setters
     *************************************************************************/
    
    public function setDbHandle($dbh)   { return $this->dbh = $dbh;       }
    public function setName($n)         { return $this->update('n', $n);  }
    public function setDescription($d)  { return $this->update('d', $d);  }
    public function setStatus($s)       { return $this->update('s', $s);  }
    public function setIconUrl($i)      { return $this->update('i', $i);  }
    
	
    /**************************************************************************
     * public static function section
     *************************************************************************/
    
    
    /**
     * @return a Team object given its ID
     */
    public static function getTeamById($dbh, $id) {
        
        $sql = 'SELECT * FROM ' . self::TEAM_TABLE 
             . ' WHERE id = :id';
        
        $stmt = $dbh->prepare($sql);    
        $stmt->execute(array(':id' => $id));
        
        $teams = $stmt->fetchAll(PDO::FETCH_CLASS, self::TEAM_CLASS);
        
        if(empty($teams)) {
            return false;
        }
        else {
            return $teams[0];
        }
    }

    /**
     * @return a Team object given its name
     */
    public static function getTeamByName($dbh, $name) {
    
        $sql = 'SELECT * FROM ' . self::TEAM_TABLE
        . ' WHERE name = :name';
    
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(':name' => $name));
    
        $teams = $stmt->fetchAll(PDO::FETCH_CLASS, self::TEAM_CLASS);
    
        if(empty($teams)) {
            return false;
        }
        else {
            return $teams[0];
        }
    }
    

    /**
     * @return an array of all Teams
     */
    public static function getAllTeams($dbh) {
        $sql = 'SELECT * FROM ' . self::TEAM_TABLE;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        
        $teams = $stmt->fetchAll(PDO::FETCH_CLASS, self::TEAM_CLASS);
        return $teams;
    }
    
    
    /**
     * Fast check if a name is registered
     */
    public static function isNameUsed($dbh, $name) {
        if(empty(self::$teamNameList)) {   
            $sql = 'SELECT name FROM ' . self::TEAM_TABLE;   
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            self::$teamNameList = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }
    
        return in_array($name, self::$teamNameList);
    }
    
    
    /**
     * Add a Team: name and description are required
     */
    public static function addTeam($dbh, $name, $description, 
                                      $status = self::STATUS_UNKNOWN,
                                      $icon_url = self::DEFAULT_ICON) {
           
        if(self::isNameUsed($dbh, $name)) {
            return false;
        }
        
        $sql = 'INSERT INTO ' . self::TEAM_TABLE
             . '(name, description, status, icon_url) '
             . ' VALUES '
             . '(:name, :description, :status, :icon_url)';
        
        $stmt = $dbh->prepare($sql);

        // $stmt->execute() returns true on success
        return $stmt->execute(array(':name'          => $name, 
                                    ':description'   => $description,
                                    ':status'        => $status,
                                    ':icon_url'       => $icon_url
                                   ));        
    }

    
    /**
     * Delete a Team given its ID
     */
    public static function removeTeamById($dbh, $id) {
        $sql = 'DELETE FROM ' . self::TEAM_TABLE . ' WHERE id = :id';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':id' => $id)); 
    }
    
    /**
     * Delete a Team given its name
     */
    public static function removeTeamByname($dbh, $name) {
        $sql = 'DELETE FROM ' . self::TEAM_TABLE . ' WHERE name = :name';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':name' => $name));
    }
    

    /**
     * Delete all Teams
     * TODO: require extra verification
     */
    public static function removeAllTeams($dbh) {
        $sql = 'TRUNCATE TABLE ' . self::TEAM_TABLE;
        $stmt = $dbh->prepare($sql);
        return $stmt->execute();
    }
 
    
    /**************************************************************************
     * team member operation
    *************************************************************************/
    
    /**
     * @return an array of member IDs of a Team
     */
    public static function getTeamMemberIds($dbh, $teamObj) {
        if(is_null($teamObj)) {
            return false;
        }
        
        $sql = 'SELECT * FROM ' . self::TEAM_MEMBER_TABLE
            . ' WHERE team_id = :team_id';
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(
                ':team_id' => $teamObj->getId()
        ));
    
        return  $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    }
    
    /**
     * @return an array of members (Users) of a Team
     */
    public static function getTeamMembers($dbh, $teamObj) {
        
        $memberIDs = self::getTeamMemberIds($dbh, $teamObj);
        if(empty($memberIDs)) {
            return false;
        }
        
        $userList = array();
        foreach ($memberIDs as $uid) {
            $user = User::getUserById($dbh, $uid);
            if($user) {
                $userList[] = $user;
            }
        }
        
        return $userList;
    }
    
    
    /**
     * Add a member (User) to a team
     */
    public static function addMember($dbh, $teamObj, $userObj) {
        if(is_null($teamObj) || is_null($userObj)) {
            return false;
        }
    
        $sql = 'INSERT INTO ' . self::TEAM_MEMBER_TABLE
               . '(team_id, user_id) '
               . ' VALUES '
               . '(:team_id, :user_id)';
    
        $stmt = $dbh->prepare($sql);
    
        // $stmt->execute() returns true on success
        return $stmt->execute(array(
                ':team_id' => $teamObj->getId(),
                ':user_id' => $userObj->getId()
        ));
    }
    
    
    /**
     * Remove a member from a Team
     */
    public static function removeMember($dbh, $teamObj, $userObj) {
        if(is_null($teamObj) || is_null($userObj)) {
            return false;
        }
        
        $sql = 'DELETE FROM ' . self::TEAM_MEMBER_TABLE 
               . ' WHERE team_id = :team_id AND user_id = :user_id';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(
                  ':team_id' => $teamObj->getId(),
                  ':user_id' => $userObj->getId()
        ));
    }
    

    /**
     * Delete all members of a give Team
     * TODO: require extra verification
     */
    public static function removeAllMembers($dbh, $teamObj) {
        if(is_null($teamObj)) {
            return false;
        }
        
        $sql = 'DELETE FROM ' . self::TEAM_MEMBER_TABLE 
               . ' WHERE team_id = :team_id';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':team_id' => $teamObj->getId()));
    }
    
    
        
        
    /**************************************************************************
     * private function section
     *************************************************************************/
    
    // Update a property given its name initial and new value
    private function update($property, $newValue) {
        // get property name
        switch($property) {
            case 'd': $propertyName = 'description';
                      $propertyref = &$this->description;
                      break;
            case 'i': $propertyName = 'icon_url';
                      $propertyref = &$this->icon_url;
                      break;
            case 'n': $propertyName = 'name';
                      $propertyref = &$this->name;
                      break;                                   
            case 's': $propertyName = 'status';
                      $propertyref = &$this->status;
                      break; 
            default: return false;
        }
    
        $sql = 'UPDATE ' . self::TEAM_TABLE
               . ' SET ' . $propertyName . ' = :newValue' . ' WHERE id = :id';
        
        $stmt = $this->dbh->prepare($sql);
    
        if($stmt->execute(array(':newValue' => $newValue, ':id' => $this->id))) {
            $propertyref = $newValue;
            return TRUE;
        }
        else {
            return false;
        }
    }
    
    
} // class Team
