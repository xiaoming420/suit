<?php
header('Content-type:text/html;charset=utf-8');
//$dsn = "mysql:host=10.100.0.38;dbname=mcdonalds";
//$db = new PDO($dsn, 'mcdonald', '2OW$k@ja');



//$dsn = "mysql:host=localhost;dbname=mcds";
//$db = new PDO($dsn, 'root', '123456');

$type = 'mysql'; //数据库类型
//$db_name = 'mcdonalds'; //数据库名
//$host = '10.100.0.38';
//$username = 'mcdonald';
//$password = '2OW$k@ja';

$db_name = 'mcds'; //数据库名
$host = '127.0.0.1';
$username = 'root';
$password = '123456';

$dsn = "$type:host=$host;dbname=$db_name";
try {
    //建立持久化的PDO连接
    $db = new PDO($dsn, $username, $password, array(PDO::ATTR_PERSISTENT => false));
} catch (Exception $e) {
    die('连接数据库失败!');
}

$phone = trim(isset($_GET['phone']) ? $_GET['phone'] : '');
if (empty($phone)) {
    return "phone can't empty";
}

$rs = $db->query("SELECT id, phone, nickname FROM mcds_users where phone=".$phone);
//$col = $rs->fetchColumn(); // 获取一个字段
//var_dump($col); // 46

$rs->setFetchMode(PDO::FETCH_ASSOC);
$result = $rs->fetchAll();
var_dump($result);die;
if ($result) {
    die('添加成功');
}
$openid = time().rand(10000,99999).rand(10000,99999);
$unionid = time().rand(10000,99999).rand(10000,99999);

$count = $db->exec("INSERT INTO mcds_users SET phone = $phone,openid=$openid,unionid=$unionid");
if ($count) {
    return 'success1';
}
return 'error1';