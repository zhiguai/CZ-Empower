<?php
    include './header.php';
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
    if ($_GET['state'] == "search") {
        # 搜索
?>


<?php
    } elseif ($_GET['state'] == "edit") {
        # 修改
?>
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
            <form action="card.php" method="GET">
            <div class="input-group col-sm-8  m-auto">
                <input type="text" class="form-control" name="searchcont" placeholder="Search...">
                <div class="input-group-append">
                    <button class="btn btn-primary" name="search" type="submit">搜索</button>
                </div>
            </div>
            </form>
            <br>
            <p class="text-muted w-50 m-auto">
                可搜索表白卡的ID，双方姓名，内容，IP，时间
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
                        <th>表当前版本</th>
                        <th>授权状态</th>
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
    if ($page_row['state'] == 'false') {
        $site_staet = 'warning';
    }
    //判断是否超过规定显示字数
    if (mb_strlen($page_row['introduce'])>5) {
        $page_row['introduce']=mb_substr($page_row['introduce'], 0, 5, "utf-8").'...';
    } ?>
                    <tr>
                        <td><span class="badge badge-<?php echo $site_staet; ?>"><?php echo $page_row['id']; ?></span></td>
                        <td><?php echo $page_row['name']; ?></td>
                        <td><?php echo $page_row['introduce']; ?></td>
                        <td><?php echo $page_row['version']; ?></td>
                        <td><?php echo $page_row['state']; ?></td>
                        <td><?php echo $page_row['time']; ?></td>
                        <td class="table-action">
                            <a href="<?php echo 'card.php?id='.$page_row['id']; ?>&state=editcard" class="action-icon"> <i class="mdi mdi-pencil"></i></a>
                            <a href="<?php echo 'api.php?id='.$page_row['id']; ?>&state=deletecard" class="action-icon"> <i class="mdi mdi-delete"></i></a>
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