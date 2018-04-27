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

    $create_sql = "create table $username.article ( id int not null auto_increment,type enum('1','2','3') not null,title text not null,content text not null,username text not null,sourcer text not null,time datetime not null,good int not null,comment text not null,transmit int not null,primary key(id) );";
    if($con->query($create_sql) === False){
        return False;
    }

    $create_sql = "create table $username.comment ( id int not null auto_increment,comment text not null,commenter text not null,primary key(id) );";
    if($con->query($create_sql) === False){
        return False;
    }

    $create_sql = "create table $username.good ( good_id int not null,gooder text not null );";
    if($con->query($create_sql) === False){
        return False;
    }

    $create_sql = "create table $username.information ( nickname text not null,signature text not null,school text not null,sex enum('男','女','未知') not null,birthday date not null,area text not null,year date not null,major text not null,area_aim text not null,phone text not null,email text not null,img text not null );";
    if($con->query($create_sql) === False){
        return False;
    }

    mkdir('users/'.$username);

    pre_perfect_info($con,$username);
    publishArticle($con,$username,"通知","快来Youtome分享你的生活吧 :)",'3',$username,'-1');
    return True;
}

//每一个新用户在出创建的时候，都会在用户对应的数据库下的article表中插入一条默认的说说
function publishArticle($con,$username,$title,$content,$type,$sourcer,$id){
    $publishTime = date('Y-m-d H:i:s',time());
    $insert_sql = "insert into $username.article (type,title,content,username,sourcer,time,good,comment,transmit) values ('$type','$title','$content','$username','$sourcer','$publishTime','0','',0);";
    $con->query($insert_sql);
    if($username != $sourcer){
        $update_sql = "update $sourcer.article set transmit=transmit+1 where id='$id'";
        $con->query($update_sql);
    }
}

//从用户的数据库中的article表中得到用户发布的说说
function getArticle($con,$username,$who,$type){
    //用户自己发表的说说
    if($who == 'mine'){
        $select_sql = "select * from $username.article where type='$type' order by time desc limit 0,10";
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
            $res = getArticle($con,$value['name'],'mine',$type);
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


//用户评论文章
function commentArticle($con,$username,$belong,$id,$content){
    $insert_comment = "insert into $belong.comment (comment,commenter) values ('$content','$username')";
    $con->query($insert_comment);
    $get_id = "select last_insert_id()";
    $res = $con->query($get_id);
    $comment_id = $res->fetch_all()[0][0];
    $update_sql = "update $belong.article set comment=concat(comment,concat(',','$comment_id')) where id='$id'";
    $con->query($update_sql);

}

//用户点赞
function goodArticle($con,$username,$belong,$id){
    //先判断是否已经点过赞
    $isGood_sql = "select * from $belong.good where good_id='$id' and gooder='$username'";
    $res = $con->query($isGood_sql);
    if($res->num_rows > 0){
        return false;
    }

    $good_sql = "update $belong.article set good=good+1 where id='$id'";
    $con->query($good_sql);
    $good_sql = "insert into $belong.good (good_id,gooder) values ('$id','$username')";
    $con->query($good_sql);
    return true;
}

//返回用户评论
function getComment($con,$belong,$id){
    $select_sql = "select comment from $belong.article where id='$id'";
    $res = $con->query($select_sql);
    
    $comment_ids = explode(',',substr($res->fetch_all()[0][0],1));
    $comments = array();
    foreach($comment_ids as $key=>$value){
        $select_sql = "select comment,commenter from $belong.comment where id='$value'";
        $res = $con->query($select_sql);
        $arr = $res->fetch_all(MYSQLI_ASSOC);
        foreach($arr as $key1=>$value1){
            $tem = ['comment'=>$value1['comment'],'commenter'=>$value1['commenter']];
            array_push($comments,$tem);
        }

    }
    return $comments;
    
}

//返回点赞的人的名字

function getGooder($con,$belong,$id){
    $select_sql = "select * from $belong.good where good_id='$id'";
    $res = $con->query($select_sql);
    $arr = $res->fetch_all(MYSQLI_ASSOC);
    $gooders = array();
    foreach($arr as $key=>$value){
        $gooder = ['gooder'=>$value['gooder']];
        array_push($gooders,$gooder);
    }

    return $gooders;
}


//下面这一部分是完善用户信息的函数

//上传头像，这部分直接在upload_head.php中实现
function upload_head(){}


//修改用户详细信息
function perfect_info($con,$username,$nickname,$signature,$school,$sex,$birthday,$area,$year,$major,$area_aim,$phone,$email){
    $update_sql = "update $username.information set nickname='$nickname',signature='$signature',school='$school',sex='$sex',birthday='$birthday',area='$area',year='$year',major='$major',area_aim='$area_aim',phone='$phone',email='$email' where 1;";
    $con->query($update_sql);
}

function pre_perfect_info($con,$username){
    $insert_sql = "insert into $username.information ( nickname,signature,school,sex,birthday,area,year,major,area_aim,phone,email,img ) values ( '昵称','个性签名','学校','未知','1000-01-01','地区','1000-01-01','意向专业','地区','电话号码','email','users/default/head_img' );";
    $con->query($insert_sql);
}



?>