<?php
    @header("content-type:application/json; charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    //引入数据库配置
    require_once "./config/sqlConfig.php";
    //引入数据库操作函数库
    require_once "./public/dbInc.php";
    //引入加密操作函数库
    require_once "./public/rsa/rsa.php";
    //初始化加密操作函数库
    $rsa = new Rsa();
    //启动session
    @session_start();

    //数据库连接检测
    $conn = Connect();
    //引入基本配置
    require_once "./config/systemConfig.php";
    
    //设定状态
    $date['state'] ="200";
    
    if(empty($_GET['key'])){
        $date['state'] ="500";//传入key
        echo json_encode($date);
        exit();
    }

    $privEncrypt = $_GET['key'];//获取key

    $data = json_decode($rsa->publicDecrypt($privEncrypt),true);//对json格式的字符串进行编码，同时进行数组化

    //判断是否存在该数据
    $sql = Execute($conn, "select * from url where id = '{$data['id']}' and site_id = '{$data['site_id']}' and url = '{$data['url']}' and email ='{$data['email']}'");//查询数据
    if (mysqli_num_rows($sql) !== 1) {
        //没有该授权时
        $date['state'] ="501";
        echo json_encode($date);
        exit;
    }
    //获取该授权数据
    $sql_data = mysqli_fetch_assoc($sql);
    
        //判断该站点是否存在
        $sql1 = Execute($conn, "select * from site where id = '{$data['site_id']}'");//查询数据
        if (mysqli_num_rows($sql1) == 1) {
            //获取该站点数据
            $sql1_data = mysqli_fetch_assoc($sql1);
            $date['site_id'] = $sql1_data['id'];
            $date['site_name'] = $sql1_data['name'];//名字
            $date['site_introduce'] = $sql1_data['introduce'];//介绍
            $date['site_version'] = $sql1_data['version'];//当前最新版本号
            $date['site_state'] = $sql1_data['state'];//锁定状态
            $date['site_time'] = $sql1_data['time'];//更新时间
        }else{
            //没有该站点时
            $date['site_state'] ="501";
            $date['site_id'] = "501";
            $date['site_name'] = "501";
            $date['site_introduce'] = "501";
            $date['site_version'] = "501";
            $date['site_state'] = "false";
            $date['site_time'] = "501";
        }
    
	//判断是否强制更新
	if($date['site_state'] == "true"){
		if($date['site_version']>$data['now_version']){
			$date['state'] ="502";
			echo json_encode($date);
			exit;
		}
	}
        
    //判断该授权状态
    if($sql_data['state'] == 'true'){
        //授权锁开启时
        //计算授权是否到期
        $time1 = strtotime(date('Y-m-d', time()));//当前时间
        $time2 = strtotime($data['now_time']);//到期时间
        if($time1<$time2){
            //授权没有到期
            $date['url_state'] = "1";
            $date['url_time'] = ceil(($time2-$time1)/86400);
        }else{
            $date['url_state'] = "2";
            //授权到期
        }
    }else{
        //授权锁定
        $date['url_state'] = "0";
    }
    echo json_encode($date);
?>