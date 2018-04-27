<?php
//这个文件用来定义所有的常量字符串

//定义代表用户身份的token的有效期
define('token_live_time',24*60*60);

//当连接数据库失败时的错误提示
define('error_connect_db','can not connect to database');

//当注册的用户名已经存在时的错误提示
define('error_signup','用户名已经存在');

//当执行sql语句失败时的错误提示
define('error_execute_sql','executing sql statement failed');

//当登陆时输入的用户名或者是密码错误时的错误提示
define('error_signin','用户名或者密码错误');

//当没有json数据发送过来时的错误提示
define('error_json','no json data');

//当检查token失败时的错误提示
define('error_check_token','checking token failed');

//当查询没有结果时
define('error_search_no_result','no matching results');

//当创建用户数据表失败时
define('error_create_user_table','creating user table failed');

//当用户请求说说但是还没有说说时
define('error_no_article','sorry don not have a article');

//当用户添加朋友失败时
define('error_add_friend','sorry you add friend failed');

//当用户重复点赞时
define('error_good_article','sorry you have gooded article');

//当没有人点赞还请求点赞人的姓名时
define('error_get_gooder','no one good');

//当没有评论还请求评论时
define('error_get_comment','no one comment');

//当上传头像失败时
define('error_upload_head','upload head fail');

?>