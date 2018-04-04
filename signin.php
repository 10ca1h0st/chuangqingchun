<?php

//跨域时要加上这个头
header('Access-Control-Allow-Origin:*');

header('Content-type:application/json;charset=utf-8');

require_once('./Functions.php');


function selectDB($con,$username,$password){
    $password = sha1($password);
    $select_sql = "select * from education.users where username='$username' and password='$password'";
    if($res = $con->query($select_sql)){
        if($res->num_rows === 0){
            die(json_encode(['status'=>'fail','reason'=>error_signin,],JSON_UNESCAPED_UNICODE));
            return false;
        }
    }
    else{
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

$res = selectDB($con,$username,$password);
if($res === true){
    $token = generateToken($username);
    echo json_encode(['status'=>'success','reason'=>'','token'=>$token,],JSON_UNESCAPED_UNICODE);
    deleteToken($con,$username);
    addToken($con,$username,$token);
}
$con->close();

?>