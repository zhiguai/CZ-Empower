<?php
    include './header.php';
    //判断当前登录者权限
    if ($admin_data['power'] !== "1") {
        echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
        exit;
    }
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
                <h4 class="header-title">基本设置</h4>
                <p class="text-muted">
                    版权,友情链接内均可插入HTML代码.
                </p>

                <form action="api.php" method="post">
                <input name="state" style="display: none;" value="system">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">站点名</label>
                            <input type="text" name="tittle" class="form-control" placeholder="CZCW表白墙" value="<?php echo SYSTEM_TITTLE;?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">站点关键词</label>
                            <input type="text" name="keywords" class="form-control" placeholder="CZCW,表白墙,吃纸怪" value="<?php echo SYSTEM_KEYWORDS;?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">站点描述</label>
                            <input type="text" name="description" class="form-control" placeholder="CZCW表白墙一个专业的表白墙！" value="<?php echo SYSTEM_DESCRIPTION;?>">
                        </div>

                        <div class="form-group mb-3">
                            <label for="example-textarea">全站留言条</label>
                            <textarea class="form-control" name="notice" rows="5" placeholder="欢迎来到CZCW表白墙！"><?php echo SYSTEM_NOTICE;?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-textarea">授权管理页留言条</label>
                            <textarea class="form-control" name="notice1" rows="5" placeholder="欢迎来到CZCW表白墙！"><?php echo SYSTEM_NOTICE1;?></textarea>
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="example-textarea">版权</label>
                            <textarea class="form-control" name="copyright" rows="5" placeholder="CZCW表白墙 by <a href='chizg.cn'>吃纸怪</a>"><?php echo stripslashes(SYSTEM_COPYRIGHT);?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-textarea">友情链接</label>
                            <textarea class="form-control" name="friend" rows="5" placeholder="<a href='chizg.cn'>吃纸怪</a>"><?php echo stripslashes(SYSTEM_FRIENDS);?></textarea>
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