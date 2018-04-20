<?php

//每一个文件都必须包含Functions.php，而Functions.php必须包含Constants.php

require_once('./Constants.php');

include_once('simple_html_dom.php');


//用来安全过滤的函数
function secHandle($input){

}

//从information.json中读出用户名，密码，数据库
function getInfo(){
    $json_str = file_get_contents("./information.json");
    $info = json_decode($json_str,True);
    return $info;
}

//连接数据库
function connectDB($host,$username,$password,$database){
    $con = new mysqli($host,$username,$password,$database);
    if($con->connect_errno){
        return false;
    }
    return $con;
}

//生成token的函数，传入参数为用户名，返回用户的token
function generateToken($username){
    $token = sha1($username.time().rand());
    return $token;
}


/*
先判断用户的token是否正确，如果正确，看expire_time是否过期，如果过期，则要重新登录，
如果没有过期，则用户每一次操作之后，修改users_token表中的expire_time字段
*/
function checkToken($con,$username,$token){
    $datetime = date('Y-m-d H:i:s');
    $select_sql = "select * from education.users_token where username='$username' and user_token='$token' and expire_time>'$datetime'";
    $res = $con->query($select_sql);
    if($res->num_rows <= 0){
        return false;
    }
    else{
        updateToken($con,$username,$token);
        return true;
    }
    
}

/**
 * 在users_token表中添加项
 */
function addToken($con,$username,$token){
    $datetime = date('Y-m-d H:i:s',time()+token_live_time);
    $insert_sql = "insert into education.users_token (username,user_token,expire_time) values ('$username','$token','$datetime')";
    $con->query($insert_sql);
}

//在users_token表中删除项
function deleteToken($con,$username){
    $delete_sql = "delete from education.users_token where username='$username'";
    $con->query($delete_sql);
}

//在users_token中更新项
function updateToken($con,$username,$token){
    $datetime = date('Y-m-d H:i:s',time()+token_live_time);
    $update_sql = "update education.users_token set expire_time='$datetime' where username='$username' and user_token='$token'";
    $con->query($update_sql);
}


//得到teachers表中的老师的信息，返回值为一个数组
function getTeachers($con){
    $select_sql = "select * from education.teachers where 1;";
    $res = $con->query($select_sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}

/*
//用来过滤名字
function getFilterArrayByName($name){
    $filterArrayByName = function ($row) use ($name) {
        if($row['name'] === $name){
            return true;
        }
        return false;
    };
    return $filterArrayByName;
}

//用来过滤性别
function getFilterArrayByGender($gender){
    $filterArrayByGender = function ($row) use ($gender) {
        if($row['gender'] === $gender){
            return true;
        }
        return false;
    };
    return $filterArrayByGender;
}

//用来过滤年级
function getFilterArrayByGrade($grade){
    $filterArrayByGrade = function ($row) use ($grade) {
        if($row['grade'] === $grade){
            return true;
        }
        return false;
    };
    return $filterArrayByGrade;
}

//用来过滤学校
function getFilterArrayBySchool($school){
    $filterArrayBySchool = function ($row) use ($school) {
        if($row['school'] === $school){
            return true;
        }
        return false;
    };
    return $filterArrayBySchool;
}

//用来过滤科目
function getFilterArrayBySubject($subject){
    $filterArrayBySubject = function ($row) use ($subject) {
        if($row['subject'] === $subject){
            return true;
        }
        return false;
    };
    return $filterArrayBySubject;
}
*/

/*

//用来过滤名字
function getFilterArrayByName($searchStr){
    $filterArrayByName = function ($row) use ($searchStr) {
        if(stripos($searchStr,$row['name']) === false){
            return false;
        }
        return true;
    };
    return $filterArrayByName;
}

//用来过滤性别
function getFilterArrayByGender($searchStr){
    $filterArrayByGender = function ($row) use ($searchStr) {
        if(stripos($searchStr,$row['gender']) === false){
            return false;
        }
        return true;
    };
    return $filterArrayByGender;
}

//用来过滤年级
function getFilterArrayByGrade($searchStr){
    $filterArrayByGrade = function ($row) use ($searchStr) {
        if(stripos($searchStr,$row['grade']) === false){
            return false;
        }
        return true;
    };
    return $filterArrayByGrade;
}

//用来过滤学校
function getFilterArrayBySchool($searchStr){
    $filterArrayBySchool = function ($row) use ($searchStr) {
        if(stripos($searchStr,$row['school']) === false){
            return false;
        }
        return true;
    };
    return $filterArrayBySchool;
}

//用来过滤科目
function getFilterArrayBySubject($searchStr){
    $filterArrayBySubject = function ($row) use ($searchStr) {
        if(stripos($searchStr,$row['subject']) === false){
            return false;
        }
        return true;
    };
    return $filterArrayBySubject;
}


function changeWeight($arr_filter,$weight){
    foreach(array_keys($arr_filter) as $value){
        $weight[$value] += 1;
    }
    return $weight;
}

//处理输入的字符串，用在search.php中
function handleStr($arr,$searchStr){
    $filterFunctions = ['getFilterArrayByName','getFilterArrayByGender','getFilterArrayByGrade',
    'getFilterArrayBySchool','getFilterArrayBySubject'];
    $weight = array_fill(0,count($arr),0);
    foreach($filterFunctions as $value){
        $arr_filter = array_filter($arr,($value)($searchStr));
        $weight = changeWeight($arr_filter,$weight);
    }
    return $weight;
}
*/

//对search.php中使用的函数的改进
/*SELECT * FROM `teachers` WHERE 
locate(name,'西电 数学')>0 or locate(gender,'西电 数学')>0 or 
locate(grade,'西电 数学')>0 or locate(school,'西电 数学')>0 or 
locate(college,'西电 数学')>0 or locate(subject,'西电 数学')>0
*/
function search($con,$searchStr){
    $select_sql = "SELECT * FROM teachers 
    WHERE locate(name,'$searchStr')>0 or locate(gender,'$searchStr')>0 
    or locate(grade,'$searchStr')>0 or locate(school,'$searchStr')>0 
    or locate(alias,'$searchStr')>0 or locate(college,'$searchStr')>0 
    or locate(subject,'$searchStr')>0";
    $res = $con->query($select_sql);
    if($res->num_rows <= 0){
        $select_sql = "select * from teachers where 1";
        $res = $con->query($select_sql);
        $arr = $res->fetch_all(MYSQLI_ASSOC);
        $arr2 = [];
        foreach($arr as $key=>$value){
            array_shift($value);
            array_push($arr2,$value);
        }
        return $arr2;
    }
    else{
        $arr = $res->fetch_all(MYSQLI_ASSOC);
        $arr2 = [];
        foreach($arr as $key=>$value){
            array_shift($value);
            array_push($arr2,$value);
        }
        return $arr2;
    }
}


//每一个新用户注册成功之后，建立一张以新用户的注册名为表名的数据表，数据表中的字段为friend，代表该用户的好友
function createUserDB($con,$username){
    $create_sql = "create database $username;";
    if($con->query($create_sql) === False){
        return False;
    }
    $create_sql = "create table $username.friends ( name text not null );";
    if($con->query($create_sql) === False){
        return False;
    }
    $create_sql = "create table $username.article ( name text not null,time datetime not null,title text not null,content text not null );";
    if($con->query($create_sql) === False){
        return False;
    }
    publishArticle($con,$username);
    return True;
}

//每一个新用户在出创建的时候，都会在用户对应的数据库下的article表中插入一条默认的说说
function publishArticle($con,$username,$title="通知",$content="快来Youtome分享你的生活吧 :)"){
    $publishTime = date('Y-m-d H:i:s',time());
    $insert_sql = "insert into $username.article (name,time,title,content) values ('$username','$publishTime','$title','$content');";
    $con->query($insert_sql);
}

//从用户的数据库中的article表中得到用户发布的说说
function getArticle($con,$username,$who){
    //用户自己发表的说说
    if($who == 'mine'){
        $select_sql = "select name,time,title,content from $username.article order by time desc limit 0,10";
        $res = $con->query($select_sql);
        $articles = $res->fetch_all(MYSQLI_ASSOC);
        $arr = [];
        foreach($articles as $key=>$value){
            array_push($arr,$value);
        }
        return $arr;
    }
    //用户的朋友圈
    else if($who == 'friends'){
        $select_sql = "select name from $username.friends";
        $res = $con->query($select_sql);
        if($res->num_rows <= 0){
            return array();
        }
        $friends = $res->fetch_all(MYSQLI_ASSOC);
        $arr = [];
        foreach($friends as $key=>$value){
            $res = getArticle($con,$value['name'],'mine');
            array_push($arr,$res);
        }
        $arr_t = [];
        foreach($arr as $key=>$value){
            foreach($value as $key1=>$value1){
                array_push($arr_t,$value1);
            }
        }
        $arr = $arr_t;
        shuffle($arr);
        return $arr;
    }
}

//用户添加朋友
function addFriend($con,$username,$friend){
    if($username == $friend){
        return false;
    }
    //防止用户重复添加好友
    $select_sql = "select name from $username.friends where name = '$friend'";
    $res = $con->query($select_sql);
    if($res->num_rows > 0){
        return false;
    }

    $select_sql = "select username from education.users where username = '$friend'";
    $res = $con->query($select_sql);
    if($res->num_rows <= 0){
        return false;
    }

    $insert_sql = "insert into $username.friends (name) values ('$friend')";
    $res = $con->query($insert_sql);
    if($res === false){
        return false;
    }
    return true;
}

//用于让考生根据分数查成绩
/*
返回值为:一个数组，数组中包含:
university	哈尔滨工业大学
location	黑龙江
batch	本科一批
introduction	http://gaokao.chsi.com.cn/sch/schoolInfo--schId-174.dhtml
belong	工业和信息化部
type	工科
property	985211
satisfaction	4.6
logo	http://gaokao.chsi.com.cn/sch/image/view.do?id=272083485
*/
function queryUniversity($ssdm,$score,$year,$ranger,$kldm){
    $url = "http://gaokao.chsi.com.cn/lqfs/query.do?ssdm=$ssdm&year=$year&kldm=$kldm&score=$score&ranger=$ranger&type=0";
    $html = file_get_html($url);
    $table = $html->find('body div.bodydiv div.left div#leftHeight div.chooseDiv',0)->next_sibling();
    $universitys = $table->children();
    array_shift($universitys);
    
    $arr = [];
    $dict = array('university','location','batch');

    $random_length = random_int(2,4);
    foreach($universitys as $key=>$value){
        if($key > $random_length){
            break;
        }

        $arr_in = [];
        
        foreach($value->children() as $key1=>$value1){
            if($key1 > 2){
                break;
            }
            
            if($key1 == 0){
                preg_match('/(\D+).*/i',rtrim(ltrim($value1->plaintext)),$matches);
                $arr_in[$dict[$key1]] = $matches[1];
                continue;
            }
            
            $arr_in[$dict[$key1]] = rtrim(ltrim($value1->plaintext));
        }
        $info = queryUniversityInfo($arr_in['university']);
        $arr_in = array_merge($arr_in,$info);
        array_push($arr,$arr_in);
    }
    return $arr;
    
}

//根据学校名称获得学校具体信息
/*
http://gaokao.chsi.com.cn/sch/search.do?searchType=1&yxmc=西北工业大学

introduction:学校介绍的网址
belong:院校隶属
type:院校类型
property:985 or 211
satisfaction:满意度
logo:校徽的url
*/
function queryUniversityInfo($university){
    $urlbase = 'http://gaokao.chsi.com.cn';
    $university = urlencode($university);
    $url = "http://gaokao.chsi.com.cn/sch/search.do?searchType=1&yxmc=$university";
    $html = file_get_html($url);
    $selected = $html->find('body div.container div.yxk-table table tbody tr',1);
    $info = array();
    $info['introduction'] = $urlbase.rtrim(ltrim($selected->find('td',0)->find('a',0)->href));
    $info['belong'] = rtrim(ltrim($selected->find('td',2)->plaintext));
    $info['type'] = rtrim(ltrim($selected->find('td',3)->plaintext));
    $info['property'] = '';
    foreach($selected->find('td',5)->find('span') as $key=>$value){
        $info['property'] .= rtrim(ltrim($value->plaintext));
    }
    $info['satisfaction'] = rtrim(ltrim($selected->find('td',7)->plaintext));
    $info['logo'] = queryUniversityLogo($info['introduction'],$urlbase);
    return $info;
    //var_dump($info);
    

}

//根据学校的介绍网址，得到学校的图片的地址
function queryUniversityLogo($url,$urlbase){
    $html = file_get_html($url);
    $image = rtrim(ltrim($html->find('body div.container div.yxk-yxmsg div.left img',0)->src));
    $image = $urlbase.$image;
    return $image;
}



?>