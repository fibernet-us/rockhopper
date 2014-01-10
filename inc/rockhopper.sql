
/******************************************************************************
 *
 * Rockhopper DB Schema
 * 
 * @author   Wen Bian
 * @version  1.11
 * @history
 *   09/03/2013: created out of Rockhopper schema in Perl 
 *               added table RH_USER, RH_TASK, RH_SUBTASK, RH_USERLOG,
 *                           RH_ASSIGNMENT
 *
 *   09/18/2013: added tables RH_TEAM, RH_TEAM_MEMBER, RH_PRODUCT, 
 *                            RH_PRODUCT_TEAM, RH_BACKLOG, RH_BACKLOG_TASK, 
 *                            RH_MESSAGE
 *
 *
 *
 * @notes
 * in some cases foreign keys are not 'cascade on delete', so as to preserve 
 * certain useful information, and in those cases the foreign key is usually 
 * set to null, so check for null when appropriate.
 *
 */


/******************************************************************************
 *
 * table RH_ASSIGNMENT
 * 
 * stores task worker info: task id - user id (dev) - user id (tester)
 *
 */
DROP TABLE IF EXISTS RH_ASSIGNMENT;
CREATE TABLE RH_ASSIGNMENT (

  task_id    int        NOT NULL,
  dev_id     smallint   NOT NULL,  
  tester_id  smallint   NOT NULL, 

  PRIMARY KEY (task_id, dev_id),

  CONSTRAINT fk_RH_ASSIGNMENT_task_id_RH_TASK_id 
             FOREIGN KEY (task_id) REFERENCES RH_TASK (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_ASSIGNMENT_dev_id_RH_USER_id 
             FOREIGN KEY (dev_id) REFERENCES RH_USER (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_ASSIGNMENT_tester_id_RH_USER_id 
             FOREIGN KEY (tester_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *

 * table RH_USER
 *
 * stores rockhopper related user info.
 * team list is stored in RH_TEAM_member.
 *
 * == type ==
 *   0 UNKNOWN
 *   1 RH_ADMIN
 *   2 PRODUCT_OWNER
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
  passhash          varchar(255)  NOT NULL, 
  salt              char(21)      NOT NULL,
  auth              varchar(255)  NOT NULL, 
  email             varchar(255)  NOT NULL,
  type              tinyint       NOT NULL  DEFAULT 0,  
  status            tinyint       NOT NULL  DEFAULT 0,  
  timezone          tinyint       NOT NULL  DEFAULT 0, 
  location          varchar(255)            DEFAULT NULL,
  icon_url           varchar(255)            DEFAULT NULL,

  enabled           tinyint(1)    NOT NULL  DEFAULT 1,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_USER_username (username),
  UNIQUE KEY uk_RH_USER_email (email)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_TEAM
 *
 * stores basic team info. 
 * member list is stored in RH_TEAM_MEMBER
 *
 * == status ==
 *   0 UNKNOWN
 *   1 WORKING
 *   2 IDLE
 *   3 DISMISSED
 *
 */
DROP TABLE IF EXISTS RH_TEAM;
CREATE TABLE RH_TEAM (

  id           smallint      NOT NULL  AUTO_INCREMENT,
  name         varchar(255)  NOT NULL,
  description  text(1024)    NOT NULL,
  status       tinyint       NOT NULL  DEFAULT 0,  
  icon_url     varchar(255)            DEFAULT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_TEAM_name (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_TEAM_MEMBER
 *
 * stores team member info: team id - user id
 *
 */
DROP TABLE IF EXISTS RH_TEAM_MEMBER;
CREATE TABLE RH_TEAM_MEMBER (

  team_id    smallint  NOT NULL,
  user_id    smallint  NOT NULL,

  PRIMARY KEY (team_id, user_id),

  CONSTRAINT fk_RH_TEAM_MEMBER_team_id_RH_TEAM_id 
             FOREIGN KEY (team_id) REFERENCES RH_TEAM (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_TEAM_MEMBER_user_id_RH_USER_id 
             FOREIGN KEY (user_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_TASK
 *
 * stores PRODUCT story, task, and subtask.
 * task dependency is stored in RH_SUBTASK.
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

  creator_id       smallint,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_TASK_priority (priority),

  CONSTRAINT fk_RH_TASK_creator_id_RH_USER_id
             FOREIGN KEY (creator_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE SET NULL   /* delete a task's owner should not delete the task itself */

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_SUBTASK
 * 
 * stores task dependency: task id - subtask id
 *
 */
DROP TABLE IF EXISTS RH_SUBTASK;
CREATE TABLE RH_SUBTASK (

  task_id      int  NOT NULL,
  subtask_id   int  NOT NULL,

  PRIMARY KEY (task_id, subtask_id),

  CONSTRAINT fk_RH_SUBTASK_task_id_RH_TASK_id 
             FOREIGN KEY (task_id) REFERENCES RH_TASK (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_SUBTASK_subtask_id_RH_TASK_id 
             FOREIGN KEY (subtask_id) REFERENCES RH_TASK (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE

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

  id           int       NOT NULL AUTO_INCREMENT,
  creation_ts  datetime  NOT NULL,
  what         text      NOT NULL,
  url          tinytext             DEFAULT NULL,

  user_id      smallint  NOT NULL,   
  task_id      int                  DEFAULT NULL,

  PRIMARY KEY (id),

  CONSTRAINT fk_RH_USERLOG_user_id_RH_USER_id
             FOREIGN KEY(user_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_USERLOG_task_id_RH_TASK_id
             FOREIGN KEY(task_id) REFERENCES RH_TASK (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_PRODUCT
 *
 * stores basic PRODUCT info. 
 * backlog info is in RH_BACKLOG.
 * team list is stored in RH_PRODUCT_RH_TEAM
 *
 * == status == 
 *   0: NOT_READY
 *   1: READY
 *   2: IN_PROGRESS
 *   3. PAUSED
 *   4. TESTING
 *   5. COMPLETED
 *   6. ABORTED
 */
DROP TABLE IF EXISTS RH_PRODUCT;
CREATE TABLE RH_PRODUCT (

  id             smallint     NOT NULL AUTO_INCREMENT,
  name           varchar(255) NOT NULL,
  description    text(65535)  NOT NULL,
  status         tinyint      NOT NULL DEFAULT 0,
  date_start     date                  DEFAULT NULL,
  date_deadline  date                  DEFAULT NULL,
  date_end       date                  DEFAULT NULL,
  date_lud       date                  DEFAULT NULL, /* last update date */
  icon_url       varchar(255)          DEFAULT NULL,

  owner_id       smallint,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_PRODUCT_name (name),

  CONSTRAINT fk_RH_PRODUCT_owner_id_RH_USER_id 
             FOREIGN KEY (owner_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE SET NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_PRODUCT_TEAM
 *
 * stores product team info: product id - team id
 *
 */
DROP TABLE IF EXISTS RH_PRODUCT_TEAM;
CREATE TABLE RH_PRODUCT_TEAM (

  product_id  smallint NOT NULL,
  team_id  smallint  NOT NULL,

  PRIMARY KEY (product_id, team_id),

  CONSTRAINT fk_RH_PRODUCT_TEAM_product_id_RH_PRODUCT_id 
             FOREIGN KEY (product_id) REFERENCES RH_PRODUCT (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_PRODUCT_TEAM_team_id_RH_TEAM_id 
             FOREIGN KEY (team_id) REFERENCES RH_TEAM (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_BACKLOG
 *
 * stores a product backlog (a product can have multiple backlogs)
 *
 */
DROP TABLE IF EXISTS RH_BACKLOG;
CREATE TABLE RH_BACKLOG (

  id             smallint     NOT NULL AUTO_INCREMENT,
  name           varchar(255) NOT NULL,
  description    text(65535)  NOT NULL,
  status         tinyint      NOT NULL DEFAULT 0,
  date_start     date                  DEFAULT NULL,
  date_deadline  date                  DEFAULT NULL,
  date_end       date                  DEFAULT NULL,
  date_lud       date                  DEFAULT NULL, 
  icon_url       varchar(255)          DEFAULT NULL,

  product_id  smallint NOT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uk_RH_BACKLOG_name (name),

  CONSTRAINT fk_RH_BACKLOG_product_id_RH_PRODUCT_id 
             FOREIGN KEY (product_id) REFERENCES RH_PRODUCT (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_BACKLOG_TASK
 *
 * stores backlog tasks: backlog id - task id 
 *
 */
DROP TABLE IF EXISTS RH_BACKLOG_TASK;
CREATE TABLE RH_BACKLOG_TASK (

  backlog_id  smallint NOT NULL,
  task_id     int      NOT NULL,

  PRIMARY KEY (backlog_id, task_id),

  CONSTRAINT fk_RH_BACKLOG_TASK_backlog_id_RH_BACKLOG_id 
             FOREIGN KEY (backlog_id) REFERENCES RH_BACKLOG (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_BACKLOG_TASK_task_id_RH_TASK_id 
             FOREIGN KEY (task_id) REFERENCES RH_TASK (id) 
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/******************************************************************************
 *
 * table RH_MESSAGE
 * 
 * stores user message and request.
 *
 * == read_status ==
 *   0 NOT_READ
 *   1 IS_READ
 *
 * == delete_status ==
 *   0 NOT_DELETED
 *   1 FROMID_DELETED
 *   2 TOID_DELETED
 *
 *
 */
DROP TABLE IF EXISTS RH_MESSAGE;
CREATE TABLE RH_MESSAGE (

  id           smallint      NOT NULL AUTO_INCREMENT,
  serial_id    int           NOT NULL,
  round_num    int           NOT NULL  DEFAULT 1,
  title        text(1024)    NOT NULL,
  from_id      smallint      NOT NULL,
  to_id        smallint      NOT NULL,
  message      text(65535)   NOT NULL,
  creation_ts  datetime      NOT NULL,
  read_status  tinyint       NOT NULL  DEFAULT 0,
  delete_status tinyint      NOT NULL  DEFAULT 0,

  CONSTRAINT fk_RH_MESSAGE_from_id_RH_USER_id 
             FOREIGN KEY (from_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE,

  CONSTRAINT fk_RH_MESSAGE_to_id_RH_USER_id 
             FOREIGN KEY (to_id) REFERENCES RH_USER (id)
             ON UPDATE CASCADE
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

