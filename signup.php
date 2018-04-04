<?php

//跨域时要加上这个头
header('Access-Control-Allow-Origin:*');

header('Content-type:application/json;charset=utf-8');

require_once('./Functions.php');


/**
 * 注意，mysql编码必须是utf8
 */

function insertDB($con,$username,$password){
    $password = sha1($password);
    $select_sql = "select * from education.users where username='$username'";
    //echo $select_sql;
    if($res = $con->query($select_sql)){
        if($res->num_rows > 0){
            //发送中文需要json_encode第二个参数指定为JSON_UNESCAPED_UNICODE
            die(json_encode(['status'=>'fail','reason'=>error_signup,],JSON_UNESCAPED_UNICODE));
            return false;
        }
    }
    else{
        die(json_encode(['status'=>'fail','reason'=>error_execute_sql,],JSON_UNESCAPED_UNICODE));
        return false;
    }

    $insert_sql = "insert into education.users (username,password) values ('$username','$password');";
    //echo $insert_sql;
    if(!$con->query($insert_sql)){
        die(json_encode(['status'=>'fail','reason'=>error_execute_sql,],JSON_UNESCAPED_UNICODE));
        return false;
    }
    return true;
}


$username = $_POST['username'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$password = $_POST['password'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));

$info = getInfo();

$con = connectDB($info["host"],$info["username"],$info["password"],$info["db"]);
if($con === false){
    die(json_encode(['status'=>'fail','reason'=>error_connect_db,],JSON_UNESCAPED_UNICODE));
}

$res = insertDB($con,$username,$password);
if($res === true){
    $res = createUserDB($con,$username);
    if($res === False){
        die(json_encode(['status'=>'fail','reason'=>error_create_user_table,],JSON_UNESCAPED_UNICODE));
    }
    else{
        echo json_encode(['status'=>'success','reason'=>''],JSON_UNESCAPED_UNICODE);
    }
}
$con->close();


?>