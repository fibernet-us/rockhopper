
/******************************************************************************
 *
 * Rockhopper DB Schema
 * 
 * @author   Wen Bian
 * @version  1.11
 * @history
 *   09/03/2013: created out of Rockhopper schema in Perl
 *               added table RH_USER, RH_LOG, RH_TASK
 *   09/12/2013: updated table RH_USER, removed last_activity_id
 *
 *
 *
 *
 * @notes
 * in some cases foreign keys are not 'cascade on delete', so as to preserve 
 * certain useful information, and in those cases the foreign key is usually 
 * set to 0 to indicate its invalidness, so check for 0 when appropriate.
 * (all valid ids start at 1)
 *
 */


/******************************************************************************
 *
 * table RH_USER
 *
 * stores rockhopper related user info.
 * team list is stored in rhteam_member.
 *
 * == type ==
 *   0 UNKNOWN
 *   1 RH_ADMIN
 *   2 PROJECT_OWNER
 *   3 DEV
 *   4 SCRUM_MASTER
 *   5 CHICKEN
 *
 * == status ==
 *   0 UNKNOWN
 *   1 WORKING
 *   2 IDLE
 *   3 ON_LEAVE
 *   4 LEFT
 *
 */
DROP TABLE IF EXISTS RH_USER;
CREATE TABLE RH_USER (

  id                smallint      NOT NULL  AUTO_INCREMENT,
  username          varchar(255)  NOT NULL,  
  fullname          varchar(255)  NOT NULL,
  passwd            varchar(255)  NOT NULL, 
  email             varchar(255)  NOT NULL,
  type              tinyint       NOT NULL  DEFAULT 0,  
  status            tinyint       NOT NULL  DEFAULT 0,  
  timezone          tinyint       NOT NULL  DEFAULT 0, 
  location          varchar(255)            DEFAULT NULL,
  iconurl           varchar(255)            DEFAULT NULL,
  enabled           tinyint(1)    NOT NULL  DEFAULT 1,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_USER_username (username),
  UNIQUE KEY uk_RH_USER_email (email)
  
  /* CONSTRAINT fk_RH_USER_last_activity_id_RH_USERLOG_id FOREIGN KEY (last_activity_id) REFERENCES RH_USERLOG (id) */

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_USERLOG
 *
 * logs user activities
 *
 */
DROP TABLE IF EXISTS RH_USERLOG;
CREATE TABLE RH_USERLOG (

  id int NOT NULL AUTO_INCREMENT,
  time datetime NOT NULL,
  what text NOT NULL,
  url tinytext DEFAULT NULL,

  user_id smallint NOT NULL,   
  task_id int NOT NULL,       

  PRIMARY KEY (id)

  /* CONSTRAINT fk_RH_USERLOG_user_id_RH_USER_id FOREIGN KEY (user_id) REFERENCES RH_USER (id), */
  /* CONSTRAINT fk_RH_USERLOG_task_id_RH_TASK_id FOREIGN KEY (task_id) REFERENCES RH_TASK (id)  */

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_TASK
 *
 * stores project story, task, and subtask.
 * task dependency is stored in rhsubtask.
 * backlog task info is stored in rhbacklog.
 *
 * == type ==
 *  0 FEATURE
 *  1 BUGFIX
 *  
 * == status ==
 *  0 NEW
 *  1 REOPENED
 *  2 ASSIGNED
 *  3 FINISHED
 *  4 VERIFIED
 *  5 DONE
 *
 * == importance ==
 *  0 BLOCKER
 *  1 CRITICAL
 *  2 MAJOR
 *  3 NORMAL
 *  4 MINOR
 *  5 TRIVIAL
 *
 */
DROP TABLE IF EXISTS RH_TASK;
CREATE TABLE RH_TASK (
  id               int       NOT NULL  AUTO_INCREMENT,
  name             tinytext  NOT NULL,
  description      text      NOT NULL,

  type             tinyint   NOT NULL,
  priority         tinyint   NOT NULL,
  importance       tinyint   NOT NULL,
  status           tinyint   NOT NULL,

  creation_ts      datetime  NOT NULL,
  lastupdated_ts   datetime  NOT NULL,
  deadline_ts      datetime            DEFAULT NULL,

  estimated_time   smallint  NOT NULL  DEFAULT 0,  /* in hours */
  adjusted_time    smallint  NOT NULL  DEFAULT 0,  
  remaining_time   smallint  NOT NULL  DEFAULT 0, 

  parent_id        int                 DEFAULT NULL,
  creator_id       smallint  NOT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_TASK_priority (priority)

  /* CONSTRAINT fk_RH_TASK_parent_id_RH_TASK_id FOREIGN KEY (parent_id) REFERENCES RH_TASK (id) ON DELETE CASCADE ON UPDATE CASCADE,   */
  /* CONSTRAINT fk_RH_TASK_creator_id_RH_USER_id FOREIGN KEY (creator_id) REFERENCES RH_USER (id) ON DELETE CASCADE ON UPDATE CASCADE, */

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * Create relationships between RH_USER, RH_USERLOG, RH_TASK
 *
 */

ALTER TABLE RH_USERLOG 
    ADD CONSTRAINT fk_RH_USERLOG_user_id_RH_USER_id
    FOREIGN KEY(user_id)
    REFERENCES RH_USER (id);

ALTER TABLE RH_USERLOG 
    ADD CONSTRAINT fk_RH_USERLOG_task_id_RH_TASK_id
    FOREIGN KEY(task_id)
    REFERENCES RH_TASK (id);

ALTER TABLE RH_TASK 
    ADD CONSTRAINT fk_RH_TASK_parent_id_RH_TASK_id
    FOREIGN KEY (parent_id)
    REFERENCES RH_TASK (id)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE RH_TASK 
    ADD CONSTRAINT fk_RH_TASK_creator_id_RH_USER_id
    FOREIGN KEY (creator_id)
    REFERENCES RH_USER (id)
    ON DELETE CASCADE ON UPDATE CASCADE;

