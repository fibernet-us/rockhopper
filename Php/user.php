<?php

include 'passcrypt.php';

/**
 * ****************************************************************************
 *
 * Class User 
 * 
 * Store rockhopper user info and provide user related queries and operations
 * 
 * @author   Wen Bian
 * @version  1.03
 * @history
 *   09/06/2013: added properties, getters and setters
 *   09/09/2013: added static methods for add, remove, get, getAll user(s)         
 *   09/11/2013: changed DB access from mysqli to PDO
 *   09/12/2013: added removeAllUsers(). tested every function.
 *   09/13/2013: added password hashing.
 *   09/13/2013: added methods for fast username/email availability check
 *                               
 */
class User {

    private $id;
    private $username;
    private $fullname;
    private $passhash;
    private $salt;
    private $email;
    private $type;
    private $status;
    private $timezone;
    private $location;
    private $iconurl;
    private $enabled;
    //private $last_activity_id;
    
    private $dbh;  // database connection handle (PDO)
    
    // for fast username availability check
    private static $usernameList = array();
    private static $emailList = array();
    
    const USER_TABLE = 'RH_USER';
    
    const TYPE_UNKNOWN        = 0;
    const TYPE_RH_ADMIN       = 1;
    const TYPE_PROJECT_OWNER  = 2;
    const TYPE_DEV            = 3;
    const TYPE_SCRUM_MASTER   = 4;
    const TYPE_CHICKEN        = 5;
        
    const STATUS_UNKNOWN      = 0;
    const STATUS_WORKING      = 1;
    const STATUS_IDLE         = 2;
    const STATUS_ON_LEAVE     = 3;
    const STATUS_LEFT         = 4;
    
  

    /**
     * A new User is created through the static method(s) addUser(),
     * while an existing User is obtained through the static method getUser().
     */
    private function __construct() {}
    
    /** magic set */
    public function  __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __toString() {
        return   "id=" . $this->id 
               . "; username=" . $this->username 
               . "; fullname=" . $this->fullname 
               . "; passhash=" . $this->passhash 
               . "; email=" . $this->email
               . "; type=" . $this->getTypeString()
               . "; status=" . $this->getStatusString()
               . "; timezone=" . $this->timezone
               . "; location=" . $this->location
               . "; iconurl=" . $this->iconurl
               . "; enabled=" . $this->enabled;
    }
    
    
    /**************************************************************************
     * getters
     *************************************************************************/
    
    public function getId()              { return $this->id;               }
    public function getUsername()        { return $this->username;         }
    public function getFullname()        { return $this->fullname;         }
    public function getEmail()           { return $this->email;            }
    public function getType()            { return $this->type;             }
    public function getStatus()          { return $this->status;           }
    public function getTimezone()        { return $this->timezone;         }
    public function getLocation()        { return $this->location;         }
    public function getIconUrl()         { return $this->iconurl;          }
    public function getEnabled()         { return $this->enabled;          }
    //public function getLastActivityId()  { return $this->last_activity_id; }

    /** return string for type */
    public function getTypeString () {
        switch($this->type) {
            case self::TYPE_UNKNOWN:       return "unknown";
            case self::TYPE_RH_ADMIN:      return "admin";
            case self::TYPE_PROJECT_OWNER: return "project owner";
            case self::TYPE_DEV:           return "developer";
            case self::TYPE_SCRUM_MASTER:  return "scrum master";
            case self::TYPE_CHICKEN:       return "chicken";
        }
    }
    
    /** return string for staus */
    public function getStatusString () {
        switch($this->status) {
            case self::STATUS_UNKNOWN:   return "unknown";
            case self::STATUS_WORKING:   return "working";
            case self::STATUS_IDLE:      return "idle";
            case self::STATUS_ON_LEAVE:  return "on leave";
            case self::STATUS_LEFT:      return "left";
        }
    }
    
    
    /**************************************************************************
     * setters
     *************************************************************************/
    
    public function setDbHandle($dbh)     { return $this->dbh = $dbh;      }
    public function setUsername($u)       { return $this->update('u', $u);  }
    public function setFullname($f)       { return $this->update('f', $f);  }
    public function setEmail($e)          { return $this->update('e', $e);  }
    public function setType($t)           { return $this->update('t', $t);  }
    public function setStatus($s)         { return $this->update('s', $s);  }
    public function setTimezone($z)       { return $this->update('z', $z);  }
    public function setLocation($l)       { return $this->update('l', $l);  }
    public function setIconUrl($i)        { return $this->update('i', $i);  }
    public function setEnabled($b)        { return $this->update('b', $b);  }
    //public function setLastActivityId($a) { return $this->update('a', $a);  }   

    
    /** 
     * Change password. Hash it with a sale and store both in DB
     */
    public function setPassword($password) {
        list($passhash, $salt) = self::getPassAndSalt($password);

        $sql = 'UPDATE ' . self::USER_TABLE
             . ' SET passhash = :passhash, salt = :salt' 
             . ' WHERE id = :id';
        $stmt = $this->dbh->prepare($sql);
        
        // $stmt->execute() returns true on success
        if($stmt->execute(array(':passhash' => $passhash, 
                                ':salt'     => $salt,
                                ':id'       => $this->id))) {
            return TRUE;
        }
        else {
            return FALSE;
        }        
    }
 
    
    
    /**************************************************************************
     * public static function section
     *************************************************************************/
    

    /**
     * Fast check if a username is registered
     */
    public static function isUsernameRegistered($dbh, $username) {
        if(empty(self::$usernameList)) {
            $sql = 'SELECT username FROM ' . self::USER_TABLE;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            self::$usernameList = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            //echo "<p><font color=red><b>query db for username list</b></font></p>";
        }
    
        return in_array($username, self::$usernameList);
    }
    
    
    /**
     * Fast check if an email is registered
     */
    public static function isEmailRegistered($dbh, $email) {
        if(empty(self::$emailList)) {
            $sql = 'SELECT email FROM ' . self::USER_TABLE;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            self::$emailList = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            //echo "<p><font color=red><b>query db for email list</b></font></p>";
        }
    
        return in_array($email, self::$emailList);
    }
    
    
    /**
     * Return a user object given username and password
     */
    public static function getUser($dbh, $username, $password) {
        
        $sql = 'SELECT * FROM ' . self::USER_TABLE 
             . ' WHERE username = :username';
        
        $stmt = $dbh->prepare($sql);    
        $stmt->execute(array(':username' => $username));
        
        $users = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
        if(empty($users)) {
            return false;
        }
        
        $passhash = $users[0]->getPasshash();
        $salt = $users[0]->getSalt();
        if(self::verifyPassword($password, $passhash, $salt)) {   
            $users[0]->setDbHandle($dbh);
            return $users[0];
        }
        else {
            return FALSE;
        }
    }

    
    /**
     * Get all users
     * @return numeric array of user
     */
    public static function getAllUsers($dbh) {
        $sql = 'SELECT * FROM ' . self::USER_TABLE;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        
        $users = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
        return $users;
    }
    
    
    /**
     * Add a user. username, fullname, password, email and timezon are required.
     */
    public static function addUser($dbh, $username, $fullname, $password, $email, $timezone,  
                                   $type = self::TYPE_UNKNOWN, $status = self::STATUS_UNKNOWN,
                                   $location = NULL, $iconurl = NULL) {

        list($passhash, $salt) = self::getPassAndSalt($password); 
           
        $sql = 'INSERT INTO ' . self::USER_TABLE
             . ' (username, fullname, passhash, salt, email, type, status, timezone, location, iconurl) '
             . ' VALUES '
             . ' (:username, :fullname, :passhash, :salt, :email, :type, :status, :timezone, :location, :iconurl)';
        
        $stmt = $dbh->prepare($sql);

        // $stmt->execute() returns true on success
        return $stmt->execute(array(':username' => $username, 
                                    ':fullname' => $fullname,
                                    ':passhash' => $passhash,
                                    ':salt'     => $salt,
                                    ':email'    => $email,
                                    ':type'     => $type,
                                    ':status'   => $status,
                                    ':timezone' => $timezone,
                                    ':location' => $location,
                                    ':iconurl'  => $iconurl,
                                   ));        
    }

    
    /**
     * Delete a user given username
     */
    public static function removeUser($dbh, $username) {
        $sql = 'DELETE FROM ' . self::USER_TABLE . ' WHERE username = :username';
        $stmt = $dbh->prepare($sql);
        
        // $stmt->execute() returns true on success
        return $stmt->execute(array(':username' => $username)); 
    }

    /**
     * Delete all users
     * TODO: require extra 
     */
    public static function removeAllUsers($dbh) {
        $sql = 'TRUNCATE TABLE ' . self::USER_TABLE;
        $stmt = $dbh->prepare($sql);
    
        // $stmt->execute() returns true on success
        return $stmt->execute();
    }

    
    /**************************************************************************
     * private function section
     *************************************************************************/
    
    // Update a property given its name initial and new value
    private function update($property, $newValue) {
        // get property name
        switch($property) {
            //case 'a': $propertyName = 'last_activity_id';
            //          $propertyref = &$this->last_activity_id;
            //          break;
            case 'b': $propertyName = 'enabled';
                      $propertyref = &$this->enabled;
                      break;
            case 'e': $propertyName = 'email';
                      $propertyref = &$this->email;
                      break;
            case 'f': $propertyName = 'fullname';
                      $propertyref = &$this->fullname;
                      break;
            case 'i': $propertyName = 'iconurl';
                      $propertyref = &$this->iconurl;
                      break;
            case 'l': $propertyName = 'lacation';
                      $propertyref = &$this->lacation;
                      break;
            case 's': $propertyName = 'status';
                      $propertyref = &$this->status;
                      break;
            case 't': $propertyName = 'type';
                      $propertyref = &$this->type;
                      break;
            case 'u': $propertyName = 'username';
                      $propertyref = &$this->username;
                      break;
            case 'z': $propertyName = 'timezone';
                      $propertyref = &$this->timezone;
                      break;
            default: return FALSE;
        }
    
        $sql = 'UPDATE ' . self::USER_TABLE
        . ' SET ' . $propertyName . ' = :newValue'
                . ' WHERE id = :id';
        $stmt = $this->dbh->prepare($sql);
    
        // $stmt->execute() returns true on success
        if($stmt->execute(array(':newValue' => $newValue, ':id' => $this->id))) {
            $propertyref = $newValue;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    
    // enscrype a given password with a random salt 
    // and return both the hashed password and the salt
    private static function getPassAndSalt($password) {
        // password_hash() available only after 5.5.0
        // return password_hash($password, PASSWORD_DEFAULT);
        return Passcrypt::hashPassword($password);  // Array($passhash, $salt)
    }
    
    
    // check if a plain password matches its hashed version
    private static function verifyPassword($password, $passhash, $salt) {
        return Passcrypt::verifyPassword($password, $passhash, $salt);
    }
       

    protected function getPasshash()      { return $this->passhash;  }
    protected function getSalt()          { return $this->salt;      }
    
    
} // class User
