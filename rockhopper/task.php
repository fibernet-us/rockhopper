<?php

/**
 * ****************************************************************************
 *
 * Class TASK UNFINISHED
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
	
    /** get a list of tasks this user can access */
    public function getTasks($username) {
        if($this->isTaskOwner()) {
            $sql = 'SELECT * FROM ' . self::TASK_TABLE . ' WHERE creator_id = :$username';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            
            $tasks = $stmt->fetchAll(PDO::FETCH_CLASS, 'Task');
            return $tasks;
        }
        else {
            return array($this);
        }
    }
	
}