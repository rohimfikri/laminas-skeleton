-- CREAT DATABASE FIRST

CREATE USER 'lam-sys'@'localhost';
ALTER USER 'lam-sys'@'localhost' IDENTIFIED BY 'l4m_5y5';
GRANT ALL PRIVILEGES ON lam_sys.* TO 'lam-sys'@'localhost';
FLUSH PRIVILEGES;

CREATE USER 'lam-sys'@'%';
ALTER USER 'lam-sys'@'%' IDENTIFIED BY 'l4m_5y5';
GRANT ALL PRIVILEGES ON lam_sys.* TO 'lam-sys'@'%';
FLUSH PRIVILEGES;

CREATE USER 'lam-app'@'localhost';
ALTER USER 'lam-app'@'localhost' IDENTIFIED BY 'l4m_4pp';
GRANT ALL PRIVILEGES ON lam_app.* TO 'lam-app'@'localhost';
FLUSH PRIVILEGES;

CREATE USER 'lam-app'@'%';
ALTER USER 'lam-app'@'%' IDENTIFIED BY 'l4m_4pp';
GRANT ALL PRIVILEGES ON lam_app.* TO 'lam-app'@'%';
FLUSH PRIVILEGES;

FLUSH PRIVILEGES;
CREATE USER 'lam-dual'@'localhost';
ALTER USER 'lam-dual'@'localhost' IDENTIFIED BY 'l4m_du4l';
GRANT ALL PRIVILEGES ON lam_sys.* TO 'lam-dual'@'localhost';
FLUSH PRIVILEGES;

CREATE USER 'lam-dual'@'%';
ALTER USER 'lam-dual'@'%' IDENTIFIED BY 'l4m_du4l';
GRANT ALL PRIVILEGES ON lam_sys.* TO 'lam-dual'@'%';
FLUSH PRIVILEGES;

FLUSH PRIVILEGES;
CREATE USER 'lam-dual'@'localhost';
ALTER USER 'lam-dual'@'localhost' IDENTIFIED BY 'l4m_du4l';
GRANT ALL PRIVILEGES ON lam_app.* TO 'lam-dual'@'localhost';
FLUSH PRIVILEGES;

CREATE USER 'lam-dual'@'%';
ALTER USER 'lam-dual'@'%' IDENTIFIED BY 'l4m_du4l';
GRANT ALL PRIVILEGES ON lam_app.* TO 'lam-dual'@'%';
FLUSH PRIVILEGES;