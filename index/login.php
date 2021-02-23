<?php   
	include './header.php';

    if (!empty($_SESSION['username'])) {
        echo "<script>window.location.href=\"index.php?notifications=2&notifications_content=请勿重复验证身份\"</script>";
        exit;
    }

    if($_POST['submit'] == "sendvocde"){

        //极验二次验证
        require_once dirname(dirname(__FILE__)) . '/public/geetest/lib/class.geetestlib.php';
        require_once dirname(dirname(__FILE__)) . '/public/geetest/config/config.php';
        
        $GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
        if ($_SESSION['gtserver'] == 1) {   //服务器正常
            $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode']);
            if ($result) {
            } else {
                echo '<script>window.location.href="?notifications=2&notifications_content=人机验证失败，请重新验证！"</script>';
                exit;
            }
        } else {  //服务器宕机,走failback模式
            if ($GtSdk->fail_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'])) {
            } else {
                echo '<script>window.location.href="?notifications=2&notifications_content=人机验证失败，请重新验证！"</script>';
                exit;
            }
        }

        $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        if(!preg_match($preg_email,$_POST['toemail'])){
            echo "<script>window.location.href=\"login.php?notifications=2&notifications_content=邮箱格式错误\"</script>";
            exit;
        }
        if (mb_strlen($_POST['toemail'], 'UTF8') > 50) {
            echo "<script>window.location.href=\"login.php?notifications=2&notifications_content=邮箱不能超出50个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['toemail'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"login.php?notifications=2&notifications_content=邮箱不能低于3个字符\"</script>";
            exit;
        }
    
        //生成一个随机数
        function make_vcode( $length = 6 ){
            // 密码字符集，可任意添加你需要的字符
            $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
            'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
            't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
            'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
            // 在 $chars 中随机取 $length 个数组元素键名
            $keys = array_rand($chars, $length); 
            $password = '';
            for($i = 0; $i < $length; $i++)
            {
                // 将 $length 个数组元素连接成字符串
                $password .= $chars[$keys[$i]];
            }
            return $password;
        }
        $time = 3*60;                    
        // 使用 setcookie 手动设置 session失效时间          
        $_SESSION['vcode']=strtolower(make_vcode());

        setcookie(session_name(),session_id(),time()+$time.'/');
        $_SESSION['userEmail'] = $_POST['toemail'];//储存邮箱        
        //整理邮件内容
        $emailtittle = "身份验证";
        $emailcontent = "验证码:".$_SESSION['vcode'].",三分钟内有效，请勿将验证码透露给他人哦！";
        
        //读取email配置
        require_once "../public/email/config.php";
        //加载邮件发送函数库
        require_once "../public/email/sendEmail.php";
        //******************** 配置信息 ********************************
        $smtpserver = smtp_server;//SMTP服务器
        $smtpserverport =smtp_serverport;//SMTP服务器端口
        $smtpusermail = smtp_usermail;//SMTP服务器的用户邮箱
        $smtpuser = smtp_usermail;//SMTP服务器的用户帐号，注：部分邮箱只需@前面的用户名
        $smtppass = smtp_pass;//SMTP服务器的授权码
        $mailusername = smtp_username;//发件人名字
        $smtpemailto = $_POST['toemail'];//发送给谁
        $mailtitle = $emailtittle;//邮件主题
        $mailcontent = "<h1>".$emailcontent."</h1>";//邮件内容
        $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
        //************************ 配置信息 ****************************
        $smtp = new Smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug = false;//是否显示发送的调试信息
        $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype,$mailusername);
    
        if($state==""){
            echo "<script>window.location.href=\"login.php?notifications=2&notifications_content=邮件发送失败！邮箱填写有误或联系管理员检查SMTP配置是否有误\"</script>";
            exit;
        }else{
            echo "<script>window.location.href=\"login.php?submit=login&notifications=1&notifications_content=发送成功,请注意查收！\"</script>";
            exit;
        }
    }

    if($_POST['submit'] == 'login'){
        if($_POST['vcode'] == $_SESSION['vcode']){
            $_SESSION['username'] = $_SESSION['userEmail'];
            setcookie(session_name(),session_id(),0);
            unset($_SESSION['vcode']);
            unset($_SESSION['userEmail']);
            echo "<script>window.location.href=\"index.php?&notifications=1&notifications_content=验证成功！\"</script>";
            exit;
        }
        echo "<script>window.location.href=\"login.php?submit=login&notifications=2&notifications_content=验证码错误！\"</script>";
        exit;
    }

    if($_GET['submit'] == 'login'){

?>
<section class="slice slice-lg" id="sct-form-contact">
    <div class="container position-relative zindex-100">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <h3>身份验证</h3>
                <p class="lh-190">请输入验证码，验证身份后继续操作</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Form -->
                <form action="" method="post">
                    <div class="form-group">
                        <input class="form-control form-control-lg" type="text" name="vcode" placeholder="验证码">
                    </div>
                    <div class="text-center">
                        <button id="embed-submit" type="submit" name="submit" value="login" class="btn btn-block btn-primary">验 证</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php
} else {
?>
<section class="slice slice-lg" id="sct-form-contact">
    <div class="container position-relative zindex-100">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <h3>身份验证</h3>
                <p class="lh-190">请输入您的邮箱，验证身份后继续操作</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Form -->
                <form action="" method="post">
                    <div class="form-group">
                        <input class="form-control form-control-lg" type="email" name="toemail" placeholder="email@example.com" required="">
                    </div>
                    <div class="text-center">
                        <!-- 极验 --> 
                        <div id="embed-captcha"></div>
                        <div id="notice" class="hide" role="alert">请先完成验证</div>
                        <p id="wait" class="show">正在加载验证码......</p>
                    
                        <div class="mt-4">
                            <button id="embed-submit" type="submit" name="submit" value="sendvocde" class="btn btn-block btn-primary">验 证</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php
}
?>
<section class="slice slice-lg bg-section-secondary">
    <div class="container text-center">
        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <!-- Title -->
                <h2 class="h1 strong-600">
                    留言
                </h2>
                <!-- Text -->
                <p class="lead text-muted">
                    <?php echo SYSTEM_NOTICE?>
                </p>
            </div>
        </div>
    </div>
</section>
<!-- 引入极验所需 -->
<?php require_once '../public/geetest/geetest.php';?>
<?php include './footer.php';?>