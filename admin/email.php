<?php
    include './header.php';
    //判断当前登录者权限
    if ($admin_data['power'] !== "1") {
        echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
        exit;
    }
    //读取email配置
    require_once "../public/email/config.php";
?>

<!-- 内容标题 -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box">
      <h4 class="page-title">系统管理</h4>
    </div>
  </div>
</div>     
<!-- 内容标题 --> 

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">邮件设置</h4>
                <p class="text-muted">
                    不支持HTML
                </p>

                <form action="api.php" method="post">
                <input name="state" style="display: none;" value="email">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">SMTP服务器</label>
                            <input type="text" name="smtp_server" class="form-control" placeholder="CZCW表白墙" value="<?php echo smtp_username;?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">SMTP端口</label>
                            <input type="text" name="smtp_serverport" class="form-control" placeholder="CZCW表白墙" value="<?php echo smtp_serverport;?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">邮箱</label>
                            <input type="text" name="smtp_usermail" class="form-control" placeholder="CZCW表白墙" value="<?php echo smtp_usermail;?>">
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">账户</label>
                            <input type="text" name="smtp_usermail1" class="form-control" placeholder="CZCW表白墙" value="<?php echo smtp_usermail;?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">密码</label>
                            <input type="password" name="smtp_pass" class="form-control" placeholder="CZCW表白墙" value="<?php echo smtp_pass;?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">发件人名</label>
                            <input type="text" name="smtp_username" class="form-control" placeholder="CZCW表白墙" value="<?php echo smtp_username;?>">
                        </div>
                    </div> <!-- end col -->
                </div>
                <!-- end row-->
                <button type="submit" class="foot-right btn btn-primary">提交</button>
                </form>
                    
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>

<?php include 'footer.php';?>