/* NYU CSGY 6083 Project
 * sql for part 1 questions
 * Author: yz518
*/

/* (1) Create a new user account, together with email, password, username, and display name.  */
insert into users (uname, passwd, email, display_name, created_ts) values
('tester','340c19a145a5232ba0d24a2c91c0ee5bf5a06e2556c83e5072697338891b2e1a', 'test4@example.com', 'Jeff Shi', now());


/* (2) Create an issue for a project with title and description, and initialize the status of this issue. */
insert into tasks (ttype, parent_tid, reporter, title, description, wfid, status, created_ts ) values 
('I', 1, 2, 'New Wall', 'Fix Library roof', 1, 1, now());

set @new_tid = (select LAST_INSERT_ID());  
insert into task_status_history (tid, status_id, created_ts) values 
(@new_tid, 1, now())
;
select * from tasks;

/* (3) For a current user and a certain issue, first check if this user is authorized to assign it to other
users (i.e., is a lead)-- query result > 1 means there exists such user; then write a query to add an assignee.  */

select count(a.uid)
from tasks p inner join tasks i on p.tid = i.parent_tid
inner join assignment a on a.tid = p.tid
where i.tid = 3 /*issue id*/
and a.uid = 2; /* user id */

insert into assignment (tid, uid, assigned_ts) values
(2, 4, now());
select * from assignment;


/* (4) List all possible next statuses of a certain issue, based on its current status */
select * from tasks where parent_tid = 1; /*find out there're two issues currently*/

select s.sid, s.sname 
from tasks i inner join wf_state ws on i.wfid = ws.wfid 
and i.status = ws.from_status inner join status s on ws.to_status = s.sid
where i.tid = 4 /* issue id*/ 
;

/* (5) Show the status change history of a certain issue, sorted by change timestamps in descending
order.  */
select * 
from task_status_history
where tid = 3 /* issue id */
order by created_ts desc;

/* (6) List any issues for the project with name “Amazon Kindle” where the issue title contains the
term “screen”, user “Jeff Bezos” is one of the assignees, and the status of the issue is
“OPEN”.  */

select * 
from tasks i inner join tasks p on i.parent_tid = p.tid
inner join assignment a on a.tid = i.tid
inner join users u on a.uid = u.uid
inner join status s on i.status = s.sid
where p.title = 'Amazon Kindle' 
and MATCH(i.title) against ('screen' IN NATURAL LANGUAGE MODE) and u.uname = 'Jeff Bezos'  
;

/* Task Rest */

select * from users where uid = 1;

select pid, pname, description, wfid, created_ts from projects;

select * from ownership;
