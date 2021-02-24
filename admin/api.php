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
    if (empty($_SESSION['id'])) {
        echo '<script>window.location.href="login.php?notifications=2&notifications_content=请先登录"</script>';
        exit;
    }
    
    //获取管理员信息数据
    $sql = Execute($conn, "select * from user where id = '{$_SESSION['id']}'");//查询数据
    $admin_data = mysqli_fetch_assoc($sql);

    //删除站点
    if ($_GET['state'] == 'deleteurl' && !empty($_GET['id'])) {

        $sql = Execute($conn, "select * from url where id = '{$_GET['id']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"url.php?&notifications=2&notifications_content=该站点不存在\"</script>";
            exit;
        }

        $sql = "delete from url where id='{$_GET['id']}'";
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"url.php?notifications=1&notifications_content=删除成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"url.php?&notifications=3&notifications_content=系统出错,数据删除失败！\"</script>";
        exit;
    }

    //修改站点
    if ($_POST['state'] == 'editurl' && !empty($_POST['id'])) {

        if (empty($_POST['site_id']) || empty($_POST['url']) || empty($_POST['email']) || empty($_POST['url_state']) || empty($_POST['expire_time'])) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=请勿留空！\"</script>";
            exit;
        }

        //防止重复
        $sql = Execute($conn, "select * from url where site_id = '{$_POST['site_id']}' and url = '{$_POST['url']}' and email = '{$_POST['email']}'");//查询数据
        if (mysqli_num_rows($sql) > 1) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=该授权信息已存在，请修改后再试\"</script>";
            exit;
        }

        if (mb_strlen($_POST['site_id'], 'UTF8') > 10) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=授权应用ID不能超出10个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['site_id'], 'UTF8') < 1) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=授权应用ID不得低于1个字符\"</script>";
            exit;
        }
        
        if (mb_strlen($_POST['url'], 'UTF8') > 50) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=域名不能超出50个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['url'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=域名不能低于3个字符\"</script>";
            exit;
        }
        $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        if(!preg_match($preg_email,$_POST['email'])){
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=邮箱格式错误\"</script>";
            exit;
        }
        if (mb_strlen($_POST['email'], 'UTF8') > 50) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=邮箱不能超出50个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['email'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=邮箱不能低于3个字符\"</script>";
            exit;
        }
        if ($_POST['url_state'] !== "true" && $_POST['url_state'] !== "false") {
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=url_state参数无效\"</script>";
            exit;
        }

        $preg_time="/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/";;
        if(!preg_match($preg_time,$_POST['expire_time'])){
            exit;
            echo "<script>window.location.href=\"url.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=expire_time参数无效\"</script>";
            exit;
        }

        $time = date('Y-m-d h:i:s', time());
        $sql = "update url set site_id = '{$_POST['site_id']}' ,email = '{$_POST['email']}' ,state = '{$_POST['url_state']}' , url = '{$_POST['url']}' , expire_time = '{$_POST['expire_time']}', time = '$time' where id = '{$_POST['id']}'";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"url.php?notifications=1&notifications_content=修改成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"url.php?notifications=3&notifications_content=系统出错,数据写入失败！\"</script>";
        exit;
    }
    //添加站点
    if ($_GET['state'] == 'addurl') {

        $state = "true";
        $time = date('Y-m-d h:i:s', time());
        $sql = "INSERT INTO url (site_id,url,email,state,expire_time,time)
        VALUES ('0','fatda.cn','xxx@qq.com','$state','$time','$time')";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"url.php?notifications=1&notifications_content=添加成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"url.php?notifications=3&notifications_content=系统出错,数据写入失败！\"</script>";
        exit;
    }

    //删除授权
    if ($_GET['state'] == 'deletesite' && !empty($_GET['id'])) {

        $sql = Execute($conn, "select * from site where id = '{$_GET['id']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"site.php?&notifications=2&notifications_content=该用户不存在\"</script>";
            exit;
        }

        $sql = "delete from site where id='{$_GET['id']}'";
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"site.php?notifications=1&notifications_content=删除成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"site.php?&notifications=3&notifications_content=系统出错,数据删除失败！\"</script>";
        exit;
    }
    
    //添加授权
    if ($_GET['state'] == 'addsite') {

        $name = "新建".substr(md5(time()), 0, 15);
        $version = "1.0.0";
        $state = "true";
        $time = date('Y-m-d h:i:s', time());
        $sql = "INSERT INTO site (name,introduce,version,state,shop,switch,time)
        VALUES ('$name','$name','$version','$state','false','$state','$time')";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"site.php?notifications=1&notifications_content=添加成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"site.php?notifications=3&notifications_content=系统出错,数据写入失败！\"</script>";
        exit;
    }

    //修改授权
    if ($_POST['state'] == 'editsite' && !empty($_POST['id'])) {
        
        if (empty($_POST['name']) || empty($_POST['introduce']) || empty($_POST['site_state']) || empty($_POST['version']) || empty($_POST['switch']) || empty($_POST['shop'])) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=请勿留空！\"</script>";
            exit;
        }

        if (mb_strlen($_POST['name'], 'UTF8') > 24) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=应用名不能超出24个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['name'], 'UTF8') < 6) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=应用名不得低于6个字符\"</script>";
            exit;
        }
        
        if (mb_strlen($_POST['introduce'], 'UTF8') > 255) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=介绍不能超出225个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['introduce'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=介绍不能低于3个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['version'], 'UTF8') > 7) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=版本号不能超出7个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['version'], 'UTF8') < 1) {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=版本号不能低于1个字符\"</script>";
            exit;
        }
        if ($_POST['site_state'] !== "true" && $_POST['site_state'] !== "false") {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=site_state参数无效\"</script>";
            exit;
        }
        if ($_POST['switch'] !== "true" && $_POST['switch'] !== "false") {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=site_switch参数无效\"</script>";
            exit;
        }
        if ($_POST['shop'] !== "true" && $_POST['shop'] !== "false") {
            echo "<script>window.location.href=\"site.php?state=edit&id={$_POST['id']}&notifications=2&notifications_content=site_switch参数无效\"</script>";
            exit;
        }

        $time = date('Y-m-d h:i:s', time());  
        $sql = "update site set name = '{$_POST['name']}' ,shop = '{$_POST['shop']}' ,introduce = '{$_POST['introduce']}' , version = '{$_POST['version']}' , state = '{$_POST['site_state']}' , switch = '{$_POST['switch']}', time = '$time' where id = '{$_POST['id']}'";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"site.php?notifications=1&notifications_content=修改成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"site.php?notifications=3&notifications_content=系统出错,数据写入失败！\"</script>";
        exit;
    }

    //系统修改
    if ($_POST['state'] == 'system') {
        //判断当前登录者权限
        if ($admin_data['power'] !== "1") {
            echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
            exit;
        }

        if ($_POST['tittle'] == SYSTEM_TITTLE && $_POST['keyworld'] == SYSTEM_KEYWORDS && $_POST['description'] == SYSTEM_DESCRIPTION && $_POST['notice'] == SYSTEM_NOTICE && $_POST['notice1'] == SYSTEM_NOTICE1 && $_POST['copyright'] == SYSTEM_COPYRIGHT && $_POST['friend'] == SYSTEM_FRIENDS) {
            echo '<script>window.location.href="system.php?notifications=2&notifications_content=请修改后再提交"</script>';
            exit;
        }

        $filename='../config/systemConfig.php';
        $str_file=file_get_contents($filename);
        $pattern="/'SYSTEM_TITTLE',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['tittle']=addslashes($_POST['tittle']);
            $str_file=preg_replace($pattern, "'SYSTEM_TITTLE','{$_POST['tittle']}')", $str_file);
        }
        $pattern="/'SYSTEM_KEYWORDS',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['keywords']=addslashes($_POST['keywords']);
            $str_file=preg_replace($pattern, "'SYSTEM_KEYWORDS','{$_POST['keywords']}')", $str_file);
        }
        $pattern="/'SYSTEM_DESCRIPTION',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['description']=addslashes($_POST['description']);
            $str_file=preg_replace($pattern, "'SYSTEM_DESCRIPTION','{$_POST['description']}')", $str_file);
        }
        $pattern="/'SYSTEM_NOTICE',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['notice']=addslashes($_POST['notice']);
            $str_file=preg_replace($pattern, "'SYSTEM_NOTICE','{$_POST['notice']}')", $str_file);
        }
        $pattern="/'SYSTEM_NOTICE1',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['notice1']=addslashes($_POST['notice1']);
            $str_file=preg_replace($pattern, "'SYSTEM_NOTICE1','{$_POST['notice1']}')", $str_file);
        }
        $pattern="/'SYSTEM_COPYRIGHT',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['copyright']=addslashes($_POST['copyright']);
            $str_file=preg_replace($pattern, "'SYSTEM_COPYRIGHT','{$_POST['copyright']}')", $str_file);
        }
        $pattern="/'SYSTEM_FRIENDS',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['friend']=addslashes($_POST['friend']);
            $str_file=preg_replace($pattern, "'SYSTEM_FRIENDS','{$_POST['friend']}')", $str_file);
        }
        if (!file_put_contents($filename, $str_file)) {
            echo '<script>window.location.href="system.php?notifications=2&notifications_content=修改失败，请检查权限！"</script>';
            exit;
        }
        echo '<script>window.location.href="system.php?notifications=1&notifications_content=修改成功"</script>';
        exit;
    }

    //邮件修改
    if ($_POST['state'] == 'email') {
        //判断当前登录者权限
        if ($admin_data['power'] !== "1") {
            echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
            exit;
        }
        //读取email配置
        require_once "../public/email/config.php";
        if ($_POST['smtp_server'] == smtp_server && $_POST['smtp_serverport'] == smtp_serverport && $_POST['smtp_usermail'] == smtp_usermail && $_POST['smtp_usermail1'] == smtp_usermail1 && $_POST['copyright'] == smtp_pass && $_POST['friend'] == smtp_username) {
            echo '<script>window.location.href="system.php?notifications=2&notifications_content=请修改后再提交"</script>';
            exit;
        }

        $filename='../public/email/config.php';
        $str_file=file_get_contents($filename);
        $pattern="/'smtp_server',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['smtp_server']=addslashes($_POST['smtp_server']);
            $str_file=preg_replace($pattern, "'smtp_server','{$_POST['smtp_server']}')", $str_file);
        }
        $pattern="/'smtp_serverport',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['smtp_serverport']=addslashes($_POST['smtp_serverport']);
            $str_file=preg_replace($pattern, "'smtp_serverport','{$_POST['smtp_serverport']}')", $str_file);
        }
        $pattern="/'smtp_usermail',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['smtp_usermail']=addslashes($_POST['smtp_usermail']);
            $str_file=preg_replace($pattern, "'smtp_usermail','{$_POST['smtp_usermail']}')", $str_file);
        }
        $pattern="/'smtp_usermail1',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['smtp_usermail1']=addslashes($_POST['smtp_usermail1']);
            $str_file=preg_replace($pattern, "'smtp_usermail1','{$_POST['smtp_usermail1']}')", $str_file);
        }
        $pattern="/'smtp_pass',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['smtp_pass']=$_POST['smtp_pass'];
            $str_file=preg_replace($pattern, "'smtp_pass','{$_POST['smtp_pass']}')", $str_file);
        }
        $pattern="/'smtp_username',.*?\)/";
        if (preg_match($pattern, $str_file)) {
            $_POST['smtp_username']=$_POST['smtp_username'];
            $str_file=preg_replace($pattern, "'smtp_username','{$_POST['smtp_username']}')", $str_file);
        }
        if (!file_put_contents($filename, $str_file)) {
            echo '<script>window.location.href="email.php?notifications=2&notifications_content=修改失败，请检查权限！"</script>';
            exit;
        }
        echo '<script>window.location.href="email.php?notifications=1&notifications_content=修改成功"</script>';
        exit;
    }

    //RSA密钥修改
    if ($_POST['state'] == 'rsa') {
        //判断当前登录者权限
        if ($admin_data['power'] !== "1") {
            echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
            exit;
        }

        if (empty($_POST['public']) || empty($_POST['private'])) {
            echo '<script>window.location.href="rsa.php?notifications=2&notifications_content=请勿留空"</script>';
            exit;
        }
        $file_path1 = "../public/rsa/rsa_public_key.pem";
        $file_path2 = "../public/rsa/rsa_private_key.pem";
        $filename1 = $_POST['public'];
        $filename2 = $_POST['private'];
        if (!file_put_contents($file_path1,$filename1) || !file_put_contents($file_path2,$filename2) ) {
            exit;
            echo '<script>window.location.href="rsa.php?notifications=2&notifications_content=修改失败，请检查权限！"</script>';
            exit;
        }
        echo '<script>window.location.href="rsa.php?notifications=1&notifications_content=修改成功"</script>';
        exit;
    }
    //账号添加
    if ($_POST['state'] == 'adduser') {
        //判断当前登录者权限
        if ($admin_data['power'] !== "1") {
            echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
            exit;
        }

        if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['power'])) {
            echo '<script>window.location.href="user.php?state=adduser&notifications=2&notifications_content=请勿留空！"</script>';
            exit;
        }
        if (mb_strlen($_POST['username'], 'UTF8') > 24) {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=用户名名不能超出24个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['username'], 'UTF8') < 6) {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=用户名不得低于6个字符\"</script>";
            exit;
        }

        $query="select * from user where username='{$_POST['username']}'";
        $result=Execute($conn, $query);
        if (mysqli_num_rows($result)) {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=用户名已存在请更换后再试\"</script>";
            exit;
        }
        
        if (mb_strlen($_POST['password'], 'UTF8') > 24) {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=密码不能超出24个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['password'], 'UTF8') < 6) {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=密码不能低于6个字符\"</script>";
            exit;
        }
        if ($_POST['power'] !== "1" && $_POST['power'] !== "2") {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=power参数无效\"</script>";
            exit;
        }

        $_POST['password'] = md5($_POST['password']);
        $sql = "INSERT INTO user (username,password,power)
        VALUES ('{$_POST['username']}','{$_POST['password']}','{$_POST['power']}')";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"user.php?notifications=1&notifications_content=添加成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"user.php?state=adduser&notifications=3&notifications_content=系统出错,数据写入失败！\"</script>";
        exit;
    }

    //账号删除
    if ($_GET['state'] == 'deleteuser' && !empty($_GET['id'])) {
        //判断当前登录者权限
        if ($admin_data['power'] !== "1") {
            echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
            exit;
        }

        $sql = Execute($conn, "select * from user where id = '{$_GET['id']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"user.php?state=adduser&notifications=2&notifications_content=该用户不存在\"</script>";
            exit;
        }

        $sql = "delete from user where id='{$_GET['id']}'";
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"user.php?notifications=1&notifications_content=删除成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"user.php?notifications=3&notifications_content=系统出错,数据删除失败！\"</script>";
        exit;
    }

    //修改账号
    if ($_POST['state'] == 'edituser' && !empty($_POST['id'])) {
        //判断当前登录者权限
        if ($admin_data['power'] !== "1") {
            echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
            exit;
        }

        $sql = Execute($conn, "select * from user where id = '{$_POST['id']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=该用户不存在\"</script>";
            exit;
        }
        if (empty($_POST['username']) || empty($_POST['power'])) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=密码除外请勿留空！\"</script>";
            exit;
        }
        if ($_POST['power'] !== "1" && $_POST['power'] !== "2") {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=power参数无效\"</script>";
            exit;
        }
        
        $sql = Execute($conn, "select * from user where id = '{$_POST['id']}'");//查询数据
        $user_data = mysqli_fetch_assoc($sql);
        if ($_POST['username'] == $user_data['username']) {
            //帐号不修改
            //判断密码是否修改
            if (empty($_POST['password'])) {
                //不修改时
                $sql = "update user set power = {$_POST['power']} where id='{$_POST['id']}'";
                
                if (Execute($conn, $sql)) {
                    echo "<script>window.location.href=\"user.php?notifications=1&notifications_content=修改成功\"</script>";
                    exit;
                }
                echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=3&notifications_content=系统出错,数据修改失败！\"</script>";
                exit;
            }
            //修改时
            if (mb_strlen($_POST['password'], 'UTF8') > 24) {
                echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=密码不能超出24个字符\"</script>";
                exit;
            }
            if (mb_strlen($_POST['password'], 'UTF8') < 6) {
                echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=密码不能低于6个字符\"</script>";
                exit;
            }
            $_POST['password'] = md5($_POST['password']);
            $sql = "update user set password = '{$_POST['password']}',power = {$_POST['power']} where id='{$_POST['id']}'";
            
            if (Execute($conn, $sql)) {
                echo "<script>window.location.href=\"user.php?notifications=1&notifications_content=修改成功\"</script>";
                exit;
            }
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=3&notifications_content=系统出错,数据修改失败！\"</script>";
            exit;
        }

        //账号修改时
        if (mb_strlen($_POST['username'], 'UTF8') > 24) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=用户名名不能超出24个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['username'], 'UTF8') < 6) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=用户名不得低于6个字符\"</script>";
            exit;
        }
        //判断账号是否存在
        $query="select * from user where username='{$_POST['username']}'";
        $result=Execute($conn, $query);
        if (mysqli_num_rows($result)) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=用户名已存在请更换后再试\"</script>";
            exit;
        }

        //判断密码是否修改
        if (empty($_POST['password'])) {
            //不修改时
            $sql = "update user set username = '{$_POST['username']}',power = {$_POST['power']} where id='{$_POST['id']}'";
            
            if (Execute($conn, $sql)) {
                echo "<script>window.location.href=\"user.php?notifications=1&notifications_content=修改成功\"</script>";
                exit;
            }
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=3&notifications_content=系统出错,数据修改失败！\"</script>";
            exit;
        }
        //修改时
        if (mb_strlen($_POST['password'], 'UTF8') > 24) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=密码不能超出24个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['password'], 'UTF8') < 6) {
            echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=2&notifications_content=密码不能低于6个字符\"</script>";
            exit;
        }
        $_POST['password'] = md5($_POST['password']);
        $sql = "update user set username = '{$_POST['username']}',password = '{$_POST['password']}',power = {$_POST['power']} where id='{$_POST['id']}'";
        
        if (Execute($conn, $sql)) {
            echo "<script>window.location.href=\"user.php?notifications=1&notifications_content=修改成功\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"user.php?state=edituser&id={$_POST['id']}&notifications=3&notifications_content=系统出错,数据修改失败！\"</script>";
        exit;
    }


echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=参数传递出错！\"</script>";
exit;
