DROP PROCEDURE IF EXISTS lam_sys.get_userpermission_byuid;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_userpermission_byuid(uid INT)
begin
	select a.layout,a.route,b.* from 
	(select * from lam_sys.`_user_access` where status=1 and `user` = uid) a
	left join  
	(select * from 
		(select id as module_id,name as module_name,allow_all as module_allow from lam_sys.`_module` where status = 1) a
		left join (select id as control_id,name as control_name,allow_all as control_allow,module from lam_sys.`_controller` where status = 1) b 
		on b.module=a.module_id
		left join (select id as act_id,name as act_name,allow_all as act_allow,controller as control from lam_sys.`_action` where status = 1) c 
		on c.control=b.control_id ) b
	on 
	(b.act_id = a.`action` and b.control_id = a.`controller` and b.module_id = a.`module`) OR
	(a.`action` = 0 and b.control_id = a.`controller` and b.module_id = a.`module`) OR
	(a.`action` = 0 and a.`controller` = 0 and b.module_id = a.`module`)
	union
	select a.layout,a.route,b.* from 
	(select * from lam_sys.`_role_access` where status=1 and 
	`role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join  
	(select * from 
		(select id as module_id,name as module_name,allow_all as module_allow from lam_sys.`_module` where status = 1) a
		left join (select id as control_id,name as control_name,allow_all as control_allow,module from lam_sys.`_controller` where status = 1) b 
		on b.module=a.module_id
		left join (select id as act_id,name as act_name,allow_all as act_allow,controller as control from lam_sys.`_action` where status = 1) c 
		on c.control=b.control_id ) b
	on 
	(b.act_id = a.`action` and b.control_id = a.`controller` and b.module_id = a.`module`) OR
	(a.`action` = 0 and b.control_id = a.`controller` and b.module_id = a.`module`) OR
	(a.`action` = 0 and a.`controller` = 0 and b.module_id = a.`module`)
	order by layout,route,module_id,control_id,act_id;
end$$
DELIMITER ;
-- call lam_sys.get_userpermission_byuid(1);

DROP PROCEDURE IF EXISTS lam_sys.get_usermenu_byuid_layout;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_usermenu_byuid_layout(uid INT,theme VARCHAR(100))
begin
	select b.* from 
	(select * from lam_sys.`_user_menu` where status=1 and `user` = uid) a
	left join 
	(select * from lam_sys.`_menu` where status = 1 and (route is not null or url is not null) and (layout is null or layout=theme)) b
	on b.id=a.menu
	union
	select b.* from 
	(select * from lam_sys.`_role_menu` where status=1 and `role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join 
	(select * from lam_sys.`_menu` where status = 1 and (route is not null or url is not null) and (layout is null or layout=theme)) b
	on b.id=a.menu
	where id is not null
	order by module,parent,priority,title;
end$$
DELIMITER ;
-- call lam_sys.get_usermenu_byuid_layout(1,'notika');

DROP PROCEDURE IF EXISTS lam_sys.get_usermenu_byuid_layout_module;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_usermenu_byuid_layout_module(uid INT,theme VARCHAR(100),module VARCHAR(100))
begin
	select b.* from 
	(select * from lam_sys.`_user_menu` where status=1 and `user` = uid) a
	left join 
	(select * from lam_sys.`_menu` where status = 1 and (route is not null or url is not null) 
		and (layout is null or layout='' or layout=theme) and (module is null or module='' or module=module)) b
	on b.id=a.menu
	union
	select b.* from 
	(select * from lam_sys.`_role_menu` where status=1 and `role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join 
	(select * from lam_sys.`_menu` where status = 1 and ((route is not null and route !='') or (url is not null and url != '')) 
		and (layout is null or layout='' or layout=theme) and (module is null or module='' or module=module)) b
	on b.id=a.menu
	where id is not null
	order by module,parent,priority,title;
end$$
DELIMITER ;
-- call lam_sys.get_usermenu_byuid_layout_module(1,'notika');

DROP PROCEDURE IF EXISTS lam_sys.get_userrole_byuid;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_userrole_byuid(uid INT)
begin
	select b.* from 
	(
	select * from lam_sys.`_user_role` where status=1 and `user` = uid
	union 
	select id as `user`,main_role as `role`,`status` from `_user` where status =1 and id= uid
	) a
	left join 
	(select * from lam_sys.`_role` where status = 1) b
	on b.code=a.role;
end$$
DELIMITER ;
-- call lam_sys.get_userrole_byuid(1);

DROP PROCEDURE IF EXISTS lam_sys.get_userubis_byuid;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_userubis_byuid(uid INT)
begin
	select b.* from 
	(
	select * from lam_sys.`_user_ubis` where status=1 and `user` = uid
	union 
	select id as `user`,main_ubis as `ubis`,`status` from `_user` where status =1 and id= uid
	) a
	left join 
	(select * from lam_sys.`_ubis` where status = 1) b
	on b.code=a.ubis;
end$$
DELIMITER ;
-- call lam_sys.get_userrole_byuid(1);

DROP PROCEDURE IF EXISTS lam_sys.get_userinbox_byuid_layout_module;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_userinbox_byuid_layout_module(uid INT,theme VARCHAR(100),module VARCHAR(100))
begin
	select b.* from 
	(select * from lam_sys.`_user_inbox` where status!=0 and `user` = uid) a
	left join 
	(select * from lam_sys.`_inbox` where status != 0 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.inbox
	union
	select b.* from 
	(select * from lam_sys.`_role_inbox` where status!=1 and `role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join 
	(select * from lam_sys.`_inbox` where status != 1 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.inbox;
end$$
DELIMITER ;
-- call lam_sys.get_userinbox_byuid_layout_module(1,'notika');

DROP PROCEDURE IF EXISTS lam_sys.get_userunreadinbox_byuid_layout_module;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_userunreadinbox_byuid_layout_module(uid INT,theme VARCHAR(100),module VARCHAR(100))
begin
	select b.* from 
	(select * from lam_sys.`_user_inbox` where status=1 and `user` = uid) a
	left join 
	(select * from lam_sys.`_inbox` where status = 1 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.inbox
	union
	select b.* from 
	(select * from lam_sys.`_role_inbox` where status=1 and `role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join 
	(select * from lam_sys.`_inbox` where status = 1 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.inbox;
end$$
DELIMITER ;
-- call lam_sys.get_userunreadinbox_byuid_layout_module(1,'notika');

DROP PROCEDURE IF EXISTS lam_sys.get_useralert_byuid_layout_module;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_useralert_byuid_layout_module(uid INT,theme VARCHAR(100),module VARCHAR(100))
begin
	select b.* from 
	(select * from lam_sys.`_user_alert` where status!=0 and `user` = uid) a
	left join 
	(select * from lam_sys.`_alert` where status != 0 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.alert
	union
	select b.* from 
	(select * from lam_sys.`_role_alert` where status!=1 and `role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join 
	(select * from lam_sys.`_alert` where status != 1 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.alert;
end$$
DELIMITER ;
-- call lam_sys.get_useralert_byuid_layout_module(1,'notika');

DROP PROCEDURE IF EXISTS lam_sys.get_userunreadalert_byuid_layout_module;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_userunreadalert_byuid_layout_module(uid INT,theme VARCHAR(100),module VARCHAR(100))
begin
	select b.* from 
	(select * from lam_sys.`_user_alert` where status=1 and `user` = uid) a
	left join 
	(select * from lam_sys.`_alert` where status = 1 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.alert
	union
	select b.* from 
	(select * from lam_sys.`_role_alert` where status=1 and `role` in (select `role` from lam_sys.`_user_role` where status = 1 and user = uid
	union select main_role as `role` from lam_sys.`_user` where status = 1 and id = uid)) a
	left join 
	(select * from lam_sys.`_alert` where status = 1 and (route is not null or url is not null) 
		and (layout is null or layout=theme) and (module is null or module=module)) b
	on b.id=a.alert;
end$$
DELIMITER ;
-- call lam_sys.get_userunreadalert_byuid_layout_module(1,'notika');

DROP PROCEDURE IF EXISTS lam_sys.get_table_schema;
DELIMITER $$
CREATE PROCEDURE  lam_sys.get_table_schema(sch VARCHAR(100),tbl VARCHAR(100))
begin
	SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE,COLUMN_DEFAULT, EXTRA
	FROM INFORMATION_SCHEMA.COLUMNS
	WHERE TABLE_SCHEMA = sch AND TABLE_NAME = tbl;
end$$
DELIMITER ;