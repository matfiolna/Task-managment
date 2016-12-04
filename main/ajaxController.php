<?php 

require 'config.php';

$db = new PDO($dsn, $user, $password);



if($_POST){
	if($_POST['controller']){
		$func = $_POST['controller'];
		unset($_POST['controller']);
		call_user_func($func, $_POST);

	}
}

function register($arr){
	global $db;
	$sql = $db->prepare("SELECT COUNT(*) as count FROM `users` WHERE email=? OR nickname=?");
	$sql->execute(array($arr['email'], $arr['nickname']));
	$result = $sql->fetchAll();
	if(!$result[0]['count']){
		$token = md5(microtime());
		$sql = $db->prepare("INSERT INTO `users` (`email`, `password`, `nickname`, `token`) VALUES (?,?,?,?)");
		$sql->execute(array((string)$arr['email'], (string)$arr['password'], (string)$arr['nickname'], (string)$token));
		$_POST['token'] = $token;
		logedIn();
	}else{
		echo 'email or login already has been taken';
	}
}

function logIn($arr){
	global $db;
	$sql = $db->prepare("SELECT COUNT(*) as count FROM `users` WHERE email=? AND password=?");
	$sql->execute(array($arr['email'], $arr['password']));
	$result = $sql->fetchAll();
	if($result[0]['count']){
		$token = md5(microtime());
		$sql = $db->prepare("UPDATE `users` SET token=? WHERE email=?");
		$sql->execute(array($token, $arr['email']));
		$_POST['token'] = $token;
		logedIn();
	}else{
		echo 'wrong mail or password';
	}
}


function logedIn(){
	$userId = isLogedIn();
	if(!$userId) return 0;

	$response['token'] = $_POST['token'];
	$response['user'] = getUserbyId($userId);
	echo json_encode($response);
}



function getUserByToken(){

	$token = $_POST['token'];
	$userId = isLogedIn($token);
	if($userId){
		$response['user'] = getUserbyId($userId);
		$response['status'] = 1;
		echo json_encode($response);
	}
}

function isLogedIn(){
	$token = $_POST['token'];
	global $db;
	$sql = $db->prepare("SELECT id FROM `users` WHERE token=?");
	$sql->execute(array((string)$token));
	$userId = $sql->fetchAll();
	@$userId = $userId[0]['id'];
	return $userId;
}

function getUserbyId($id){
	global $db;
	$sql = $db->prepare("SELECT `id`,`email`,`nickname` FROM `users` WHERE id=?");
	$sql->execute(array((int)$id));
	$userId = $sql->fetchAll();
	foreach ($userId as $key => $array) {
		foreach ($array as $key1 => $value) {
			if(is_numeric($key1)){
				unset($userId[$key][$key1]);
			}
		}
	}
	$user = $userId[0];
	return $user;
}


function getTasks(){
	$userId = isLogedIn();
	global $db;
	$sql = $db->prepare("SELECT * FROM `tasks` WHERE user_id=?");
	$sql->execute(array((int)$userId));
	$tasks = $sql->fetchAll();
	foreach ($tasks as $key => $array) {
		foreach ($array as $key1 => $task) {
			if(is_numeric($key1)){
				unset($tasks[$key][$key1]);
			}
		}
	}
	$tasks = json_encode($tasks);
	echo $tasks;
}

function createTask($arr){
	$text = $arr['text'];
	$priority = (isset($arr['priority']) && $arr['priority']) ? 1 : 0;
	$finish_to = (int)$arr['finish_to'] ? (int)$arr['finish_to'] : 'null';
	$userId = isLogedIn();
	global $db;
	$sql = $db->prepare("INSERT INTO `tasks`(`text`,`priority`,`finish_to`,`user_id`) VALUES(:text,:priority,:finish_to,:userId)");
	$sql->bindValue(':text',  $text, PDO::PARAM_STR);
	$sql->bindValue(':priority',  $priority, PDO::PARAM_INT);
	$sql->bindValue(':finish_to',  $finish_to, PDO::PARAM_INT);
	$sql->bindValue(':userId',  $userId, PDO::PARAM_INT);
	$sql->execute();
	//print_r("INSERT INTO `tasks`(`text`,`priority`,`finish_to`,`userId`) VALUES($text,$priority,$finish_to,$userId)");
}

function updateTaskDone($arr){

	$taskId = (int)$arr['taskId'];
	$status = 1 - (int)$arr['status'];

	$userId = isLogedIn();
	global $db;
	$sql = $db->prepare("UPDATE `tasks` SET status=? WHERE user_id=? AND id=?");
	$sql->execute(array((int)$status, (int)$userId, (int)$taskId));
	print_r(array((int)$status, (int)$userId, (int)$taskId));
}

function removeTask($arr){
	$taskId = (int)$arr['taskId'];;
	$userId = isLogedIn();

	global $db;
	$sql = $db->prepare("DELETE FROM `tasks` WHERE user_id=? AND id=?");
	$sql->execute(array((int)$userId,(int)$taskId));
}
/*
$sth = $dbh->prepare('SELECT name, colour, calories
    FROM fruit
    WHERE calories < ? AND colour = ?');
$sth->execute(array(150, 'red'));
$red = $sth->fetchAll();
$sth->execute(array(175, 'yellow'));
$yellow = $sth->fetchAll();*/