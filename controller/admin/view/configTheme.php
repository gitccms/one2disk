<?php
if(isAdmin()&&isset($_GET['configTheme'])){#是管理员才允许修改
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
    <main class="flex-1 rounded-3xl  mt-[max(1vh, 1vw)] mb-[max(1vh, 1vw)] text-sm lg:text-l shadow-xl">
        <form ref="myForm"  id="myForm" method="post" action="?<?php echo $config['ADMIN_CODE'];?>&configTheme" class="flex flex-col p-10">
            <div class="grid md:grid-cols-4 gap-x-2 gap-y-8">
                <div class="flex item-center col-span-2">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">favicon地址</span>
                    <input name="FAVICON_SRC" :value="configs.FAVICON_SRC" placeholder="网站favicon链接(支持外链)" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center col-span-2">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">github主页</span>
                    <input name="ADMIN_GITHUB" :value="configs.ADMIN_GITHUB" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">首页主题</span>
                    <select name="THEME" v-model="configs.THEME" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                        <option value="nginx">Nginx</option>
                        <option value="OneDrive-DEV">OneDrive</option>
                        <option value="glasses">毛玻璃效果</option>
                    </select>
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">管理员名称</span><input name="ADMIN_NAME" :value="configs.ADMIN_NAME" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">管理员邮箱</span><input name="ADMIN_EMAIL" :value="configs.ADMIN_EMAIL" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex item-center items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">管理员电话</span><input name="ADMIN_TEL" :value="configs.ADMIN_TEL" placeholder="" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200">
                </div>
                <div class="flex items-center">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700 text-sm">开启跑马灯</span>

                    <div class="flex items-center gap-6 grow">
                        <!-- 选项：是 -->
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="needMarquee" value="1"
                                   :checked="configs.needMarquee == true || configs.needMarquee == '1'"
                                   @change="configs.needMarquee = true"
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2 transition">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-blue-600 font-medium">开启</span>
                        </label>
                        <!-- 选项：否 -->
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="needMarquee" value="0"
                                   :checked="configs.needMarquee == false || configs.needMarquee == '0'"
                                   @change="configs.needMarquee = false"
                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2 transition">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-red-600 font-medium">关闭</span>
                        </label>
                    </div>

                    <!-- 移除之前的 hidden input，因为 radio 本身就有 name 了 -->
                </div>

                <div class="flex item-center col-span-3">
                    <span class="w-12 lg:w-24 text-right shrink-0 font-medium text-gray-700">跑马灯内容</span><textarea name="marqueeContent" :value="configs.marqueeContent" placeholder="跑马灯内容,支持html" class="grow text-left rounded-lg border border-gray-300 px-2 py-1 mx-2 focus:outline-none focus:shadow-lg focus:shadow-blue-200"></textarea>
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
                    if (!myForm.value.ADMIN_NAME.value) {
                        myForm.value.ADMIN_NAME.value='Administrator';
                        return ElementPlus.ElMessage.error("管理员名称不能为空，已更新为Administrator");
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