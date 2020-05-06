/* NYU CSGY 6083 Project
 * sql inserts for pupulating some test data
 * Author: yz518
*/

use ticket;

/* Some Sample Status */
insert into status (sname) values 
('OPEN'),
('IN_PROC'),
('REVIEW'),
('QA'),
('CLOSED')
;

/* Sample workflow */
insert into workflows (wfname, created_ts) values 
('project1 wf', now()),
('project2 wf', now()),
('project3 wf', now())
;

insert into wf_state (wfid, from_status, to_status) values 
(1, null, 1),
(1, 1, 2),
(1, 2, 1),
(1, 2, 3),
(1, 3, 2),
(2, 3, 1),
(2, 3, 4),
(2, 4, 3),
(3, 4, 1),
(3, 4, 5),
(3, 5, 1)
;

/* Sample users */
insert into users (uname, passwd, email, display_name, created_ts) values
('test1','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test1@example.com', 'Lucy Lu', now()),
('test2','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test2@example.com', 'Mimi Hu', now()),
('test3','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test3@example.com', 'Lin Pan', now()),
('test4','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test4@example.com', 'Jeff Bezos', now())
;

/*Sample Projects*/
insert into projects (pname, description, wfid, created_ts) values 
('Solidarity GN', 'Meet at Library', 1, now()),
('Social Distancing', 'Stay at home', 2, now())
;

/*Sample Project Leads*/
insert into ownership (pid, uid, created_ts) values 
(1, 2, now()), 
(2, 3, now())
;

/* Sample tasks */
insert into tasks (ttype, pid, reporter, title, description, wfid, status, created_ts ) values 
('I', 1, 1, 'New Roof', 'Fix Library roof', 3, 1, now()),
('I', 2, 4, 'Amazon Kindle screen', 'Make Kindle Great Again', 3, 1, now())
;

insert into task_status_history (tid, status_id, created_ts) values 
(1, 2, now()),
(1, 3, now()),
(1, 4, now()),
(1, 5, now())
;

/* Sample Assign*/
insert into assignment (tid, uid, assigned_ts) values
(1, 1, now()),
(2, 4, now())
;
