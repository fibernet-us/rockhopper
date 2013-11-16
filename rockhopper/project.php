<?php

/**
 * ****************************************************************************
 *
 * Class Project 
 * 
 * Store rockhopper Project info and provide related queries and operations
 * 
 * @author   Wen Bian
 * @version  1.00
 * @history
 *   11/12/2013: added properties, getters and setters
 *                               
 */
class Project {

    private $id;
    private $name;
    private $description;
    private $status;
    private $date_start;
    private $date_deadline;
    private $date_lud;
    private $icon_url;
    private $owner_id;
    
    private $dbh;  
    private static $projectNameList = array(); // for fast name availability check   

    const PROJECT_TABLE = 'RH_PRODUCT';
    const PROJECT_CLASS = 'Project';
    
	const ICON_PATH = 'usericons/';
	const DEFAULT_ICON = 'usericons/project.jpg';
        
    const STATUS_NOT_READY    = 0;
    const STATUS_READY        = 1;
    const STATUS_IN_PROGRESS  = 2;
    const STATUS_PAUSED       = 3;
    const STATUS_TESTING      = 4;
    const STATUS_COMPLETED    = 5;
    const STATUS_ABORTED      = 6;

    /**
     * A new Project is created through the static method(s) addProject(),
     * while an existing Project is obtained through the static method getProject().
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
               . "; date_start=" .    $this->date_start
               . "; date_deadline=" . $this->date_deadline
               . "; date_lud=" .      $this->date_lud               
               . "; icon_url=" .       $this->icon_url
               . "; owner_id=" .      $this->owner_id; 
    }
    
    
    /**************************************************************************
     * getters
     *************************************************************************/
    
    public function getId()           { return $this->id;            }
    public function getName()         { return $this->name;          }
    public function getDescription()  { return $this->description;   }
    public function getStatus()       { return $this->status;        }
    public function getStartDate()    { return $this->date_start;    }
    public function getDeadline()     { return $this->date_deadline; }
    public function getLud()          { return $this->date_lud;      }
    public function getIconUrl()      { return $this->icon_url;       }
    public function getOwnerId()      { return $this->owner_id;      }
    
    
    /** return string for staus */
    public function getStatusString() {
        switch($this->status) {
            case self::STATUS_NOT_READY:   return "not ready";
            case self::STATUS_READY:       return "ready";
            case self::STATUS_IN_PROGRESS: return "in progress";
            case self::STATUS_PAUSED:      return "paused";
            case self::STATUS_TESTING:     return "testing";
            case self::STATUS_COMPLETED:   return "completed";
            case self::STATUS_ABORTED:     return "aborted";
        }
    }
    	
    
    /**************************************************************************
     * setters
     *************************************************************************/
    
    public function setDbHandle($dbh)   { return $this->dbh = $dbh;       }
    public function setName($n)         { return $this->update('n', $n);  }
    public function setDescription($d)  { return $this->update('d', $d);  }
    public function setStatus($s)       { return $this->update('s', $s);  }
    public function setStartDate($S)    { return $this->update('S', $S);  }
    public function setDeadline($D)     { return $this->update('D', $D);  }
    public function setLud($L)          { return $this->update('L', $L);  }
    public function setIconUrl($i)      { return $this->update('i', $i);  }
    public function setOwnerId($o)      { return $this->update('o', $o);  }
    
	
	
    /**************************************************************************
     * public static function section
     *************************************************************************/
    
    /**
     * @return a Project object given its ID
     */
    public static function getProjectById($dbh, $id) {
        
        $sql = 'SELECT * FROM ' . self::PROJECT_TABLE 
             . ' WHERE id = :id';
        
        $stmt = $dbh->prepare($sql);    
        $stmt->execute(array(':id' => $id));
        
        $projects = $stmt->fetchAll(PDO::FETCH_CLASS, self::PROJECT_CLASS);
        
        if(empty($projects)) {
            return false;
        }
        else {
            return $projects[0];
        }
    }

    /**
     * @return a Project object given its name
     */
    public static function getProjectByName($dbh, $name) {
    
        $sql = 'SELECT * FROM ' . self::PROJECT_TABLE
        . ' WHERE name = :name';
    
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(':name' => $name));
    
        $projects = $stmt->fetchAll(PDO::FETCH_CLASS, self::PROJECT_CLASS);
    
        if(empty($projects)) {
            return false;
        }
        else {
            return $projects[0];
        }
    }
    

    /**
     * @return an array of all Projects
     */
    public static function getAllProjects($dbh) {
        $sql = 'SELECT * FROM ' . self::PROJECT_TABLE;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        
        $projects = $stmt->fetchAll(PDO::FETCH_CLASS, self::PROJECT_CLASS);
        return $projects;
    }
    
    
    /**
     * Fast check if a name is registered
     */
    public static function isNameUsed($dbh, $name) {
        if(empty(self::$projectNameList)) {
    
            $sql = 'SELECT name FROM ' . self::PROJECT_TABLE;
    
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            self::$projectNameList = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            //echo "<p><font color=red><b>query db for name list</b></font></p>";
        }
    
        return in_array($name, self::$projectNameList);
    }
    
    
    /**
     * Add a Project: name, description, and owner_id are required
     */
    public static function addProject($dbh, $name, $description, $owner_id,  
                                      $status = self::STATUS_NOT_READY,
                                      $date_start = NULL, 
                                      $date_deadline = NULL,
                                      $date_lud = NULL,
                                      $icon_url = self::DEFAULT_ICON) {
           
        if(self::isNameUsed($dbh, $name)) {
            return false;
        }
        
        $sql = 'INSERT INTO ' . self::PROJECT_TABLE
             . '(name, description, status, date_start, date_deadline, date_lud, icon_url, owner_id) '
             . ' VALUES '
             . '(:name, :description, :status, :date_start, :date_deadline, :date_lud, :icon_url, :owner_id)';
        
        $stmt = $dbh->prepare($sql);

        // $stmt->execute() returns true on success
        return $stmt->execute(array(':name'          => $name, 
                                    ':description'   => $description,
                                    ':status'        => $status,
                                    ':date_start'    => $date_start,
                                    ':date_deadline' => $date_deadline,
                                    ':date_lud'      => $date_lud,
                                    ':icon_url'       => $icon_url,
                                    ':owner_id'      => $owner_id
                                   ));        
    }

    
    /**
     * Delete a Project given its ID
     */
    public static function removeProjectById($dbh, $id) {
        $sql = 'DELETE FROM ' . self::PROJECT_TABLE . ' WHERE id = :id';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':id' => $id)); 
    }
    
    /**
     * Delete a Project given its name
     */
    public static function removeProjectByname($dbh, $name) {
        $sql = 'DELETE FROM ' . self::PROJECT_TABLE . ' WHERE name = :name';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':name' => $name));
    }
    

    /**
     * Delete all Projects
     * TODO: require extra verification
     */
    public static function removeAllProjects($dbh) {
        $sql = 'TRUNCATE TABLE ' . self::PROJECT_TABLE;
        $stmt = $dbh->prepare($sql);
        return $stmt->execute();
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
            case 'D': $propertyName = 'date_deadline';
                      $propertyref = &$this->date_deadline;
                      break;
            case 'i': $propertyName = 'icon_url';
                      $propertyref = &$this->icon_url;
                      break;
            case 'L': $propertyName = 'date_lud';
                      $propertyref = &$this->date_lud;
                      break;   
            case 'n': $propertyName = 'name';
                      $propertyref = &$this->name;
                      break;
            case 'o': $propertyName = 'owner_id';
                      $propertyref = &$this->owner_id;
                      break;                                         
            case 's': $propertyName = 'status';
                      $propertyref = &$this->status;
                      break; 
            case 'S': $propertyName = 'date_start';
                      $propertyref = &$this->date_start;
                      break;
            default: return false;
        }
    
        $sql = 'UPDATE ' . self::PROJECT_TABLE
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
    
    
} // class Project
