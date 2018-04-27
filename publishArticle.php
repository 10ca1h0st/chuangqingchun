<?php
//跨域时要加上这个头
header('Access-Control-Allow-Origin:*');

header('Content-type:application/json;charset=utf-8');

require_once('./Functions.php');

$info = getInfo();

$con = connectDB($info["host"],$info["username"],$info["password"],$info["db"]);
if($con === false){
    die(json_encode(['status'=>'fail','reason'=>error_connect_db,],JSON_UNESCAPED_UNICODE));
}

$username = $_POST['username'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$token = $_POST['token'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$title = $_POST['title'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$content = $_POST['content'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$type = $_POST['type'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$sourcer = $_POST['sourcer'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$id = $_POST['id'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));

$res = checkToken($con,$username,$token);
if($res === false){
    die(json_encode(['status'=>'fail','reason'=>error_check_token,],JSON_UNESCAPED_UNICODE));
}

publishArticle($con,$username,$title,$content,$type,$sourcer,$id);
echo json_encode(['status'=>'success','reason'=>''],JSON_UNESCAPED_UNICODE);
$con->close();


?>