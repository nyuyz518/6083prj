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
('my wf', now())
;

insert into wf_state (wfid, from_status, to_status) values 
(1, null, 1),
(1, 1, 2),
(1, 2, 1),
(1, 2, 3),
(1, 3, 2),
(1, 3, 1),
(1, 3, 4),
(1, 4, 3),
(1, 4, 1),
(1, 4, 5),
(1, 5, 1)
;

/* Sample users */
insert into users (uname, passwd, email, display_name, created_ts) values
('test1','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test1@example.com', 'Lucy Lu', now()),
('test2','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test2@example.com', 'Mimi Hu', now()),
('test3','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test3@example.com', 'Lin Pan', now())
;

/* Sample tasks */
insert into tasks (ttype, parent_tid, reporter, title, description, wfid, status, created_ts ) values 
('P', null, 2, 'Solidarity GN', 'Meet at Library', 1, null, now()),
('I', 1, 2, 'New Roof', 'Fix Library roof', 1, 1, now())
;

insert into task_status_history (tid, status_id, created_ts) values 
(2, 1, now())
;

/* Sample Assign*/
insert into assignment (tid, uid, assigned_ts) values
(1, 2, now()),
(1, 3, now()),
(2, 1, now())
;
