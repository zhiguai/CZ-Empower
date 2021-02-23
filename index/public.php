<?php
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

    // 报告 E_NOTICE 之外的所有错误
    error_reporting(E_ALL & ~E_NOTICE);
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

    if ($_GET['submit'] == "logout") {
        unset($_SESSION['username']);
        echo "<script>window.location.href=\"index.php?submit=login&notifications=1&notifications_content=身份注销成功\"</script>";
        exit;
    }

    //常用变量
    $ip = GetClientIp();
