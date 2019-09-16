<?php
require_once __DIR__ . '/qiniuapi.php';
$access_key = 'HD5ciAh6Thjrh2puTtoyvdg8crp-nev4JEhW4YnH';
$secret_key = 'QzQsyhjUlg_IZSl06Uo8mgJN1rgT8AUy4shKOFNQ';
$bucket = 'cloud_goods';
$domain = 'https://static.zeropartin.com/';
//qiniu cloud update img 
if($access_key==""||$secret_key==""){
  exit("参数不能为空");
}
$upgoodsimg = new uploadImage();
$upgoodsimg->access_key =$access_key;
$upgoodsimg->secret_key =$secret_key;
$upgoodsimg->bucket = $bucket;
$upgoodsimg->domain = $domain;
$upgoodsimg->getAuthToken();
//var_dump($upgoodsimg->token);

//open mysql database
$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='api_zeropartin_com_dbs';    //使用的数据库
$user='debian-sys-maint';      //数据库连接用户名
$pass='OiRdwBrifghSzWNL';          //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";
try {
	$dbh = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true));
	$dbh->query('set names utf8');
} catch (PDOException $e) {
	    die ("Error!: " . $e->getMessage() . "<br/>");
}

#redis数据出队操作,从redis中将请求取出
$redis = new Redis();
$redis->pconnect('127.0.0.1',6379);
$queuename = $argv[1];
while(true){
	try{
		$goodsqueue = $redis->brpop($queuename,600);
		if($goodsqueue[1]==="over"||$goodsqueue==null){
			break;	
		}
		$goodsarr =json_decode($goodsqueue[1],true); 
		if(isPutImg($dbh,$goodsarr["gid"],$domain)===false){
			continue;
		}
		updateimg($goodsarr,$upgoodsimg,$dbh);
	#	exit;
	//	var_dump($goodsarr);
		echo "update img ".$goodsarr["gid"]."\n";
	#	usleep(500);
	}catch(Exception $e){
		echo $e->getMessage();
	}
}
function isPutImg($dbh,$gid,$domain){
	$sqlgetimg = "select content,thumb_url from ns_goods where goods_id= :gid";
	$sth = $dbh->prepare($sqlgetimg);
	$sth->bindParam(':gid', $gid, PDO::PARAM_INT);
	$sth->execute();
#	$sth->debugDumpParams();
	$result=$sth->fetch(PDO::FETCH_ASSOC);
	if(isset($result["content"])&&strstr($result["content"],$domain)){
		return false;
	}
	if(isset($result["thumb_url"])&&strstr($result["thumb_url"],$domain)){
		return false;
	}
	return true;
}
function updateimg ($result,$upgoodsimg,$dbh){
	$sql = "UPDATE ns_goods SET content= :content,thumb_url= :thumb_url WHERE goods_id= :gid";
	if(isset($result["img"])){
		$content_pic = explode(";", $result['img']);
		unset($result["img"]);
		$content = "";
		foreach ($content_pic as $k=>$img) {
			$newimg=$upgoodsimg->uploadImg($img,"good_".$k."_".$result["gid"]);
			if($newimg==false){
				$content .= "<p>< img src={$img} width=\"100%\"/></p >";
			}else{
				$content .= "<p>< img src={$newimg} width=\"100%\"/></p >";
			}
		}
		unset($img);
		unset($newimg);
	}
	if(isset($result["displayImgUrls"])){
		$thumb_urlarr =explode(";", $result['displayImgUrls']);  	
		unset($result["displayImgUrls"]);
		$thumb_url = [];
		foreach($thumb_urlarr as $k=>$img){
			$newimg=$upgoodsimg->uploadImg($img,"good_thumb_".$k."_".$result["gid"]);
			if($newimg==false){
				array_push($thumb_url,$img);
			}else{
				array_push($thumb_url,$newimg);
			}
		
		}
		
	}
	$result["thumb_url"]= $thumb_url;
	$thumbstr = serialize($thumb_url);
	if(isset($result["content"])){
		$result["content"].=$content;
	}else{
		$result["content"] = $content;	
	}
/*	var_dump($result);
	var_dump($thumbstr);
 */	
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':content', $result["content"], PDO::PARAM_STR, 6400);
	$sth->bindParam(':thumb_url', $thumbstr, PDO::PARAM_STR, 6400);
	$sth->bindParam(':gid', $result["gid"], PDO::PARAM_INT);
	$sth->execute();
//	$sth->debugDumpParams();
}
