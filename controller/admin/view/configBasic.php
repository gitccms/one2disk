<?php
if(isAdmin()&&isset($_GET['configBasic'])){#是管理员才允许修改
    foreach(array_keys($_POST) as $key){
        $config[$key] = $_POST[$key]??$config[$key];
    }
    $config['isSign']=true;
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
        <form ref="myForm" id="myForm" method="post" action="?<?php echo $config['ADMIN_CODE'];?>&configBasic" class="flex flex-col p-10">
            <div class="grid lg:grid-cols-2 gap-x-12 gap-y-4">
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">*应用ID</span><input name="CLIENT_ID" :value="configs.CLIENT_ID" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">*租户ID</span><input name="TENANT_ID" :value="configs.TENANT_ID" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">*应用机密</span><input name="CLIENT_SECRET" :value="configs.CLIENT_SECRET" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">映射路径</span><input name="SHARE_ROOT" :value="configs.SHARE_ROOT" placeholder="将哪个目录映射，例如/user/hub" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex flex-row items-center  lg:col-span-2">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">重定向链接</span><input name="REDIRECT_URI" :value="configs.REDIRECT_URI" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>


                <div class="flex flex-row h-30 w-auto items-center col-span-2">
                    <hr class="h-10 w-full">
                </div>
                <div class="flex flex-row w-auto items-center h-full">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">Access_Token</span>
                    <textarea class="grow text-left text-[10px] rounded-lg border border-gray-300 px-2 break-all h-full leading-tight"
                              :value="access_token" rows="20" disabled readonly>
                    </textarea>
                </div>
                <div class="flex flex-row w-auto items-center h-full">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">Refresh_Token</span>
                    <textarea class="grow text-left text-sm rounded-lg border border-gray-300 px-2 break-all h-full leading-tight"
                              :value="refresh_token" rows="20" disabled readonly>
                    </textarea>
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
                const access_token = ref("<?php echo $act;?>");
                const refresh_token = ref("<?php echo $rft;?>");
                const myForm = ref(null);


                const triggerSubmit = () => {
                    if (!myForm.value.CLIENT_ID.value) {
                        myForm.value.CLIENT_ID.focus();
                        return ElementPlus.ElMessage.error("应用ID不能为空");
                    }
                    if (!myForm.value.CLIENT_SECRET.value) {
                        myForm.value.CLIENT_SECRET.focus();
                        return ElementPlus.ElMessage.error("应用机密不能为空");
                    }
                    if (!myForm.value.TENANT_ID.value) {
                        myForm.value.TENANT_ID.focus();
                        return ElementPlus.ElMessage.error("租户ID不能为空");
                    }
                    if (!myForm.value.REDIRECT_URI.value) {
                        myForm.value.REDIRECT_URI.focus();
                        return ElementPlus.ElMessage.error("重定向链接不能为空");
                    }
                    if (!myForm.value.SHARE_ROOT.value) {
                        myForm.value.SHARE_ROOT.value='/';
                        return ElementPlus.ElMessage.error("映射路径不能为空，已设为根目录");
                    }
                    var shareRoot = myForm.value.SHARE_ROOT.value;
                    if (shareRoot.endsWith('/')) {
                        shareRoot = shareRoot.replace(/\/+$/, "");
                        configs.value.SHARE_ROOT.value = shareRoot;
                        ElementPlus.ElMessage.info("已自动去除目录末尾的斜杠");
                    }
                    if (myForm.value) {
                        myForm.value.submit();
                    }
                };
                return { configs,access_token, refresh_token, myForm, triggerSubmit};
            }
        });
        app.use(ElementPlus)
        app.mount('#app');
    </script>
    <?php include "./htmls/foot.php";?>