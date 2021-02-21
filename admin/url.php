<?php
    include './header.php';
    require_once "../public/rsa/rsa.php";//引入加密操作函数库
	$rsa = new Rsa();//初始化加密操作函数库
?>
<!-- 内容标题 -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">站点管理</h4>
        </div>
    </div>
</div>  
<?php
    if (isset($_GET['search'])) {
        //判断是否传入值
        if (empty($_GET['searchcont'])) {
            echo '<script>window.location.href="url.php?notifications=2&notifications_content=请填写搜索内容"</script>';
            exit;
        }

        $searchcont = Escape($conn, $_GET['searchcont']);

        //分页1
        $page_per_number = 8; //设定每页显示个数
        $page_now_page = $_GET['page'];

        $page_sql_whole = Execute($conn, "select * from url where id like binary '%{$searchcont}%' or state like binary '%{$searchcont}%' or email like binary '%{$searchcont}%' or expire_time like binary '%{$searchcont}%' or url like binary '%{$searchcont}%' or time like binary '%{$searchcont}%' or site_id like binary '%{$searchcont}%'"); //获得记录总数
        $page_rs = mysqli_num_rows($page_sql_whole);
            
        $page_totalPage = ceil($page_rs/$page_per_number);
        if (!isset($page_now_page)) {
            $page_now_page = 1;
        }
        
        $page_start_count = ($page_now_page-1)*$page_per_number;

        $page_result =  Execute($conn, "select * from url where id like binary '%{$searchcont}%' or state like binary '%{$searchcont}%' or email like binary '%{$searchcont}%' or expire_time like binary '%{$searchcont}%' or url like binary '%{$searchcont}%' or time like binary '%{$searchcont}%' or site_id like binary '%{$searchcont}%' order by id desc limit {$page_start_count},{$page_per_number}"); 
?>
<!-- 搜索-->
<div class="row">
    <div class="col-xl-12">
        <div class="text-center">
            <form action="url.php" method="GET">
            <div class="input-group col-sm-8  m-auto">
                <input type="text" class="form-control" name="searchcont" placeholder="Search...">
                <div class="input-group-append">
                    <button class="btn btn-primary" name="search" type="submit">搜索</button>
                </div>
            </div>
            </form>
            <br>
            <p class="text-muted w-50 m-auto">
                可搜索应用的ID，应用名，应用介绍，授权状态，更新机制，时间
            </p>
        </div>
    </div>
</div> 
<!-- 搜索-->
<br><br><br>
    <div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3">授权管理：<a href="api.php?state=addurl"><button type="button" class="foot-right btn btn-info btn-rounded">添加授权</button></a></h4>

        <div class="table-responsive">
            <table class="table table-bordered table-centered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>授权ID</th>
                        <th>授权域名</th>
                        <th>联系邮箱</th>
                        <th>站点授权状态</th>
                        <th>到期时间</th>
                        <th>最后修改时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
            <tbody>
<?php
//分页2
while ($page_row = mysqli_fetch_array($page_result)) {
    $url_staet = 'success';
    //判断是否匿名2
    if ($page_row['state'] == 'false') {
        $url_staet = 'warning';
    }
    $time1 = strtotime(date('Y-m-d', time()));//当前时间
    $time2 = strtotime($page_row['expire_time']);//到期时间
    $url_staet1 = 'success';
    if($time1>$time2){
    	$url_staet1 = 'warning';
    }
    //判断是否超过规定显示字数
    if (mb_strlen($page_row['introduce'])>5) {
        $page_row['introduce']=mb_substr($page_row['introduce'], 0, 5, "utf-8").'...';
    } ?>
                    <tr>
                        <td><?php echo $page_row['id']; ?></td>
                        <td><?php echo $page_row['site_id']; ?></td>
                        <td><?php echo $page_row['url']; ?></td>
                        <td><?php echo $page_row['email']; ?></td>
                        <td><span class="badge badge-<?php echo $url_staet; ?>"><?php echo $page_row['state']; ?></span></td>
                        <td><span class="badge badge-<?php echo $url_staet1; ?>"><?php echo $page_row['expire_time']; ?></span></td>
                        <td><?php echo $page_row['time']; ?></td>
                        <td class="table-action">
                            <a href="<?php echo 'url.php?id='.$page_row['id']; ?>&state=edit" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                            <a href="<?php echo 'api.php?id='.$page_row['id']; ?>&state=deleteurl" class="action-icon"> <i class="mdi mdi-delete"></i></a>
                        </td>
                    </tr>
<?php
} ?>
                </tbody>
            </table>
        </div> <!-- end table responsive-->
    </div>
</div>

<?php
    } elseif (!empty($_GET['id']) && $_GET['state'] == "edit") {
        # 修改
        $sql = Execute($conn, "select * from url where id = '{$_GET['id']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"url.php?notifications=2&notifications_content=授权不存在\"</script>";
            exit;
        }
        $url_data = mysqli_fetch_assoc($sql); 
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">修改站点<small class="foot-right">ID:<?php echo $url_data['id']; ?>-Site_ID:<?php echo $url_data['site_id']; ?>-TIME:<?php echo $url_data['time']; ?></small></h4>
                <p class="text-muted">
                    请修改后提交.
                </p>

                <form action="./api.php" method="post">
                <input name="state" style="display: none;" value="editurl">
                <input name="id" style="display: none;" value="<?php echo $url_data['id']; ?>">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">授权应用ID</label>
                            <input type="text" name="site_id" class="form-control" placeholder="chizhiguai" value="<?php echo $url_data['site_id']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">域名</label>
                            <input type="text" name="url" class="form-control" placeholder="chizhiguai" value="<?php echo $url_data['url']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">域名</label>
                            <input type="text" name="email" class="form-control" placeholder="chizhiguai" value="<?php echo $url_data['email']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-date">到期时间:<?php echo $url_data['expire_time']; ?></label>
                            <input class="form-control" id="example-date" type="date" name="expire_time" >
                        </div>
                    </div> <!-- end col -->

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">当前用户Key</label>
                            <textarea rows="6" class="form-control" readonly>
<?php 
	//构造数组
	$data['id'] = $url_data['id'];//从数据库获取
	$data['site_id'] = $url_data['site_id'];//从数据库获取
	$data['name']  = $url_data['url'];//从数据库获取
	$data['email'] = $url_data['email'];//从数据库获取

	echo $privEncrypt = $rsa->privEncrypt(json_encode($data));

?>
                            </textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">授权状态</label>
                            <select name="url_state"  class="form-control">
                                <option value="" selected>请选择</option>
                                <option value="true">开</option>
                                <option value="false">关</option>
                            </select>
                        </div>
                    </div> <!-- end col -->
                </div><br>
                <!-- end row-->
                <button type="submit" class="foot-right btn btn-primary">提交</button>
                </form>
                    
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>
<?php
    } else {
        //正常访问
        //分页1
        $page_per_number = 8; //设定每页显示个数
        $page_now_page = $_GET['page'];

        $page_sql_whole = Execute($conn, 'select * from url'); //获得记录总数
        $page_rs = mysqli_num_rows($page_sql_whole);
        
        $page_totalPage = ceil($page_rs/$page_per_number);
        if (!isset($page_now_page)) {
            $page_now_page = 1;
        }
    
        $page_start_count = ($page_now_page-1)*$page_per_number;

        $page_result =  Execute($conn, "select * from url order by id desc limit {$page_start_count},{$page_per_number}"); 
?>
<!-- 搜索-->
<div class="row">
    <div class="col-xl-12">
        <div class="text-center">
            <form action="url.php" method="GET">
            <div class="input-group col-sm-8  m-auto">
                <input type="text" class="form-control" name="searchcont" placeholder="Search...">
                <div class="input-group-append">
                    <button class="btn btn-primary" name="search" type="submit">搜索</button>
                </div>
            </div>
            </form>
            <br>
            <p class="text-muted w-50 m-auto">
                可搜索应用的ID，应用名，应用介绍，授权状态，更新机制，时间
            </p>
        </div>
    </div>
</div> 
<!-- 搜索-->
    <br><br><br>
    <div class="card">
    <div class="card-body">
        <h4 class="header-title mb-3">授权管理：<a href="api.php?state=addurl"><button type="button" class="foot-right btn btn-info btn-rounded">添加授权</button></a></h4>

        <div class="table-responsive">
            <table class="table table-bordered table-centered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>授权ID</th>
                        <th>授权域名</th>
                        <th>联系邮箱</th>
                        <th>站点授权状态</th>
                        <th>到期时间</th>
                        <th>最后修改时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
            <tbody>
<?php
//分页2
while ($page_row = mysqli_fetch_array($page_result)) {
    $url_staet = 'success';
    //判断是否匿名2
    if ($page_row['state'] == 'false') {
        $url_staet = 'warning';
    }
    $time1 = strtotime(date('Y-m-d', time()));//当前时间
    $time2 = strtotime($page_row['expire_time']);//到期时间
    $url_staet1 = 'success';
    if($time1>$time2){
    	$url_staet1 = 'warning';
    }
    //判断是否超过规定显示字数
    if (mb_strlen($page_row['introduce'])>5) {
        $page_row['introduce']=mb_substr($page_row['introduce'], 0, 5, "utf-8").'...';
    } ?>
                    <tr>
                        <td><?php echo $page_row['id']; ?></td>
                        <td><?php echo $page_row['site_id']; ?></td>
                        <td><?php echo $page_row['url']; ?></td>
                        <td><?php echo $page_row['email']; ?></td>
                        <td><span class="badge badge-<?php echo $url_staet; ?>"><?php echo $page_row['state']; ?></span></td>
                        <td><span class="badge badge-<?php echo $url_staet1; ?>"><?php echo $page_row['expire_time']; ?></span></td>
                        <td><?php echo $page_row['time']; ?></td>
                        <td class="table-action">
                            <a href="<?php echo 'url.php?id='.$page_row['id']; ?>&state=edit" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                            <a href="<?php echo 'api.php?id='.$page_row['id']; ?>&state=deleteurl" class="action-icon"> <i class="mdi mdi-delete"></i></a>
                        </td>
                    </tr>
<?php
} ?>
                </tbody>
            </table>
        </div> <!-- end table responsive-->
    </div>
</div>
<?php
}

if (empty($_GET['id']) || $_GET['state'] !== 'edit') {
?>
<!-- 翻页 --> 
<div class="pagination justify-content-center" >
    <li class="page-item"><a class="page-link" href="?page=1">首页</a></li>

    <?php if (isset($_GET['page']) && $_GET['page'] !== "1") { ?>
        <li class="page-item"><a class="page-link" id="pagebtn-s" href="?page=<?php echo $page_now_page - 1;?>">上页</a></li>
    <?php } ?>

    <li class="page-item"><a class="page-link"><?php echo $page_now_page ?>/<?php echo $page_totalPage ?></a></li>

    <?php if ($page_totalPage > $page_now_page) {?>
        <li class="page-item"><a class="page-link" id="pagebtn-x" href="?page=<?php echo $page_now_page + 1;?>">下页</a></li>
    <?php } ?>

    <li class="page-item"><a class="page-link" href="?page=<?php echo $page_totalPage; ?>">尾页</a></li>

</div>
<br><br>	
<!-- 翻页 -->  
<?php
} include './footer.php';
?>