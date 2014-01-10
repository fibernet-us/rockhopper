<?php

/**
 * ****************************************************************************
 *
 * Class Task 
 * 
 * Store rockhopper Task info and provide related queries and operations
 * 
 * @author   Ting Zhao
 * @version  1.00
 *
 */
 
 
class Task {
	private $id;
	private $name;
	private $description;
	private $type;
	private $priority;
	private $importance;
	private $status;
	private $creation_ts;
	private $lastupdated_ts;
	private $deadline_ts;
	private $estimated_time;
	private $adjusted_time;
	private $remaining_time;
	private $creator_id;
	
    const TASK_TABLE = 'RH_TASK';
    
    const TYPE_FEATURE        = 0;
    const TYPE_BUGFIX         = 1;
        
    const STATUS_NEW          = 0;
    const STATUS_REOPENED     = 1;
    const STATUS_ASSIGNED     = 2;
    const STATUS_FINISHED     = 3;
    const STATUS_VERIFIED     = 4;
    const STATUS_DONE         = 5;
	
    const IMPORTANCE_BLOCKER  = 0;
    const IMPORTANCE_CRITICAL = 1;
    const IMPORTANCE_MAJOR    = 2;
    const IMPORTANCE_NORMAL   = 3;
    const IMPORTANCE_MINOR    = 4;
    const IMPORTANCE_TRIVIAL  = 5;
	
	
    private function __construct() {}
    
    /** magic set */
    public function  __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __toString() {
        return   "id=" . $this->id 
               . "; name=" . $this->name 
               . "; description=" . $this->description 
               . "; type=" . $this->getTypeString() 
               . "; priority=" . $this->priority
               . "; importance=" . $this->getImportanceString()
               . "; status=" . $this->getStatusString()
               . "; creation time=" . $this->creation_ts
               . "; last updated time=" . $this->lastupdated_ts
               . "; dead line=" . $this->deadline_ts
               . "; estimated time=" . $this->estimated_time
               . "; adjusted time=" . $this->adjusted_time
               . "; remaining time" . $this->remaining_time
               . "; creator" . $this->creator_id;
    }
	
	/**************************************************************************
     * getters
     *************************************************************************/
    
    public function getId()				 { return $this->id;               }
    public function getName()            { return $this->name;             }
    public function getDescription()     { return $this->description;      }
    public function getType()            { return $this->type;             }
    public function getPriority()        { return $this->priority;         }
    public function getImportance()      { return $this->importance;       }
    public function getStatus()          { return $this->status;           }
    public function getCreation_ts()     { return $this->creation_ts;      }
    public function getLastupdated_ts()  { return $this->lastupdated_ts;   }
    public function getDeadline_ts()     { return $this->Deadline_ts;      }
    public function getEstimated_time()  { return $this->estimated_time;   }
    public function getAdjusted_time()   { return $this->adjusted_time;    }
    public function getRemaining_time()  { return $this->remaining_time;   }
    public function getCreator_id()      { return $this->creator_id;       }
	
    /** return string for type */
    public function getTypeString() {
        switch($this->type) {
            case self::TYPE_FEATURE:		return "feature";
            case self::TYPE_BUGFIX:			return "bugfix";
        }
    }
	    
    /** return string for staus */
    public function getStatusString() {
        switch($this->status) {
            case self::STATUS_NEW:			return "new";
            case self::STATUS_REOPENED:		return "reopened";
            case self::STATUS_ASSIGNED:		return "assigned";
            case self::STATUS_FINISHED:		return "finished";
            case self::STATUS_VERIFIED:		return "verified";
			case self::STATUS_DONE:			return "done";
        }
    }
	    
    /** return string for importance */
    public function getImportanceString() {
        switch($this->importance) {
            case self::IMPORTANCE_BLOCKER:	return "blocker";
            case self::IMPORTANCE_CRITICAL:	return "critical";
            case self::IMPORTANCE_MAJOR:	return "major";
            case self::IMPORTANCE_NORMAL:	return "normal";
            case self::IMPORTANCE_MINOR:	return "minor";
			case self::IMPORTANCE_TRIVIAL:	return "trivial";
        }
    }
	

    /**************************************************************************
     * setters
     *************************************************************************/
    public function setDbHandle($dbh)     { return $this->dbh = $dbh;       }
    public function setName($n)         	{ return $this->update('n', $n);  }
    public function setDescription($d)  	{ return $this->update('d', $d);  }
    public function setType($t)				{ return $this->update('t', $t);  }
    public function setPriority($p)			{ return $this->update('p', $p);  }
    public function setImportance($i)		{ return $this->update('i', $i);  }
    public function setStatus($s)       	{ return $this->update('s', $s);  }
    public function setCreation_ts($c)  	{ return $this->update('c', $c);  }
    public function setLastupdated_ts($l)	{ return $this->update('l', $l);  }
    public function setDeadline_ts($D)    	{ return $this->update('D', $D);  }
    public function setEstimated_time($e)   { return $this->update('e', $e);  }
    public function setAdjusted_time($a)    { return $this->update('a', $a);  }
    public function setRemaining_time($r)   { return $this->update('r', $r);  }
    public function setCreator_id($c)      	{ return $this->update('C', $C);  }
	
	
		
    /**************************************************************************
     * public static function section
     *************************************************************************/
    
    /**
     * @return a Task object given its ID
     */
    public static function getTaskById($dbh, $id) {
        
        $sql = 'SELECT * FROM ' . self::TASK_TABLE 
             . ' WHERE id = :id';
        
        $stmt = $dbh->prepare($sql);    
        $stmt->execute(array(':id' => $id));
        
        $projects = $stmt->fetchAll(PDO::FETCH_CLASS,  'Task');
        
        if(empty($projects)) {
            return false;
        }
        else {
            return $projects[0];
        }
    }
	
	 /**
     * @return a Task object given its name
     */
    public static function getTaskByName($dbh, $name) {
    
        $sql = 'SELECT * FROM ' . self::TASK_TABLE
        . ' WHERE name = :name';
    
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(':name' => $name));
    
        $projects = $stmt->fetchAll(PDO::FETCH_CLASS, 'Task');
    
        if(empty($projects)) {
            return false;
        }
        else {
            return $projects[0];
        }
    }
	
	 /**
     * @return an array of all Tasks
     */
    public static function getAllTasks($dbh) {
        $sql = 'SELECT * FROM ' . self::TASK_TABLE;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        
        $projects = $stmt->fetchAll(PDO::FETCH_CLASS, 'Task');
        return $projects;
    }
	
	    
    /**
     * Fast check if a name is registered
     */
    public static function isNameUsed($dbh, $name) {
        if(empty(self::$taskNameList)) {
    
            $sql = 'SELECT name FROM ' . self::TASK_TABLE;
    
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            self::$taskNameList = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            //echo "<p><font color=red><b>query db for name list</b></font></p>";
        }
    
        return in_array($name, self::$taskNameList);
    }


    /**
     * Add a Task: name, description, and creator_id are required
     */
    public static function addTask($dbh, $name, $description, $creator_id,
									  $type = self::TYPE_FEATURE, 
									  $priority = 0,
                                      $importance = self::IMPORTANCE_NORMAL,
									  $status = self::STATUS_NEW,
                                      $creation_ts = NULL, 
									  $lastupdated_ts = NULL,
									  $deadline_ts = NULL,
									  $estimated_time = NULL,
                                      $adjusted_time = NULL,
                                      $remaining_time = NULL) {
           
        if(self::isNameUsed($dbh, $name)) {
            return false;
        }
        
        $sql = 'INSERT INTO ' . self::TASK_TABLE
             . '(name, description, type, priority, importance, status, creation_ts, lastupdated_ts, deadline_ts, estimated_time, adjsted_time, remaining_time, creator_id) '
             . ' VALUES '
             . '(:name, :description, :type, :priority, :importance, :status, :creation_ts, :lastupdated_ts, :deadline_ts, :estimated_time, :adjusted_time, :reamining_time, :creator_id)';
        
        $stmt = $dbh->prepare($sql);

        // $stmt->execute() returns true on success
        return $stmt->execute(array(':name'				=> $name, 
                                    ':description'		=> $description,
                                    ':type'				=> $type,
                                    ':priority'			=> $priority,
                                    ':importance'		=> $importance,
                                    ':status'			=> $status,
                                    ':creation_ts'		=> $creation_ts,
                                    ':lastupdated_ts'	=> $lastupdated_ts,
                                    ':deadline_ts'		=> $deadline_ts,
                                    ':estimated_time'	=> $estimated_time,
                                    ':adjusted_time'	=> $adjusted_time,
                                    ':remaining_time'	=> $remaining_time,
                                    ':creator_id'		=> $creator_id
                                   ));        
    }

    
    /**
     * Delete a Task given its ID
     */
    public static function removeTaskById($dbh, $id) {
        $sql = 'DELETE FROM ' . self::TASK_TABLE . ' WHERE id = :id';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':id' => $id)); 
    }
    
    /**
     * Delete a Task given its name
     */
    public static function removeTaskByname($dbh, $name) {
        $sql = 'DELETE FROM ' . self::TASK_TABLE . ' WHERE name = :name';
        $stmt = $dbh->prepare($sql);
        return $stmt->execute(array(':name' => $name));
    }
    

    /**
     * Delete all Tasks
     * TODO: require extra verification
     */
    public static function removeAllTasks($dbh) {
        $sql = 'TRUNCATE TABLE ' . self::TASK_TABLE;
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
            case 'n': $propertyName = 'name';
                      $propertyref = &$this->name;
                      break;
            case 'd': $propertyName = 'description';
                      $propertyref = &$this->description;
                      break;
            case 't': $propertyName = 'type';
                      $propertyref = &$this->type;
                      break;
            case 'p': $propertyName = 'priority';
                      $propertyref = &$this->priority;
                      break;
            case 'i': $propertyName = 'importance';
                      $propertyref = &$this->importance;
                      break;   
            case 's': $propertyName = 'status';
                      $propertyref = &$this->status;
                      break;
            case 'c': $propertyName = 'creation_ts';
                      $propertyref = &$this->creation_ts;
                      break;                                         
            case 'l': $propertyName = 'lastupdated_ts';
                      $propertyref = &$this->status;
                      break; 
            case 'D': $propertyName = 'Deadline_ts';
                      $propertyref = &$this->date_start;
                      break;
            case 'e': $propertyName = 'estimated_time';
                      $propertyref = &$this->estimated_time;
                      break;
            case 'a': $propertyName = 'adjusted_time';
                      $propertyref = &$this->adjusted_time;
                      break;
            case 'r': $propertyName = 'remaining_time';
                      $propertyref = &$this->remaining_time;
                      break;
            case 'C': $propertyName = 'creator_id';
                      $propertyref = &$this->creator_id;
                      break;   
            default: return false;
        }
    
        $sql = 'UPDATE ' . self::TASK_TABLE
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
} // class Task