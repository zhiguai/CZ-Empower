<?php   
	include './header.php';
    $result = Execute($conn, 'select * from site'); //获得记录总数
    require_once "../public/rsa/rsa.php";//引入加密操作函数库
	$rsa = new Rsa();//初始化加密操作函数库

?>
    <section class="pt-5 col bg-section-secondary">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="row align-items-center">
                        <div class="col ml-n3 ml-md-n2"><h2 class="mb-0">查询授权</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form action="" method="post">
    <div class="slice col slice-sm bg-section-secondary">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card card-fluid">
                                <div class="card-header">

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <select class="custom-select" name="site_id">
                                                    <option selected>请选择你要查询的站点</option>
<?php while ($row = mysqli_fetch_array($result)) { ?>
                                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input name="email" type="text" class="form-control form-control-emphasized" placeholder="xxx@qq.com">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <input name="site_name" type="text" class="form-control form-control-emphasized" placeholder="www.fatda.cn">
                                            </div>
                                        </div>
                                    </div>
                                </div>
<?php
    if($_POST['state']=="find"){
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
        if (empty($_POST['site_id']) || empty($_POST['site_name']) || empty($_POST['email'])) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=请勿留空！\"</script>";
            exit;
        }
        if (mb_strlen($_POST['site_name'], 'UTF8') > 50) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=域名不能超出50个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['site_name'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=域名不能低于3个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['site_id'], 'UTF8') > 10) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=授权应用ID不能超出10个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['site_id'], 'UTF8') < 1) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=授权应用ID不得低于1个字符\"</script>";
            exit;
        }
        $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
        if(!preg_match($preg_email,$_POST['email'])){
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=邮箱格式错误\"</script>";
            exit;
        }
        if (mb_strlen($_POST['email'], 'UTF8') > 50) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=邮箱不能超出50个字符\"</script>";
            exit;
        }
        if (mb_strlen($_POST['email'], 'UTF8') < 3) {
            echo "<script>window.location.href=\"?&notifications=2&notifications_content=邮箱不能低于3个字符\"</script>";
            exit;
        }
        $sql = Execute($conn, "select * from url where site_id = '{$_POST['site_id']}' and url = '{$_POST['site_name']}' and email = '{$_POST['email']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"?notifications=2&notifications_content=授权不存在\"</script>";
            exit;
        }
        $site_data = mysqli_fetch_assoc($sql);
        $url_staet = 'success';
        if ($site_data['state'] == 'false') {
            $url_staet = 'warning';
        }
        $time1 = strtotime(date('Y-m-d', time()));//当前时间
        $time2 = strtotime($site_data['expire_time']);//到期时间
        $url_staet1 = 'success';
        if($time1>$time2){
            $url_staet1 = 'warning';
        }
?>

<div class="card-body py-5">
    <!-- 极验 --> 
    <div id="embed-captcha"></div>
    <div id="notice" class="hide" role="alert">请先完成验证</div>
    <p id="wait" class="show">正在加载验证码......</p>

    <div class="mt-4">
        <button id="embed-submit" type="submit" name="state" value="find" class="btn btn-block btn-primary">查 询</button>
    </div>
</div>

<div class="card bg-section-dark border-0 rounded-lg" style="max-width: 100%;">
    <div class="card-footer px-5">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center align-items-center mb-3">
                    <div>
                        <div class="icon icon-sm icon-shape bg-warning text-white rounded-circle mr-3">
                            <i data-feather="airplay"></i>
                        </div>
                    </div>
                    <span class="h6 text-white mb-0">您的授权情况</span>
                </div>
                <h5 class="text-white pt-4">授权状态：<span class="badge badge-<?php echo $url_staet; ?>"><?php echo $site_data['state']; ?></span></h5>
                <p class="text-white opacity-8">
                    到期时间：<span class="badge badge-<?php echo $url_staet1; ?>"><?php echo $site_data['expire_time']; ?></span>
                </p>

            </div>
            <div class="col-md-6">
            <textarea rows="6" class="form-control" readonly>
<?php 
	//构造数组
	$data['id'] = $site_data['id'];//从数据库获取
	$data['site_id'] = $site_data['site_id'];//从数据库获取
	$data['name']  = $site_data['url'];//从数据库获取
	$data['email'] = $site_data['email'];//从数据库获取

	echo $privEncrypt = $rsa->privEncrypt(json_encode($data));

?>
            </textarea>
            </div>
            
        </div>
    </div>
</div>
<?php }else{ ?>
                                <div class="card-body py-5">
                                    <!-- 极验 --> 
                                    <div id="embed-captcha"></div>
                                    <div id="notice" class="hide" role="alert">请先完成验证</div>
                                    <p id="wait" class="show">正在加载验证码......</p>
                                
                                    <div class="mt-4">
                                        <button id="embed-submit" type="submit" name="state" value="find" class="btn btn-block btn-primary">查 询</button>
                                    </div>
                                </div>

<?php } ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
    </form>

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