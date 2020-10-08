<?php
set_time_limit(0);
header('Content-Type: application/json');

/*
Script name: 	RenameTPLinkPro
Script Ver: 	1.0.0
Router Support:	TP-Link
Model Router: 	TL-WR841N, ....
Author:			Thiên Đẹp Zaii
*/


//---------------Config----------------------
//nhập tài khoản mật khẩu truy cập vào TP-LINK ( Lật mặt dưới của router để xem)
$username = "admin";
$password = "admin";
//Tên Wifi cần đổi ( icon đẹp truy cập https://run.vn/ )
$nameWifi = "❣️ Thiên Đẹp Zaii ❣️";
//-------------------------------------------


$nameWifi = urlencode($nameWifi);
$cookie = _buildCookie($username, $password);
if(($pathId=_loginTPLink($cookie))!==false){
	_reNameWifiTPLink($pathId, $cookie, $nameWifi);
	die("Đổi tên Wifi TPLink thành công!, Mở Wifi lên và xem kết quả :))");
}else{
	die("Đăng nhập thất bại, Có thể tài khoản, mật khẩu đăng nhập TP-Link sai!");
}


function _reNameWifiTPLink($pathId, $cookie, $nameWifi){
	$URL = "http://tplinkwifi.net/$pathId/userRpm/WlanNetworkRpm.htm?ssid1=$nameWifi&ssid2=ThienDepZaii_2&ssid3=ThienDepZaii_3&ssid4=ThienDepZaii_4&region=101&band=0&mode=5&chanWidth=2&channel=15&rate=71&ap=1&broadcast=2&brlssid=&brlbssid=&addrType=1&keytype=1&wepindex=1&authtype=1&keytext=&Save=Save";
	$header = array(
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9", 
		"Accept-Encoding: gzip, deflate", 
		"Accept-Language: vi,vi-VN;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5,zh-CN;q=0.4,zh;q=0.3", 
		"Connection: keep-alive", 
		"Host: tplinkwifi.net", 
		"Referer: http://tplinkwifi.net/$pathId/userRpm/WlanNetworkRpm.htm", 
		"Upgrade-Insecure-Requests: 1", 
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36"
	);
	_CURL($URL, null, $cookie, $header);
}

function _loginTPLink($cookie){
	$URL = "http://tplinkwifi.net/userRpm/LoginRpm.htm?Save=Save";
	$htmlResp = _CURL($URL, null, $cookie);
	if(preg_match("#/([A-Z]+)/userRpm#is", $htmlResp, $pathId)){
		return $pathId[1];
	}
	return false;
}

function _buildCookie($username, $password){
	$password = md5($password);
	$auth = "Basic%20".urlencode(base64_encode("$username:$password"));
	$cookie = "Authorization=$auth;path/";
	return $cookie;
}

function _CURL($url, $param=null, $cookie=null, $header=null, $proxy=null, $resp=1, $pathDownload=null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $param==null?"GET":"POST");
	if($param!=null){
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $param); 
	}
	if($cookie!=null){
		curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	}
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($curl, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	if($header!=null && is_array($header)){
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	}
	if($proxy!=null && is_array($header)){
		curl_setopt($curl, CURLOPT_PROXY, $proxy[0]);
		if(count($proxy)>1){
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy[1]);
		}
	}
	curl_setopt($curl, CURLOPT_HEADER, ($resp==null||$resp==0)?true:false);
	curl_setopt($curl, CURLOPT_NOBODY, ($resp==null||$resp==1)?false:true);
	if($pathDownload!=null && !file_exists($pathDownload)){
		$f = @fopen($pathDownload, "w+");
		if($f) curl_setopt($curl, CURLOPT_FILE, $f);
	}	
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($curl, CURLOPT_TIMEOUT, 3);
	$resp = curl_exec($curl);
	curl_close($curl);
	return $resp;
}
?>