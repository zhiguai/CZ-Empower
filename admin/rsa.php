<?php
    include './header.php';
    //判断当前登录者权限
    if ($admin_data['power'] !== "1") {
        echo "<script>window.location.href=\"index.php?notifications=3&notifications_content=权限不足\"</script>";
        exit;
    }
    $file_path = "../public/rsa/rsa_public_key.pem";
    if(file_exists($file_path)){
        $str = file_get_contents($file_path);//将整个文件内容读入到一个字符串中
        $str = $str;
    }
    $file_path = "../public/rsa/rsa_private_key.pem";
    if(file_exists($file_path)){
        $str1 = file_get_contents($file_path);//将整个文件内容读入到一个字符串中
        $str1 = $str1;
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
                <h4 class="header-title">RSA密钥设置</h4>
                <p class="text-muted">
                    RSA公私钥,可自行生成.不同的公私钥意味key也会不同.
                </p>

                <form action="api.php" method="post">
                <input name="state" style="display: none;" value="rsa">
                <div class="row">
                    <div class="col-lg-6">
                    <div class="form-group mb-3">
                            <label for="example-textarea">公钥</label>
                            <textarea class="form-control" name="public" rows="5"><?php echo $str;?></textarea>
                        </div>
                    </div> <!-- end col -->

                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="example-textarea">私钥</label>
                            <textarea class="form-control" name="private" rows="5"><?php echo $str1;?></textarea>
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