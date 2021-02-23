<?php
    define('free_number','5');//自助授权个数
    define('free_day','3');//自助授权开通天数

    @header("Content-type: text/html; charset=utf-8");
    //引入数据库配置
    require_once "../config/sqlConfig.php";
    //引入数据库操作函数库
    require_once "../public/dbInc.php";

    //启动session
    @session_start();

    //数据库连接检测
    $conn = Connect();


    //其余


    //引入基本配置
    require_once "../config/systemConfig.php";

    //ip获取函数
    function GetClientIp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
    //常用变量
    $ip = GetClientIp();
    //判断是否登入
    if (empty($_SESSION['username'])) {
        echo '<script>window.location.href="login.php?notifications=2&notifications_content=请先登录"</script>';
        exit;
    }

   //自助添加站点
   if ($_POST['state'] == 'addurl') {
        $_POST['site_id'] = addslashes($_POST['site_id']);
        $_POST['url'] = addslashes($_POST['url']);
        if (empty($_POST['site_id']) || empty($_POST['url'])) {
            echo "<script>window.location.href=\"shop.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=请勿留空！\"</script>";
            exit;
        }

        $result_url = Execute($conn, "select * from url where email = '{$_SESSION['username']}'"); //获得记录总数
        if (free_number <= mysqli_num_rows($result_url)) {
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=您的申请次数达到上限，请联系管理员\"</script>";
            exit;
        }
        //防止重复
        $sql = Execute($conn, "select * from url where site_id = '{$_POST['site_id']}' and url = '{$_POST['url']}'");//查询数据
        if (mysqli_num_rows($sql) == 1) {
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=该授权信息已存在，请修改后再试\"</script>";
            exit;
        }

        if (mb_strlen($_POST['site_id'], 'UTF8') > 10) {
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=授权应用ID不能超出10个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['site_id'], 'UTF8') < 1) {
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=授权应用ID不得低于1个字符\"</script>";
            exit;
        }

        //该站点是否开启自助授权
        $result = Execute($conn, "select * from site where id='{$_POST['site_id']}'"); //获得记录总数
        $sql = mysqli_fetch_assoc($result);
        if(empty($sql['shop']) || $sql['shop'] == "false"){
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=该站点没有开启自助授权\"</script>";
            exit;
        }

        $search = '/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/';
        if(!preg_match($search,$_POST['url'])){
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=域名格式错误\"</script>";
            exit;
        }
        if (mb_strlen($_POST['url'], 'UTF8') > 50) {
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=域名不能超出50个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['url'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"shop.php?&notifications=2&notifications_content=域名不能低于3个字符\"</script>";
            exit;
        }

        $time = date('Y-m-d h:i:s', time());
        $time1 = date("Y-m-d",strtotime("+".free_day." day"));
        $sql = "INSERT INTO url (site_id,url,email,state,expire_time,time)
        VALUES ('{$_POST['site_id']}','{$_POST['url']}','{$_SESSION['username']}','true','{$time1}','{$time}')";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"shop.php?notifications=1&notifications_content=添加成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"shop.php?notifications=3&notifications_content=系统出错,数据写入失败！\"</script>";
        exit;
    }   
