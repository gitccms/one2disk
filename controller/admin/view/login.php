<?php
session_start();
if(isset($_GET['logout'])){
    $_SESSION['isAdmin']=false;
    $_SESSION['isLogin']=false;
}
?>

<?php if(!isLogin()){?>
    <main class="flex-1 rounded-3xl  mx-auto h-[80vh]
                lg:w-[50%]  lg:mt-[10vh] lg:p-[max(1vh, 1vw)]
                text-sm lg:text-2xl shadow-xl">
        <form ref="myForm" method="post" action="?<?php echo $config['ADMIN_CODE'];?>&login" class="flex flex-col">
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-4">
                <div class="flex item-center justify-center col-span-2">
                    <span class="text-center font-medium text-gray-900">Mixi2, Hua bu Laji</span>
                </div>
                <div class="flex item-center col-span-2">
                    <span class="w-40 text-right mr-4 shrink-0 font-medium text-gray-700">Key<i class="fa-solid fa-clipboard-user"></i></span>
                    <input class="grow text-left rounded-lg border border-gray-300 px-2 py-1 focus:outline-none focus:shadow-lg focus:shadow-blue-200"
                           name="ADMIN1" value="如果你不米西" disabled>
                </div>
                <div class="flex item-center col-span-2">
                    <span class="w-40 text-right mr-4 shrink-0 font-medium text-gray-700">Password<i class="fa-solid fa-passport"></i></span>
                    <input class="grow text-left rounded-lg border border-gray-300 px-2 py-1 focus:outline-none focus:shadow-lg focus:shadow-blue-200"
                           name="ADMIN2" value="" placeholder="请输入密码">
                </div>
                <div class="flex item-center justify-center col-span-2">
                    <el-button type="primary" size="large" native-type="submit" class="text-center font-medium text-gray-900">登录</el-button>
                </div>
            </div>
        </form>
    </main>

    <script>
        const { createApp, ref, reactive } = Vue;
        const app = createApp({
            setup() {
                return {};
            }
        });
        app.use(ElementPlus)
        app.mount('#app');
    </script>
<?php }else{?>
    <pre class="text-[5px]">
<?php
    echo json_encode($_SERVER,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
//    header("Location:".$_REQUEST['']);
    exit; // 务必加上 exit，防止后续代码继续执行
}?>
        </pre>
