<?php
    include './header.php';
?>
<!-- 内容标题 -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">授权管理</h4>
        </div>
    </div>
</div>  
<?php
    if (isset($_GET['search'])) {
        //判断是否传入值
        if (empty($_GET['searchcont'])) {
            echo '<script>window.location.href="site.php?notifications=2&notifications_content=请填写搜索内容"</script>';
            exit;
        }

        $searchcont = Escape($conn, $_GET['searchcont']);

        //分页1
        $page_per_number = 8; //设定每页显示个数
        $page_now_page = $_GET['page'];

        $page_sql_whole = Execute($conn, "select * from site where id like binary '%{$searchcont}%' or switch like binary '%{$searchcont}%' or introduce like binary '%{$searchcont}%' or version like binary '%{$searchcont}%' or name like binary '%{$searchcont}%' or time like binary '%{$searchcont}%' or state like binary '%{$searchcont}%'"); //获得记录总数
        $page_rs = mysqli_num_rows($page_sql_whole);
            
        $page_totalPage = ceil($page_rs/$page_per_number);
        if (!isset($page_now_page)) {
            $page_now_page = 1;
        }
        
        $page_start_count = ($page_now_page-1)*$page_per_number;

        $page_result =  Execute($conn, "select * from site where id like binary '%{$searchcont}%' or switch like binary '%{$searchcont}%' or introduce like binary '%{$searchcont}%' or version like binary '%{$searchcont}%' or name like binary '%{$searchcont}%' or time like binary '%{$searchcont}%' or state like binary '%{$searchcont}%' order by id desc limit {$page_start_count},{$page_per_number}"); 
?>
<!-- 搜索-->
<div class="row">
    <div class="col-xl-12">
        <div class="text-center">
            <form action="site.php" method="GET">
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
        <h4 class="header-title mb-3">授权管理：<a href="api.php?state=addsite"><button type="button" class="foot-right btn btn-info btn-rounded">添加授权</button></a></h4>

        <div class="table-responsive">
            <table class="table table-bordered table-centered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>应用名</th>
                        <th>应用介绍</th>
                        <th>当前版本</th>
                        <th>授权状态</th>
                        <th>自助授权</th>
                        <th>强制更新状态</th>
                        <th>最后修改时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
            <tbody>
<?php
//分页2
while ($page_row = mysqli_fetch_array($page_result)) {
    $site_staet = 'success';
    //判断是否匿名2
    if ($page_row['switch'] == 'false') {
        $site_staet = 'warning';
    }
    //判断是否超过规定显示字数
    if (mb_strlen($page_row['introduce'])>5) {
        $page_row['introduce']=mb_substr($page_row['introduce'], 0, 5, "utf-8").'...';
    } ?>
                    <tr>
                        <td><?php echo $page_row['id']; ?></td>
                        <td><?php echo $page_row['name']; ?></td>
                        <td><?php echo $page_row['introduce']; ?></td>
                        <td><?php echo $page_row['version']; ?></td>
                        <td><span class="badge badge-<?php echo $site_staet; ?>"><?php echo $page_row['switch']; ?></span></td>
                        <td><?php echo $page_row['shop']; ?></td>
                        <td><?php echo $page_row['state']; ?></td>
                        <td><?php echo $page_row['time']; ?></td>
                        <td class="table-action">
                            <a href="<?php echo 'site.php?id='.$page_row['id']; ?>&state=edit" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                            <a href="<?php echo 'api.php?id='.$page_row['id']; ?>&state=deletesite" class="action-icon"> <i class="mdi mdi-delete"></i></a>
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
        $sql = Execute($conn, "select * from site where id = '{$_GET['id']}'");//查询数据
        if (mysqli_num_rows($sql) !== 1) {
            echo "<script>window.location.href=\"site.php?notifications=2&notifications_content=授权不存在\"</script>";
            exit;
        }
        $site_data = mysqli_fetch_assoc($sql); 

        $state_state1 = "开";
        $state_state = "true";
        $switch_state1 = "开";
        $switch_state = "true";
        $shop_state1 = "开";
        $shop_state = "true";
        if($site_data['state']  == "false"){
            $state_state1 = "关";
            $state_state = "false";
        }
        if($site_data['switch']  == "false"){
            $switch_state1 = "关";
            $switch_state = "false";
        }
        if($site_data['shop']  == "false"){
            $shop_state1 = "关";
            $shop_state = "false";
        }
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">修改授权<small class="foot-right">ID:<?php echo $site_data['id']; ?>-TIME:<?php echo $site_data['time']; ?></small></h4>
                <p class="text-muted">
                    请修改后提交.
                </p>

                <form action="./api.php" method="post">
                <input name="state" style="display: none;" value="editsite">
                <input name="id" style="display: none;" value="<?php echo $site_data['id']; ?>">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">应用名称</label>
                            <input type="text" name="name" class="form-control" placeholder="chizhiguai" value="<?php echo $site_data['name']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">当前版本</label>
                            <input type="text" name="version" class="form-control" placeholder="chizhiguai" value="<?php echo $site_data['version']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">强制更新机制</label>
                            <select name="site_state"  class="form-control">
                                <option value="<?php echo $state_state; ?>" selected><?php echo $state_state1; ?></option>
                                <option value="true">开</option>
                                <option value="false">关</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">自助授权</label>
                            <select name="shop"  class="form-control">
                                <option value="<?php echo $shop_state; ?>" selected><?php echo $shop_state1; ?></option>
                                <option value="true">开</option>
                                <option value="false">关</option>
                            </select>
                        </div>
                    </div> <!-- end col -->

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="simpleinput">应用介绍</label>
                            <textarea name="introduce" rows="6" class="form-control" maxlength="240" placeholder="必填"><?php echo $site_data['introduce']; ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="simpleinput">授权状态</label>
                            <select name="switch"  class="form-control">
                                <option value="<?php echo $switch_state; ?>" selected><?php echo $switch_state1; ?></option>
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

        $page_sql_whole = Execute($conn, 'select * from site'); //获得记录总数
        $page_rs = mysqli_num_rows($page_sql_whole);
        
        $page_totalPage = ceil($page_rs/$page_per_number);
        if (!isset($page_now_page)) {
            $page_now_page = 1;
        }
    
        $page_start_count = ($page_now_page-1)*$page_per_number;

        $page_result =  Execute($conn, "select * from site order by id desc limit {$page_start_count},{$page_per_number}"); 
?>
<!-- 搜索-->
<div class="row">
    <div class="col-xl-12">
        <div class="text-center">
            <form action="site.php" method="GET">
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
        <h4 class="header-title mb-3">授权管理：<a href="api.php?state=addsite"><button type="button" class="foot-right btn btn-info btn-rounded">添加授权</button></a></h4>

        <div class="table-responsive">
            <table class="table table-bordered table-centered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>应用名</th>
                        <th>应用介绍</th>
                        <th>当前版本</th>
                        <th>授权状态</th>
                        <th>自助授权</th>
                        <th>强制更新状态</th>
                        <th>最后修改时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
            <tbody>
<?php
//分页2
while ($page_row = mysqli_fetch_array($page_result)) {
    $site_staet = 'success';
    //判断是否匿名2
    if ($page_row['switch'] == 'false') {
        $site_staet = 'warning';
    }
    //判断是否超过规定显示字数
    if (mb_strlen($page_row['introduce'])>5) {
        $page_row['introduce']=mb_substr($page_row['introduce'], 0, 5, "utf-8").'...';
    } ?>
                    <tr>
                        <td><?php echo $page_row['id']; ?></td>
                        <td><?php echo $page_row['name']; ?></td>
                        <td><?php echo $page_row['introduce']; ?></td>
                        <td><?php echo $page_row['version']; ?></td>
                        <td><span class="badge badge-<?php echo $site_staet; ?>"><?php echo $page_row['switch']; ?></span></td>
                        <td><?php echo $page_row['shop']; ?></td>
                        <td><?php echo $page_row['state']; ?></td>
                        <td><?php echo $page_row['time']; ?></td>
                        <td class="table-action">
                            <a href="<?php echo 'site.php?id='.$page_row['id']; ?>&state=edit" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                            <a href="<?php echo 'api.php?id='.$page_row['id']; ?>&state=deletesite" class="action-icon"> <i class="mdi mdi-delete"></i></a>
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

if (empty($_GET['id']) && $_GET['state'] !== 'edit') {
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