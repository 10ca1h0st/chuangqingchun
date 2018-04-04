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
$searchStr = $_POST['searchStr'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));

$res = checkToken($con,$username,$token);
if($res === false){
    die(json_encode(['status'=>'fail','reason'=>error_check_token,],JSON_UNESCAPED_UNICODE));
}

/*
//此时代表$res===true
$teachers = getTeachers($con);
$weight = handleStr($teachers,$searchStr);

$arr_final = ['status'=>'success','results'=>[]];

foreach($weight as $key=>$value){
    if($value > 0){
        array_push($arr_final['results'],$teachers[$key]);
    }
}
if(count($arr_final) === 1){
    die(json_encode(['status'=>'fail','reason'=>error_search_no_result]));
}
else{
    echo json_encode($arr_final);
}
*/

$arr = search($con,$searchStr);

if(count($arr) === 0){
    die(json_encode(['status'=>'fail','reason'=>error_search_no_result]));
}
else{
    $arr_final = ['status'=>'success','results'=>$arr];
    echo json_encode($arr_final);
}

$con->close();

?>