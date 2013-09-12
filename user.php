<?php

/**
 * ****************************************************************************
 *
 * Class User 
 * 
 * Store rockhopper user info and provide user related queries and operations
 * 
 * @author   Wen Bian
 * @version  1.02
 * @history
 *   09/06/2013: added properties, getters and setters
 *   09/09/2013: added static methods for add, remove, get, getAll user(s)         
 *   09/11/2013: changed DB access from mysqli to PDO
 *               
 * 
 */
class User {

    private $id;
    private $username;
    private $fullname;
    private $passwd;
    private $email;
    private $type;
    private $status;
    private $timezone;
    private $location;
    private $iconurl;
    private $last_activity_id;
    
    private $dbh;  // database connection handle (PDO)
    
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
    
    // getters 
    public function getId()              { return $this->$id;               }
    public function getUsername()        { return $this->$username;         }
    public function getFullname()        { return $this->$fullname;         }
    public function getPassword()        { return $this->$passwd;           }
    public function getEmail()           { return $this->$email;            }
    public function getType()            { return $this->$type;             }
    public function getStatus()          { return $this->$status;           }
    public function getTimezone()        { return $this->$timezone;         }
    public function getLocation()        { return $this->$location;         }
    public function getIconUrl()         { return $this->$iconurl;          }
    public function getEnabled()         { return $this->$enabled;          }
    public function getLastActivityId()  { return $this->$last_activity_id; }

    // setters 
    public function setDbHandle($dbh)     { return $this->$dbh = $dbh;      }
    public function setUsername($u)       { return $this->update('u', $u);  }
    public function setFullname($f)       { return $this->update('f', $f);  }
    public function setPassword($p)       { return $this->update('p', $p);  }
    public function setEmail($e)          { return $this->update('e', $e);  }
    public function setType($t)           { return $this->update('t', $t);  }
    public function setStatus($s)         { return $this->update('s', $s);  }
    public function setTimezone($z)       { return $this->update('z', $z);  }
    public function setLocation($l)       { return $this->update('l', $l);  }
    public function setIconUrl($i)        { return $this->update('i', $i);  }
    public function setEnabled($b)        { return $this->update('b', $b);  }
    public function setLastActivityId($a) { return $this->update('a', $a);  }   
    
    // Update a property given its name initial and new value
    private function update($property, $newValue) {
        // get property name
        switch($property) {
            case 'a': $propertyName = 'last_activity_id'; break;
            case 'b': $propertyName = 'enabled'; break;            
            case 'e': $propertyName = 'email'; break;
            case 'f': $propertyName = 'fullname'; break;
            case 'i': $propertyName = 'iconurl'; break;  
            case 'l': $propertyName = 'lacation'; break;                
            case 'p': $propertyName = 'passwd'; break;
            case 's': $propertyName = 'status'; break;
            case 't': $propertyName = 'type'; break;
            case 'u': $propertyName = 'username'; break;
            case 'z': $propertyName = 'timezone'; break;
            default: return FALSE;
        }
        
        $sql = 'UPDATE ' . self::USER_TABLE 
             . ' SET ' . $propertyName . ' = :newValue' 
             . ' WHERE id = :id';
        $stmt = $dbh->prepare($sql);
        
        // $stmt->execute() returns true on success
        if($stmt->execute(array(':newValue' => newValue, ':id' => $this->$id))) {
            this->$propertyName = $newValue;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    
    ////////////////////////// static functions ///////////////////////////////
    
    
    /**
     * Return a user object given username and password
     */
    public static function getUser($dbh, $username, $passwd) {
        $sql = 'SELECT * FROM ' . self::USER_TABLE 
             . ' WHERE username = :username AND passwd = :passwd';
        $stmt = $dbh->prepare($sql);    
        $stmt->execute(array(':name' => $username, ':passwd' => $passwd));
        $user = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
        $user->setDbHandle($dbh);
        return $user;
    }

    
    /**
     * Get all users
     * @return numeric array of user
     */
    public static function getAllUsers($dbh) {
        $sql = 'SELECT * FROM ' . self::USER_TABLE;
        $stmt = $dbh->prepare($sql);
        $users = $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
        return $users;
    }
    
    
    /**
     * Add a user given required attributes
     */
    public static function addUser($dbh, $username, $fullname, $passwd, $email, $timezone) {
         
        $type = self::TYPE_UNKNOWN;
        $status = self::STATTUS_UNKNOWN;
        $location = NULL;
        $iconurl = NULL;
        
        return self::addUser($dbh, $username, $fullname, $passwd, $email, $timezone,
                                   $type, $status, $location, $iconurl);
    }
    
    
    /**
     * Add a user given all attributes
     */
    public static function addUser($dbh, $username, $fullname, $passwd, $email, $timezone,  
                                         $type, $status, $location, $iconurl) {
        
        $sql = 'INSERT INTO ' . self::USER_TABLE
             . ' (username, fullname, passwd, email, type, status, timezone, location, iconurl) '
             . ' VALUES '
             . ' (:username, :fullname, :passwd, :email, :type, :status, :timezone, :location, :iconurl)';
        
        $stmt = $dbh->prepare($sql);
        
        // $stmt->execute() returns true on success
        return $stmt->execute(array(':username' => $username, 
                                    ':fullname' => $fullname,
                                    ':passwd'   => $passwd,
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
    public static function removeUser($username) {
        $sql = 'DELETE FROM ' . self::USER_TABLE . ' WHERE username = :username';
        $stmt = $dbh->prepare($sql);
        
        // $stmt->execute() returns true on success
        return $stmt->execute(array(':name' => $username)); 
    }

}
