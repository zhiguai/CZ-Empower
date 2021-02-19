<?php
    @header("content-type:application/json; charset=utf-8");
    @header('Access-Control-Allow-Origin: *');
	
    require_once "./config/sqlConfig.php";//引入数据库配置
    require_once "./public/dbInc.php";//引入数据库操作函数库
    require_once "./public/rsa/rsa.php";//引入加密操作函数库
    require_once "./config/systemConfig.php";//引入基本配置	
	
    @session_start();//启动session	
	
    $rsa = new Rsa();//初始化加密操作函数库
    $conn = Connect();//数据库连接并检测

    
	/*
	参数
	|---GET接收
	|-------key
	|-------naem 携带域名	
	|-------version 携带版本号
	|---POST接收
	|-------无
	|---json输出
	|-------state
	|-----------200 正常
	|-----------300 KEY与域名不匹配
	|-----------500 参数key,url缺少
	|-----------501 没有这条授权数据
	|-----------502 该站点开启强制更新
	|-------site_id
	|-----------501 没有这条站点数据
	|-------site_name
	|-----------501 没有这条站点数据
	|-------site_version
	|-----------501 没有这条站点数据
	|-------site_state
	|-----------true 站点开启强制更新
	|-----------false 站点关闭强制更新
	|-------site_switch
	|-----------true 站点开启授权
	|-----------false 站点关闭授权
	|-------site_time
	|-----------501 没有这条站点数据
	|-------url_time
	|-------url_state
	|-----------0 url表state为true时 站点被锁定
	|-----------1 正常
	|-----------2 站点授权已到期
	变量
	|---$receive_data 接收
	|---$output_data 输出
	参数
	|---key
	|-------id	数据库存储的url表的id
	|-------site_id 数据库存储的url表的ip
	|-------name 数据库存储的url表的url
	|-------email 数据库存储的url表的email
	*/
	
    $output_data['state'] ="200";//设定初始状态"200"
	
	//判断key 和 url是否传入
    if(empty($_GET['key']) || empty($_GET['name']) || empty($_GET['version'])){
        $output_data['state'] ="500";//设定参数state输出值500
    }else{
		$privEncrypt = $_GET['key'];//解密KEY
		$receive_data = json_decode($rsa->publicDecrypt($privEncrypt),true);//对json格式的字符串进行编码，同时进行数组化

		//判断该域名是否与key域名匹配
		if($receive_data['name'] == $_GET['name']){
			//匹配时
			$receive_data['name'] = $_GET['name'];
			
			$sql = Execute($conn, "select * from site where id = '{$receive_data['site_id']}'");//构造查询表site数据命令
			//判断该站点数据是否存在
			if (mysqli_num_rows($sql) == 1) {
				//该站点数据存在时
				$sql_data = mysqli_fetch_assoc($sql);//获取该站点数据
				//设定json返回参数值
				$output_data['site_id'] = $sql_data['id'];
				$output_data['site_name'] = $sql_data['name'];
				$output_data['site_introduce'] = $sql_data['introduce'];
				$output_data['site_version'] = $sql_data['version'];
				$output_data['site_state'] = $sql_data['state'];
				$output_data['site_switch'] = $sql_data['switch'];
				$output_data['site_time'] = $sql_data['time'];
				//判断该站点是否开启授权
				if($sql_data['switch'] == 'true'){
					//开启时
					$sql = Execute($conn, "select * from url where id = '{$receive_data['id']}' and site_id = '{$receive_data['site_id']}' and url = '{$receive_data['name']}' and email ='{$receive_data['email']}'");//构造查询表url数据命令

					//判断是否存在该授权数据
					if (mysqli_num_rows($sql) !== 1) {
						//没有该授权数据时
						$output_data['state'] ="501";//设定状态为"501"
					}else{
						//有该授权数据时
						$sql_data = mysqli_fetch_assoc($sql);//获取该站点数据
						//判断该授权状态是否锁定
						if($sql_data['state'] == 'true'){
							//锁定开启时
							//计算授权是否到期
							$time1 = strtotime(date('Y-m-d', time()));//当前时间
							$time2 = strtotime($sql_data['expire_time']);//到期时间
							if($time1<$time2){
								//授权没有到期
								$output_data['url_state'] = "1";
								$output_data['url_time'] = ceil(($time2-$time1)/86400);
							}else{
								//授权到期
								$output_data['url_state'] = "2";
							}
						}else{
							//授权锁定
							$output_data['url_state'] = "0";
						}
					}
				}else{
				//不开启时
				$output_data['url_state'] = "1";
				$output_data['url_time'] = "999";
				}
				//判断该站点手机否开启强制更新
				if($output_data['site_state'] == 'true'){
					//比较当前版本和key的版本
					if($output_data['site_version']>$_GET['version']){
						$output_data['state'] ="502";//设定参数state输出值502
					}
				}
			}else{
				//该站点数据不存在时
				$output_data['site_state'] ="200";//设定参数state输出值200
				//设定json返回参数值
				$output_data['site_id'] = "501";
				$output_data['site_name'] = "501";
				$output_data['site_introduce'] = "501";
				$output_data['site_version'] = "501";
				$output_data['site_state'] = "false";
				$output_data['site_switch'] = "false";
				$output_data['site_time'] = "501";
				$output_data['url_state'] = "1";
				$output_data['url_time'] = "999";
			}
		}else{
		//不匹配时
		$output_data['state'] ="300";//设定参数state输出值300
		}
	}
	
	//输出JSON
	echo json_encode($output_data);
?>