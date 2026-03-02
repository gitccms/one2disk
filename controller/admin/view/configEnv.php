<?php
if(isAdmin()&&isset($_GET['configEnv'])){#是管理员才允许修改
    foreach(array_keys($_POST) as $key){
        $config[$key] = $_POST[$key]??$config[$key];
    }
    $content = "<?php\nreturn ".var_export($config, true).";\n?>";
    if (!file_put_contents($configPath, $content)) {
        echo "写入失败，请检查文件权限。";
    } else {
        $x=1;
//        header("Location:./?".$config['ADMIN_CODE']);
    }

}
?>
<?php if(isAdmin()){?>
    <main class="flex-1 rounded-3xl text-sm lg:text-l shadow-xl">
        <form ref="myForm" id="myForm" method="post" action="?<?php echo $config['ADMIN_CODE'];?>&configEnv" class="flex flex-col p-10">
            <div class="grid md:grid-cols-4 gap-x-6 gap-y-8 pb-10">
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-32 text-right shrink-0 font-medium text-gray-700">云盘名称</span><input name="DRIVE_NAME" :value="configs.DRIVE_NAME" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-32 text-right shrink-0 font-medium text-gray-700">管理入口参数</span><input name="ADMIN_CODE" :value="configs.ADMIN_CODE" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center col-span-2">
                    <span class="w-12 lg:w-32 text-right shrink-0 font-medium text-gray-700">密码文件</span><input name="PASSWORD_FILE" :value="configs.PASSWORD_FILE" placeholder="访问当前目录的密码存储在哪个文件内" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center col-span-4">
                    <span class="w-12 lg:w-32 text-right shrink-0 font-medium text-gray-700">不显示文件名称</span><input name="HIDDEN_NAME_LIST" :value="configs.HIDDEN_NAME_LIST" placeholder="包含该名称的文件(夹)不显示，半角逗号做间隔" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
            </div>
        </form>
    </main>
<?php }else{?>
    <h1>没有登录</h1>
<?php }?>

    <script>
        const { createApp, ref, reactive } = Vue;
        const app = createApp({
            setup() {
                const configs = ref(<?php echo json_encode($config,JSON_UNESCAPED_UNICODE) ?: '[]'; ?>);
                const myForm = ref(null);


                const triggerSubmit = () => {
                    if (!myForm.value.ADMIN_CODE.value) {
                        myForm.value.ADMIN_CODE.value='admin';
                        return ElementPlus.ElMessage.error("映射路径不能为空，已设为admin");
                    }
                    if (myForm.value) {
                        myForm.value.submit();
                    }
                };
                return { configs, myForm, triggerSubmit};
            }
        });
        app.use(ElementPlus)
        app.mount('#app');
    </script>
<?php include "./htmls/foot.php";?>