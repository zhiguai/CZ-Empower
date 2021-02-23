<?php   
	include './header.php';

    if (empty($_SESSION['username'])) {
        echo "<script>window.location.href=\"login.php?notifications=2&notifications_content=请先验证身份\"</script>";
        exit;
    }

    $result = Execute($conn, 'select * from site where shop="true"'); //获得记录总数
    $result_url = Execute($conn, "select * from url where email = '{$_SESSION['username']}'"); //获得记录总数
?>
<section class="slice py-6 pt-lg-7 pb-lg-8 bg-dark">
    <!-- Container -->
    <div class="container">
        <div class="row row-grid align-items-center">
            <div class="col-lg-6">
                <!-- Heading -->
                <h1 class="h1 text-white text-center text-lg-left my-4">
                    您好！ <strong><?php echo $_SESSION['username']?></strong>
                </h1>
                <!-- Text -->
                <p class="lead text-white text-center text-lg-left opacity-8">
                    以下是该邮箱的数据信息,如果有其他问题,请联系管理员咨询.
                </p>
                <!-- Buttons -->
                <div class="mt-5 text-center text-lg-left">
                    <a href="index.php?submit=logout" data-scroll-to="" class="btn btn-white btn-lg btn-icon">
                        <span class="btn-inner--icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </span>
                        <span class="btn-inner--text">注 销 验 证</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- SVG separator -->
    <div class="shape-container shape-line shape-position-bottom">
        <svg width="2560px" height="100px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 2560 100" style="enable-background:new 0 0 2560 100;" xml:space="preserve" class="">
            <polygon points="2560 0 2560 100 0 100"></polygon>
        </svg>
    </div>
</section>

<section class="slice slice-lg" id="sct-form-contact">
    <div class="container position-relative zindex-100">
 
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6">

                        <div class="pt-5">
                            <div class="mb-5 text-center">
                                <h6 class="h3 mb-1">全部授权</h6>
                                <p class="text-muted mb-0">切勿将自己的授权信息泄露.</p>
                            </div>
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">应用ID</th>
                                        <th scope="col">域名</th>
                                        <th scope="col">授权状态</th>
                                        <th scope="col">到期时间</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php while ($row_url = mysqli_fetch_array($result_url)) { ?>
                                    <tr>
                                        <th scope="row"><?php echo $row_url['id']?></th>
                                        <td><?php echo $row_url['site_id']?></td>
                                        <td><?php echo $row_url['url']?></td>
                                        <td><?php echo $row_url['state']?></td>
                                        <td><?php echo $row_url['expire_time']?></td>
                                    </tr>
<?php } ?>
                                </tbody>
                            </table>
                        </div>       

            </div>  
        </div>   

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Form -->
                <div class="tab-example-result">
                    <div class="card mb-0">
                        <div class="p-5">
                            <div>
                                <div class="mb-5 text-center">
                                    <h6 class="h3 mb-1">申请授权</h6>
                                    <p class="text-muted mb-0">请慎重填写信息，申请后将无法修改.</p>
                                </div>
                                <span class="clearfix"></span>
                                <form action="api.php" method="POST">
                                    <div class="form-group">
                                        <label class="form-control-label">选择授权应用</label>
                                        <select class="custom-select" name="site_id">
                                        <option  value="0" selected>请选择你要查询的站点</option>
<?php while ($row = mysqli_fetch_array($result)) { ?>
                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                    <label class="form-control-label">填写域名</label>
                                        <div class="form-group">
                                            <input name="url" type="text" class="form-control form-control-emphasized" placeholder="www.fatda.cn">
                                        </div>
                                    </div>
                                        <!-- 极验 --> 
                                    <div id="embed-captcha"></div>
                                    <div id="notice" class="hide" role="alert">请先完成验证</div>
                                    <p id="wait" class="show">正在加载验证码......</p>
                                    <div class="mt-4">
                                        <button  id="embed-submit"  type="submit" name="state" value="addurl"class="btn btn-block btn-primary">提 交</button>
                                    </div>
                                
                                </form>
                                
                                <!-- Alternative login -->
                                
                                <!-- Links -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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