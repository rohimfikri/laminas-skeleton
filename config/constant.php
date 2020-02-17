<?php
define("_APP_NAME_","Laminas App");
define("_APP_ALIAS_","Laminas");
define("_DEFAULT_THEME_","notika");
define("_DEBUG_",true);

define("_USE_LAMINAS_LOADER_",false);
define("_CONFIG_CACHE_ENABLED_",false);
define("_CONFIG_CACHE_KEY_",_APP_ALIAS_.'.config.cache');
define("_MODULE_MAP_CACHE_ENABLED_",false);
define("_MODULE_MAP_CACHE_KEY_",_APP_ALIAS_.'.modulemap.cache');
define("_CACHE_DIR_",APP_PATH.DS.'data'.DS.'cache'.DS);

define("HTTP_INTERNALSERVERERROR",500);
define("HTTP_REQUESTTIMEOUT",404);
define("HTTP_NOTFOUND",404);
define("HTTP_FORBIDDEN",403);
define("HTTP_UNAUTHORIZED",401);
define("HTTP_BADREQUEST",400);

//LOGIN TRY
define("_MAX_LOGIN_TRY_",3);
define("_LOGIN_WAIT_",5*60);

//Cache Constant
define("_SESSION_CACHE_NAMESPACE_",_APP_ALIAS_ . "_SESSION");
define("_SESSION_CACHE_TTL_", (60*60*24));
define("_SESSION_CACHE_DIR_",_CACHE_DIR_ . 'session');
define("_VIEW_CACHE_NAMESPACE_",_APP_ALIAS_ . "_VIEW");
define("_VIEW_CACHE_TTL_", (60*60*24));
define("_VIEW_CACHE_DIR_",_CACHE_DIR_ . 'view');
define("_DATA_CACHE_NAMESPACE_",_APP_ALIAS_ . "_DATA");
define("_DATA_CACHE_TTL_", (60*60*24));
define("_DATA_CACHE_DIR_",_CACHE_DIR_ . 'data');

//Session Constant
// define("_SESSION_SAVEHANDLER_","FILE");
define("_SESSION_TABLE_","_session");
define("_SESSION_NAME_",_APP_ALIAS_."_session");
define("_SESSION_SAVE_PATH_",APP_PATH.DS."data".DS."session");
define("_SESSION_SAVEHANDLER_","DB");
define("_SESSION_EXPIRE_",(60*60*24));
define("_COOKIE_LIFETIME_",0);
define("_GCMAX_LIFETIME_",(60*60*24*30));
define("_REMEMBER_ME_",(60*60*24*345));

//DB Constant
//SYS
define("_DBSYS_DRIVER_","Pdo_Mysql");
define("_DBSYS_HOST_","localhost");
define("_DBSYS_PORT_",3306);
define("_DBSYS_USER_","lam-sys");
define("_DBSYS_PASSWORD_","l4m_5y5");
define("_DBSYS_NAME_","lam_sys");
//APP
define("_DBAPP_DRIVER_","Pdo_Mysql");
define("_DBAPP_HOST_","localhost");
define("_DBAPP_PORT_",3306);
define("_DBAPP_USER_","lam-app");
define("_DBAPP_PASSWORD_","l4m_4pp");
define("_DBAPP_NAME_","lam_app");
//DUAL
define("_DBDUAL_DRIVER_","Pdo_Mysql");
define("_DBDUAL_HOST_","localhost");
define("_DBDUAL_PORT_",3306);
define("_DBDUAL_USER_","lam-dual");
define("_DBDUAL_PASSWORD_","l4m_du4l");
define("_DBDUAL_NAME_","lam_app");