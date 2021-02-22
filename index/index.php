<?php   
	include './header.php';

	$sqlcont='select * from site';//站点数
	$cntcont=mysqli_num_rows(Execute($conn,$sqlcont));
	
	$resspk='select * from url';//授权数
	$cntspk=mysqli_num_rows(Execute($conn,$resspk));
	
	$reszan='select * from user';//管理数
	$rowzan=mysqli_num_rows(Execute($conn,$reszan));

?>
    <!-- Main content -->
    <section class="slice py-5">
        <div class="container">
            <div class="row row-grid align-items-center">
                <div class="col-12 col-md-5 col-lg-6 order-md-2">
                    <!-- Image -->
                    <figure class="w-100">
                        <img alt="Image placeholder" src="../assets/index/img/svg/illustrations/illustration-3.svg" class="img-fluid mw-md-120">
                    </figure>
                </div>
                <div class="col-12 col-md-7 col-lg-6 order-md-1 pr-md-5">
                    <!-- Heading -->
                    <h1 class="display-4 text-center text-md-left mb-3">
                        欢迎来到 <strong class="text-primary"><?php echo SYSTEM_TITTLE;?></strong>
                    </h1>
                    <!-- Text -->
                    <p class="lead text-center text-md-left text-muted">
						<?php echo SYSTEM_DESCRIPTION;?>
                    </p>
                    <!-- Buttons -->
                    <div class="text-center text-md-left mt-5">
                        <a href="#" class="btn btn-primary btn-icon">
                            <span class="btn-inner--text">获取授权</span><span class="btn-inner--icon">
                                <i data-feather="arrow-right"></i>
                            </span>
                        </a>
                        <a href="find.php" class="btn btn-neutral btn-icon d-none d-lg-inline-block">查询授权</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<section class="slice py-lg-6 bg-primary">
		<div class="shape-container shape-line shape-position-top shape-orientation-inverse">
			<svg width="2560px" height="100px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="none" x="0px" y="0px" viewBox="0 0 2560 100" style="enable-background:new 0 0 2560 100" xml:space="preserve" class="">
			<polygon points="2560 0 2560 100 0 100"></polygon>
			</svg>
		</div>
		<div class="container pt-4 position-relative zindex-100">
			<div class="row mb-5 justify-content-center text-center">
				<div class="col-lg-8 col-md-10">
					<h2 class="text-white mt-4">数据详情</h2>
					<div class="mt-2">
						<p class="lead lh-180 text-white">以下是目前该站点的授权应用，以及授权域名数据情况，仅供参考！</p>
					</div>
				</div>
			</div>
			<div class="row mt-4">
				<div class="col-lg-6 mx-auto">
					<div class="row">
						<div class="col-6">
							<div class="text-center">
								<h3 class="mb-0">
									<span class="counter text-white counting-finished" data-from="0" data-to="30" data-speed="3000" data-refresh-interval="200"><?php echo $cntcont;?></span></h3>
								<p class="h6 text-sm text-white mb-0">应用数</p>
							</div>
						</div>
						<div class="col-6">
								<div class="text-center">
									<h3 class="mb-0"><span class="counter text-white counting-finished" data-from="0" data-to="53" data-speed="3000" data-refresh-interval="200"><?php echo $cntspk;?></span></h3>
								<p class="h6 text-sm text-white mb-0">授权数</p>
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

<?php include './footer.php';?>