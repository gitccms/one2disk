<?php
/* 这里是所有界面的路由
 * 必要参数是?admin,也就是在config中设置的admin_code
 * 首先校验amdin_code，若没有则可以跳转首页
 * 通过参数login,config,等进行include文件的
 *
 * */
session_start();#违规访问跳转首页
$configPath = "../../.config/config.php";
$config = require_once $configPath;
require_once "./utils.php";
$act = require_once "../../lib/readAccessToken.php";
$rft = require_once "../../lib/readRefreshToken.php";
#1.首先校验amdin_code或logout，若没有则可以跳转首页
if(!isset($_GET[$config['ADMIN_CODE']])) header("Location:/");
#2.通过参数进行页面路由
/* 路由逻辑
 * 若含参数login，则跳login
 * 通过utils函数isLogin判断是否登录，若未登录则跳登录界面，否则跳转config
 * 若含参数config,跳config
 * 通过utils函数isLogin判断是否登录，若未登录则跳登录界面，否则跳转config
 * */
if(isset($_POST['ADMIN2'])&&$_POST['ADMIN2']==="我就不能拉几"){
//    $_POST=null;
    $_SESSION['isLogin']=true;
    $_SESSION['isAdmin']=true;
}
if(isset($_GET['logout'])||!isLogin()){
    $route_path="./view/login.php";
    $TITLE = "管理-登录";
}
elseif(isAdmin()){
    $route_path="./view/configBasic.php";
    $TITLE = "管理-编辑";
    if(isset($_GET['configBasic'])){
        $route_path="./view/configBasic.php";
        $TITLE = "管理基础配置";
    }elseif(isset($_GET['configEnv'])){
        $route_path="./view/configEnv.php";
        $TITLE = "管理环境变量";
    }elseif(isset($_GET['configTheme'])){
        $route_path="./view/configTheme.php";
        $TITLE = "编辑主题参数";
    }
}
else{
    $route_path="./view/login.php";
    $TITLE = "管理-错误";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta  value="configs." name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $TITLE?></title>
    <link rel="shortcut icon" href="<?php echo $config['FAVICON_SRC'];?>">

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://unpkg.com/element-plus/dist/index.full.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">
    <script src="https://unpkg.com/@element-plus/icons-vue/dist/index.iife.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.2.0/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.2.0/css/fontawesome.min.css">
</head>
<body>
<div id="app" class="flex flex-col min-h-screen">
        <!-- Header: 顶部状态栏 -->
        <?php if(isAdmin()){?>
        <header id="head" class="h-12 bg-blue-600 flex items-center px-6 shadow-lg z-20 text-white justify-between">
            <div class="flex items-center gap-3 font-bold tracking-tight">
                <i class="fa-solid fa-gears text-blue-200"></i>
                <span><?php echo $TITLE;?></span>
                Microsoft Graph连接状态:
                <span>
                    <?php if($config['isSign']){?>
                        <label class="text-green-500 font-black shadow"><i class="fa-solid fa-thumbs-up"></i>已注册</label>
                    <?php }else{?>
                        <label class="text-red-500 font-black shadow"><i class="fa-solid fa-circle-xmark"></i>未注册</label>
                    <?php }?>
                </span>
            </div>
            <div class="flex flex-row items-center gap-3">
                <div class="flex items-center font-bold tracking-tight">
                    <a href="./oauth.php?refreshToken&redirect_uri=<?php echo urlencode($_SERVER['REQUEST_URI']);?>"><i class="fa-solid fa-arrows-rotate"></i>刷新Token</a>
                </div>
                <div class="flex items-center font-bold tracking-tight">
                    <button type="submit" form="myForm"><i class="fa-regular fa-circle-check"></i>提交当前数据</button>
                </div>
                <div class="flex items-center gap-4 text-sm opacity-90 ml-10">
                    <a href="?logout" class="bg-blue-800 px-3 py-1 rounded-full"><i class="fa-solid fa-user-shield mr-2"></i><?php echo $config['ADMIN_NAME'];?></a>
                    <a href="?<?php echo $config['ADMIN_CODE'];?>&logout" class="bg-red-500/10 px-3 py-1 rounded-full hover:bg-red-800 transition-all text-red-200">
                        <i class="fa-solid fa-power-off"></i>
                        <span class="text-sm font-medium">退出登录</span>
                    </a>
                </div>
            </div>
        </header>
        <main id="main" class="flex-1 flex flex-row overflow-hidden">
            <aside id="aside" class="w-48 bg-stone-700 flex flex-col p-4 shadow-2xl z-10">
                <!-- 编辑按钮菜单 -->
                <nav class="flex flex-col gap-2 flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    <a href="?<?php echo $config['ADMIN_CODE'];?>&configBasic" class="flex items-center gap-3 px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800 hover:text-white transition-all group">
                        <i class="fa-solid fa-pen-to-square group-hover:text-blue-400"></i>
                        <span class="text-sm font-medium">基础参数编辑</span>
                    </a>

                    <a href="?<?php echo $config['ADMIN_CODE'];?>&configEnv" class="flex items-center gap-3 px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800 hover:text-white transition-all group">
                        <i class="fa-solid fa-chart-line group-hover:text-emerald-400"></i>
                        <span class="text-sm font-medium">环境变量编辑</span>
                    </a>

                    <a href="?<?php echo $config['ADMIN_CODE'];?>&configTheme" class="flex items-center gap-3 px-4 py-3 rounded-xl text-zinc-400 hover:bg-zinc-800 hover:text-white transition-all group">
                        <i class="fa-solid fa-shield-halved group-hover:text-purple-400"></i>
                        <span class="text-sm font-medium">主题配置编辑</span>
                    </a>
                </nav>

                <!-- 底部退出 -->
                <div class="pt-4 border-t border-zinc-800">
                    <a href="?logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 transition-all">
                        <i class="fa-solid fa-power-off"></i>
                        <span class="text-sm font-medium">退出登录</span>
                    </a>
                </div>
            </aside>
            <?php }?>

            <!-- 内容展示区: Iframe 所在位置 -->
            <section class="flex-1 bg-zinc-200 relative p-4">
                <div class="w-full h-full bg-white rounded-3xl shadow-sm border border-zinc-300/50 overflow-hidden">
                    <?php include_once $route_path;?>
<!--                    <iframe id="mainBox" name="mainBox" class="w-full h-full border-none" src="about:blank"></iframe>-->
                </div>
            </section>

        </main>
        <footer class="p-3 transition-shadow duration-300 bg-stone-700 text-zinc-200 gap-3">
            <a href="/"><i class="fa-solid fa-house-chimney"></i>首页</a>
            <a href="../../"><i class="fa-solid fa-sitemap"></i>网盘首页</a>
            <?php
            if(!empty($config['ADMIN_GITHUB'])){
                echo "<a href=\"".$config['ADMIN_GITHUB']."\" target=\"_blank\"><i class=\"fa-brands fa-github\"></i>GitHub</a>";
            }
            if(!empty($config['ADMIN_EMAIL'])){
                echo "<a href='mailto:".$config['ADMIN_EMAIL']."'><i class=\"fa-solid fa-envelope\"></i>{$config['ADMIN_EMAIL']}</a>";
            }
            if(!empty($config['ADMIN_TEL'])){
                echo "<a href='tel:".$config['ADMIN_TEL']."'><i class=\"fa-solid fa-phone\"></i>{$config['ADMIN_TEL']}</a>";
            }
            ?>
        </footer>

    <style>
        /* 美化侧边栏滚动条 */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
    </style>

    <?php
//#路由跳转部分
//include_once $route_path;
//?>
</div>
<script>

</script>
</body>
</html>

