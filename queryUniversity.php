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

/*
http://gaokao.chsi.com.cn/lqfs/query.do?ssdm=33&year=2016&kldm=1&score=620&ranger=10&type=0
ssdm是考生所在的省份，year是根据哪一年的分数线判断，kldm是文科(1)还是理科(5)，score是分数，ranger是分数上下浮动的范围
 */


$conversion_ssdm = ['北京'=>'11','天津'=>'12','河北'=>'13','山西'=>'14','内蒙古'=>'15','辽宁'=>'21','吉林'=>'22',
                    '黑龙江'=>'23','上海'=>'31','江苏'=>'32','浙江'=>'33','安徽'=>'34','福建'=>'35','江西'=>'36',
                    '山东'=>'37','河南'=>'41','湖北'=>'42','湖南'=>'43','广东'=>'44','广西'=>'45','海南'=>'46',
                    '重庆'=>'50','四川'=>'51','贵州'=>'52','云南'=>'53','西藏'=>'54','陕西'=>'61','甘肃'=>'62',
                    '青海'=>'63','宁夏'=>'64','新疆'=>'65'];

$conversion_kldm = ['理科'=>'5','文科'=>'1'];

$username = $_POST['username'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$token = $_POST['token'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$score = $_POST['score'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$kldm = $_POST['kldm'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$ssdm = $_POST['ssdm'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$year = $_POST['year'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));
$ranger = $_POST['ranger'] or die(json_encode(['status'=>'fail','reason'=>error_json,],JSON_UNESCAPED_UNICODE));

$ssdm = $conversion_ssdm[$ssdm];
$kldm = $conversion_kldm[$kldm];

$res = checkToken($con,$username,$token);
if($res === false){
    die(json_encode(['status'=>'fail','reason'=>error_check_token,],JSON_UNESCAPED_UNICODE));
}

$res = queryUniversity($ssdm,$score,$year,$ranger,$kldm);
$arr_final = ['status'=>'success','results'=>$res];
echo json_encode($arr_final,JSON_UNESCAPED_UNICODE);
$con->close();

?>