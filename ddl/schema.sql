use ticket;

drop table if exists tasks, users, assignment, workflows, status;

create table status (
sid int not null,
sname varchar(64) not null,
primary key (sid)
);

create table workflows (
wfid int not null,
from_status int default null,
to_status int default null,
primary key (wfid),
key from_status (from_status),
key to_status (to_status),
constraint fk_wf_fsid foreign key (from_status) references status (sid),
constraint fk_wf_tsid foreign key (to_status) references status (sid)
);

create table users(
uid int not null,
uname varchar(100) not null,
passwd varchar(64) not null,
email varchar(320) not null,
display_name varchar(100) not null,
created_ts timestamp not null,
primary key (uid)
);

create table tasks(
tid int not null,
ttype char not null,
parent_tid int default null,
reporter int not null,
title varchar(255) not null,
description text default null,
wfid int not null,
status int not null,
created_ts timestamp not null,
primary key (tid),
key parent_tid (parent_tid),
key wfid (wfid),
key status (status),
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