<?php
/*
 * @Description: 
 * @Autor: 吃纸怪
 * @QQ: 2903074366
 * @Email: 2903074366@qq.com
 * @QQgroup: 801235342
 * @Github: https://github.com/zhiguai
 */
//生成一个key
require_once "rsa.php";
$rsa = new Rsa();

//构造数组
$data['id'] = '39';//从数据库获取
$data['site_id'] = '1';//从数据库获取
$data['name']  = 'host650945.xg.vipfuq.xyz';//从数据库获取
$data['email'] = '2635435377@qq.com';//从数据库获取

$privEncrypt = $rsa->privEncrypt(json_encode($data));
echo '私钥加密后:'.$privEncrypt.'<br>';

$publicDecrypt = $rsa->publicDecrypt($privEncrypt);
echo '公钥解密后:'.$publicDecrypt.'<br>';

$publicEncrypt = $rsa->publicEncrypt(json_encode($data));
echo '公钥加密后:'.$publicEncrypt.'<br>';

$privDecrypt = $rsa->privDecrypt($publicEncrypt);
echo '私钥解密后:'.$privDecrypt.'<br>';