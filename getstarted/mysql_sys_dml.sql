use lam_sys;

INSERT INTO `lam_sys`.`_role`
(code, name, status, redirect_route, redirect_param, redirect_query, redirect_url)
VALUES('SUPADM', 'SUPER ADMIN', 1, 'Admin', NULL, NULL, NULL);

INSERT INTO `lam_sys`.`_ubis`
(code, name, status, parent, `level`, redirect_route, redirect_param, redirect_query, redirect_url)
VALUES('SUPADM', 'Super Admin', 1, NULL, NULL, 'Admin', NULL, NULL, NULL);

INSERT INTO `lam_sys`.`_user`
(username, full_name, password, email, status, pass_reset_token, created_date, pass_reset_date, redirect_route, redirect_param, redirect_query, redirect_url, main_role, main_ubis, is_ldap)
VALUES('admin', 'Super Admin', '$2y$10$fXGyPKdxujgtiCJAYVQIgO3Mg75RnsEbYLeUxGIvTL92rdakJVM3m', 'admin@localhost', 1, NULL, '2019-12-10 10:19:14.0', NULL, 'Admin', NULL, NULL, '', 'SUPADM', 'SUPADM', 0);