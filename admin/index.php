<?php
    include './header.php';

	$sqlcont='select * from site';//站点数
	$cntcont=mysqli_num_rows(Execute($conn,$sqlcont));
	
	$resspk='select * from url';//授权数
	$cntspk=mysqli_num_rows(Execute($conn,$resspk));
	
	$reszan='select * from user';//管理数
	$rowzan=mysqli_num_rows(Execute($conn,$reszan));
?>
<!-- 内容标题 -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box">
      <h4 class="page-title">后台首页</h4>
    </div>
  </div>
</div>     
<!-- 内容标题 --> 

<div class="row">
	<div class="col-md-4">
		<!-- Personal-Information -->
		<div class="card">
			<div class="card-body">
				<h4 class="header-title mt-0 mb-3">系统详情卡</h4>
				<p class="text-muted font-13">
				Made by 吃纸怪 ©2021 FatDa. All rights reserved.
				</p>

				<hr>

				<div class="text-left">
					<p class="text-muted"><strong>作者 :</strong> <span class="ml-2">吃纸怪</span></p>

					<p class="text-muted"><strong>QQ :</strong><span class="ml-2">236435377</span></p>

					<p class="text-muted"><strong>Email :</strong> <span class="ml-2">2635435377@qq.com</span></p>

					<p class="text-muted"><strong>作者主页 :</strong> <span class="ml-2"><a target="_blank" href="https://chizg.cn">chizg.cn</a></span></p>

					<p class="text-muted"><strong>系统版本 :</strong>
						<span class="ml-2"> V1.0.0 </span>
					</p>

				</div>
			</div>
		</div>
		<!-- Personal-Information -->
	</div> <!-- end col-->

	<div class="col-md-8">	
		<!-- End Chart-->

		<div class="row">
			<div class="col-sm-4">
				<div class="card tilebox-one">
					<div class="card-body">
						<i class="mdi mdi-heart-box float-right text-muted"></i>
						<h6 class="text-muted text-uppercase mt-0">授权总数</h6>
						<h2 class="m-b-20"><?php echo $cntcont;?>个</h2>
					</div> <!-- end card-body-->
				</div> <!--end card-->
			</div><!-- end col -->

			<div class="col-sm-4">
				<div class="card tilebox-one">
					<div class="card-body">
						<i class="mdi mdi-tooltip-text float-right text-muted"></i>
						<h6 class="text-muted text-uppercase mt-0">站点总数</h6>
						<h2 class="m-b-20"><?php echo $cntspk;?>个</h2>
					</div> <!-- end card-body-->
				</div> <!--end card-->
			</div><!-- end col -->

			<div class="col-sm-4">
				<div class="card tilebox-one">
					<div class="card-body">
						<i class="mdi mdi-thumb-up float-right text-muted"></i>
						<h6 class="text-muted text-uppercase mt-0">管理员总数</h6>
						<h2 class="m-b-20"><?php echo $rowzan;?>个</h2>
					</div> <!-- end card-body-->
				</div> <!--end card-->
			</div><!-- end col -->

		</div>
		<!-- end row -->
		 <!-- end row-->
	</div>
	<!-- end col -->
</div>

<?php include 'footer.php';?>