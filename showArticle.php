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
$who = $_POST['who'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$type = $_POST['type'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));

$res = checkToken($con,$username,$token);
if($res === false){
    die(json_encode(['status'=>'fail','reason'=>error_check_token,],JSON_UNESCAPED_UNICODE));
}

$res = getArticle($con,$username,$who,$type);

if(count($res) === 0){
    die(json_encode(['status'=>'fail','reason'=>error_no_article,],JSON_UNESCAPED_UNICODE));
}

foreach($res as $key=>$value){
    if($value['comment'] == ''){
        $res[$key]['comment'] = 0;
    }
    else{
        $res[$key]['comment'] = count(explode(',',substr($value['comment'],1)));
    }
}


$data = ['status'=>'success','results'=>$res];

echo json_encode($data,JSON_UNESCAPED_UNICODE);

$con->close();




?>