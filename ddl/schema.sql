/* NYU CSGY 6083 Project
 * DDL For creating the db schema
 * Author: yz518
*/

use ticket;

drop table if exists tasks, users, assignment, workflows, wf_state, status, task_status_history;

create table status (
  sid int not null auto_increment,
  sname varchar(64) not null,
  
  primary key (sid)
);

create table workflows (
  wfid int not null auto_increment,
  wfname varchar(64), 
  created_ts timestamp not null,
  
  key (wfname),
  primary key (wfid)
);

create table wf_state (
  wfid int not null,
  from_status int default null,
  to_status int default null,
  
  key (wfid, from_status),
  constraint fk_wfs_wfid foreign key (wfid) references workflows (wfid),
  constraint fk_wfs_fsid foreign key (from_status) references status (sid),
  constraint fk_wfs_tsid foreign key (to_status) references status (sid)
);

create table users(
  uid int not null auto_increment,
  uname varchar(100) not null,
  passwd varchar(64) not null,
  email varchar(320) not null,
  display_name varchar(100) not null,
  created_ts timestamp not null,
  
  primary key (uid)
);

create table tasks(
  tid int not null auto_increment,
  ttype char not null,
  parent_tid int default null,
  reporter int not null,
  title varchar(255) not null,
  description text default null,
  wfid int not null,
  status int default null,
  created_ts timestamp not null,
  
  primary key (tid),
  key parent_tid (parent_tid),
  key wfid (wfid),
  key status (status),
  fulltext index title (title),
  fulltext index description(description),
  constraint fk_t_ptid foreign key (parent_tid) references tasks (tid),
  constraint fk_t_wfid foreign key (wfid) references workflows (wfid),
  constraint fk_t_sid foreign key (status) references status (sid)
);

create table assignment (
  tid int not null,
  uid int not null,
  assigned_ts timestamp not null,
  
  key tid (tid),
  key uid (uid),
  constraint fk_a_tid foreign key (tid) references tasks (tid),
  constraint fk_a_uid foreign key (uid) references users (uid)
);

create table task_status_history (
  tid int not null,
  status_id int not null,
  created_ts timestamp not null,
  
  key tid (tid),
  key status_id (status_id),
  constraint fk_tsh_tid foreign key (tid) references tasks (tid),
  constraint fk_tsh_sid foreign key (status_id) references status (sid)
)