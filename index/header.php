<?php include './public.php';?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="<?php echo SYSTEM_KEYWORDS;?>">
    <meta name="description" content="<?php echo SYSTEM_DESCRIPTION;?>">
    <meta name="author" content="Webpixels">
    <title><?php echo SYSTEM_TITTLE;?></title>
    <!-- Preloader -->
    <style>
        @keyframes hidePreloader {
            0% {
                width: 100%;
                height: 100%;
            }

            100% {
                width: 0;
                height: 0;
            }
        }

        body>div.preloader {
            position: fixed;
            background: white;
            width: 100%;
            height: 100%;
            z-index: 1071;
            opacity: 0;
            transition: opacity .5s ease;
            overflow: hidden;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        body:not(.loaded)>div.preloader {
            opacity: 1;
        }

        body:not(.loaded) {
            overflow: hidden;
        }

        body.loaded>div.preloader {
            animation: hidePreloader .5s linear .5s forwards;
        }
    </style>
    <script>
        window.addEventListener("load", function() {
            setTimeout(function() {
                document.querySelector('body').classList.add('loaded');
            }, 300);
        });
    </script>
    <!-- Favicon -->
    <link rel="icon" href="../assets/index/img/brand/favicon.png" type="image/png"><!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/index/libs/@fortawesome/fontawesome-free/css/all.min.css">
    <!-- Quick CSS -->
    <link rel="stylesheet" href="../assets/index/css/quick-website.css" id="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="index.php">
                <img alt="Image placeholder" src="../assets/index/img/brand/dark.svg" id="navbar-logo">
            </a>
            <!-- Toggler -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Collapse -->
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mt-4 mt-lg-0 ml-auto">
                    <li class="nav-item ">
                        <a class="nav-link" href="index.php">主页</a>
                    </li>
                </ul>
                <!-- Button -->
                <a class="navbar-btn btn btn-sm btn-primary d-none d-lg-inline-block ml-3" href="">
                    获取授权
                </a>
                <a class="navbar-btn btn btn-sm btn-warning d-none d-lg-inline-block" href="find.php" target="_blank">
                    查询授权
                </a>
                <!-- Mobile button -->
                <div class="d-lg-none text-center">
                    <a href="" class="btn btn-sm btn-primary">查询授权</a>
                    <a href="find.php" class="btn btn-sm btn-warning">查询授权</a>
                </div>
            </div>
        </div>
    </nav>
    </nav>
    <!-- Main content -->
                <?php 
                    /*
                    弹窗提示
                    $_GET['notifications'] 状态
                    参数1，2，3/success,warning,danger
                    $_GET['notifications_content'] 内容
                    */
                    if($_GET['notifications'] == "1" || $_GET['notifications'] == "2" || $_GET['notifications'] == "3"){
                        if($_GET['notifications'] == '1'){
                            $notifications = 'success';
                        }
                        if($_GET['notifications'] == '2'){
                            $notifications = 'warning';
                        }
                        if($_GET['notifications'] == '3'){
                            $notifications = 'danger';
                        }
                ?>

                    <div class="container">
                        <div class="alert alert-<?php echo $notifications?> alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <?php echo $_GET['notifications_content']?>
                        </div>
                    </div>

                <?php 
                    }
                ?>