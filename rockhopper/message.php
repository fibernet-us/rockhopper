<?php

/**
 * ****************************************************************************
 *
 * Class MESSAGE
 *
 * Store rockhopper Message info and provide related queries and operations
 * 
 * @author   Ting Zhao
 * @version  1.00 
 *                              
 */
 
 class Message {
	private $id;
	private $serial_id;
	private $round_num;
	private $title;
	private $from_id;
	private $to_id;
	private $message;
	private $creation_ts;
	private $read_status;
	private $delete_status;
	
	private $dbh;
	
    const MESSAGE_TABLE = 'RH_MESSAGE';
        
    const READ_STATUS_NOT_READ     = 0;
    const READ_STATUS_READ         = 1;
	
	const DELETE_STATUS_NOT_DELETED       = 0;
    const DELETE_STATUS_FROMID_DELETED    = 1;
    const DELETE_STATUS_TOID_DELETED      = 2;
	
	
    private function __construct() {}
    
    /** magic set */
    public function  __set($name, $value) {
        $this->data[$name] = $value;
    }
    
    public function __toString() {
        return   "id=" . $this->id
		       . "; serial_id=" . $this->serial_id 
               . "; round_num=" . $this->round_num 
               . "; title=" . $this->title 
               . "; from_id=" . $this->from_id 
               . "; to_id=" . $this->to_id
               . "; message=" . $this->message
               . "; creation_ts=" . $this->creation_ts
               . "; read_status=" . $this->getReadStatusString()
               . "; delete_status=" . $this->getDeleteStatusString();
    }
	
	/**************************************************************************
     * getters
     *************************************************************************/
    
    public function getId()				 { return $this->id;               }
	public function getSerial_id()		 { return $this->serial_id;        }
    public function getRound_num()		 { return $this->round_num;        }
    public function getTitle()    		 { return $this->title;            }
    public function getFrom_id()         { return $this->from_id;          }
    public function getTo_id()       	 { return $this->to_id;            }
    public function getMessage()  		 { return $this->message;          }
    public function getCreation_ts()     { return $this->creation_ts;      }
    public function getRead_status()	 { return $this->read_status;	   }
    public function getDelete_status()	 { return $this->delete_status;	   }
	    
    /** return string for read_staus */
    public function getReadStatusString() {
        switch($this->read_status) {
            case self::READ_STATUS_NOT_READ:		return "not read";
            case self::READ_STATUS_READ:		    return "read";
        }
    }
	
    /** return string for read_staus */
    public function getDeleteStatusString() {
        switch($this->delete_status) {
            case self::DELETE_STATUS_NOT_DELETED:		return "not deleted";
            case self::DELETE_STATUS_FROMID_DELETED:	return "deleted by from_id";
            case self::DELETE_STATUS_TOID_DELETED:		return "deleted by to_id";
        }
    }
	
    
    /**************************************************************************
     * setters
     *************************************************************************/
    
    public function setDbHandle($dbh)     { return $this->dbh = $dbh;       }
    public function setTitle($t)          { return $this->update('t', $t);  }
    public function setFrom_id($a)        { return $this->update('f', $f);  }
    public function setTo_id($T)          { return $this->update('T', $T);  }
    public function setMessage($m)        { return $this->update('m', $m);  }
    public function setCreation_ts($c)    { return $this->update('c', $c);  }
    public function setRead_status($r)    { return $this->update('r', $r);  }
    public function setDelete_status($d)  { return $this->update('d', $d);  }
	
 
     /**************************************************************************
     * public static function section
     *************************************************************************/
    
	 /**
     * @return all messages object given their serial_id. $msg[0] is the lastest one.
     */
    public static function getMessageBySerial_id($dbh, $id) {
		$sql = 'SELECT * FROM '.self::MESSAGE_TABLE.' WHERE serial_id = :id ORDER BY round_num DESC';
    
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(':id' => $id));
    
        $msg = $stmt->fetchAll(PDO::FETCH_CLASS, 'Message');
        if(empty($msg)) {
            return false;
        }
		else {
			$msg[0]->setDbHandle($dbh);
			return $msg;
		}
    }
	
    /**
     * @return a Message object array given the user's id.
     */
    public static function getMessageByUserId($dbh, $userid) {
		$sql = 'SELECT * FROM ( 
		        SELECT * FROM '.self::MESSAGE_TABLE.' WHERE ( 
				from_id = '.$userid.' and ( delete_status = "0" or delete_status = "2" ) ) or ( 
				to_id = '.$userid.' and ( delete_status = "0" or delete_status = "1" ) ) ORDER BY round_num DESC 
				) x GROUP BY serial_id ORDER BY creation_ts DESC';
		
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$usermsg = $stmt->fetchALL(PDO::FETCH_CLASS, 'Message');
		
		if(empty ($usermsg)) {
			return false;
		}
		else {
			return $usermsg;
		}
    }
	
    /**
     * @return a maximum number of serial_id.
     */
    public static function getMaxSerial_id($dbh) {
		$sql = 'SELECT MAX(serial_id) FROM '.self::MESSAGE_TABLE;	
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$max = $stmt->fetchColumn();
		return $max;
    }
	
    /**
     * @change the status of a seiral of messages given its serial_id and userid.
     */
	public static function changesDeleteStatus($dbh, $id, $userid) {
		// have to use else otherwise will set to default
		$sql = 'UPDATE '.self::MESSAGE_TABLE.' SET delete_status = (
			    CASE when (from_id = :userid and serial_id = :id) then 1
				     when (to_id = :userid and serial_id= :id) then 2
					 else delete_status
			    END)';
		
		$stmt = $dbh->prepare($sql);

		if($stmt->execute(array(':userid' => $userid, ':id' => $id))) return true;
		else return false;
	}
	
	public static function addMsg($dbh, $serial_id, $round_num = 1, $title, $from_id, $to_id, $message, $creation_ts, $read_status = 0, $delete_status = 0) {
		$sql = 'INSERT INTO '.self::MESSAGE_TABLE.
		       '(serial_id, round_num, title, from_id, to_id, message, creation_ts, read_status, delete_status) '.
			   ' VALUES '.
			   '(:serial_id, :round_num, :title, :from_id, :to_id, :message, :creation_ts, :read_status, :delete_status)';
		$stmt = $dbh->prepare($sql);

        // $stmt->execute() returns true on success
        return $stmt->execute(array(':serial_id'	=> $serial_id, 
                                    ':round_num'	=> $round_num,
									':title'		=> $title,
                                    ':from_id'		=> $from_id,
                                    ':to_id'		=> $to_id,
        		                    ':message'		=> $message,
                                    ':creation_ts'	=> $creation_ts,
                                    ':read_status'	=> $read_status,
                                    ':delete_status'=> $delete_status
                                   )); 
	}
	
    /**
     * @delete a serial of messages given its serial_id.
     */
	public static function deleteMsg($dbh, $id) {
		$sql = 'DELETE FROM ' . self::MESSAGE_TABLE . ' WHERE serial_id = :id';
		
		$stmt = $dbh->prepare($sql);
		if($stmt->execute(array(':id' => $id))) return true;
		else return false;
	}
	
	  
    /**************************************************************************
     * private function section
     *************************************************************************/

    // Update a property given its name initial and new value
    private function update($property, $newValue) {
        // get property name
        switch($property) {
            case 't': $propertyName = 'title';
                      $propertyref = &$this->title;
                      break;
            case 'f': $propertyName = 'from_id';
                      $propertyref = &$this->from_id;
                      break;
            case 'T': $propertyName = 'to_id';
                      $propertyref = &$this->to_id;
                      break;   
            case 'm': $propertyName = 'message';
                      $propertyref = &$this->message;
                      break;
            case 'c': $propertyName = 'creation_ts';
                      $propertyref = &$this->creation_ts;
                      break;                                         
            case 'r': $propertyName = 'read_status';
                      $propertyref = &$this->read_status;
                      break; 
            case 'd': $propertyName = 'delete_status';
                      $propertyref = &$this->delete_status;
                      break;
            default: return false;
        }
    
        $sql = 'UPDATE ' . self::MESSAGE_TABLE
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
 
 } //class Message