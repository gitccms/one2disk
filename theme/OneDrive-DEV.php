<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['DRIVE_NAME'];?></title>
    <link rel="shortcut icon" href="<?php echo $config['FAVICON_SRC'];?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 强制使用 OneDrive 标准字体 */
        body {
            font-family: 'Segoe UI', "Segoe UI Web (West European)", -apple-system, sans-serif;
            background-color: #f3f2f1; /* OneDrive 经典灰色背景 */
        }
        /* 美化滚动条，使其更纤细 */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f3f2f1; }
        ::-webkit-scrollbar-thumb { background: #c8c6c4; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a19f9d; }
    </style>
    <style>
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body class="text-[#323130] antialiased">

<!-- 1. 顶部固定导航栏 -->
<header class="sticky top-0 z-50 w-full bg-[#0078d4] shadow-md h-12 flex items-center px-4">
    <div class="max-w-6xl mx-auto w-full flex items-center justify-between">
        <div class="flex items-center space-x-3 text-white">
            <a href="?/" class="flex items-center hover:bg-white/10 px-2 py-1 rounded transition-all">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
                <span class="font-semibold text-sm tracking-tight"><?php echo $config['DRIVE_NAME'];?></span>
            </a>
        </div>
        <!-- 搜索框占位-->
        <div class="hidden sm:block flex-1 max-w-md mx-6">
            <form method="post" action="?<?php echo $currentPath;?>" class="relative w-full">
                <!-- 搜索输入框 -->
                <input
                    type="text"
                    name="search"
                    placeholder="搜索文件..."
                    value="<?php echo isset($_POST['search'])?$_POST['search']:null;?>"
                    class="w-full bg-white/20 hover:bg-white/30 rounded px-3 py-1 text-white/80 text-xs outline-none transition-all placeholder:text-white/60 focus:bg-white/30"
                >
                <!-- 搜索按钮（视觉隐藏，保留功能） -->
                <button
                    type="submit"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-white/80 hover:text-white focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-full h-full">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- 预览模式（全屏覆盖层） -->
<?php if(isset($_GET['preview'])){?>
    <div class="fixed inset-0 z-[100] bg-white flex flex-col h-screen">
        <div class="h-10 bg-[#f3f2f1] border-b flex items-center justify-between px-4">
            <button onclick="window.history.back()" class="p-2 hover:bg-black/5 rounded-full transition-all">
                <svg class="w-5 h-5 text-[#323130]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="flex items-center text-sm font-semibold text-[#323130]">
                <svg class="w-4 h-4 mr-2 text-[#0078d4]" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                文件预览
            </div>
            <div class="flex items-center text-sm font-semibold text-[#323130] truncate">
                <a href="<?php echo $datas['infos']['@microsoft.graph.downloadUrl']; ?>"
                   class="flex items-center text-xs font-medium text-[#0078d4] hover:bg-white px-2 py-1 rounded transition-all">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    下载原始文件
                </a>
            </div>
        </div>
        <div class="flex-1 overflow-hidden relative">
            <!-- 加载动画（默认显示） -->
            <div id="loadingAnimation" class="absolute inset-0 flex items-center justify-center space-x-2 py-20 bg-white/50 backdrop-blur-sm rounded-xl z-10 transition-all duration-300">
                <div class="w-4 h-4 bg-[#FF0000] rounded-full animate-[bounce_0.6s_infinite] [animation-delay:-0.5s] shadow-[0_0_10px_rgba(255,0,0,0.4)]"></div>
                <div class="w-4 h-4 bg-[#FF8E00] rounded-full animate-[bounce_0.6s_infinite] [animation-delay:-0.4s] shadow-[0_0_10px_rgba(255,142,0,0.4)]"></div>
                <div class="w-4 h-4 bg-[#FFD041] rounded-full animate-[bounce_0.6s_infinite] [animation-delay:-0.3s] shadow-[0_0_10px_rgba(255,208,65,0.4)]"></div>
                <div class="w-4 h-4 bg-[#008E00] rounded-full animate-[bounce_0.6s_infinite] [animation-delay:-0.2s] shadow-[0_0_10px_rgba(0,142,0,0.4)]"></div>
                <div class="w-4 h-4 bg-[#0078d4] rounded-full animate-[bounce_0.6s_infinite] [animation-delay:-0.1s] shadow-[0_0_10px_rgba(0,120,212,0.4)]"></div>
                <div class="w-4 h-4 bg-[#8E008E] rounded-full animate-[bounce_0.6s_infinite] [animation-delay:0s] shadow-[0_0_10px_rgba(142,0,142,0.4)]"></div>
            </div>

            <!-- 预览iframe（默认隐藏，加载完成后显示） -->
            <iframe
                id="previewBox"
                class="w-full h-full border-none bg-white opacity-0 transition-opacity duration-300"
                src="<?php echo $datas['getUrl'];?>"
                allowfullscreen
                onload="hideLoadingAnimation()"
            ></iframe>
            <style>
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-10px); }
                }
            </style>
            <script>
                // 加载完成后隐藏动画的函数
                function hideLoadingAnimation() {
                    const loadingEl = document.getElementById('loadingAnimation');
                    const iframeEl = document.getElementById('previewBox');

                    iframeEl.classList.remove('opacity-0');
                    loadingEl.style.opacity = 0;
                    loadingEl.style.pointerEvents = 'none'; // 防止遮挡iframe交互
                    setTimeout(() => {
                        loadingEl.style.display = 'none';
                    }, 300);
                }

                // 额外保障：如果iframe加载失败（比如链接失效），也隐藏动画
                document.getElementById('previewBox').addEventListener('error', hideLoadingAnimation);
            </script>
        </div>
    </div>
<?php } ?>
<main class="max-w-6xl mx-auto p-2 sm:p-4 lg:p-6">
    <!-- 面包屑导航栏（卡片外部） -->
    <nav class="flex items-center px-2 mb-4 text-sm font-semibold text-[#0078d4]">
        <a href="?/" class="hover:underline">我的文件</a>
        <?php
        $paths = array_filter(explode('/', $currentPath));
        $stepPath = "";
        foreach ($paths as $pa):
            $stepPath .= '/' . $pa;
            $link = '?' . rawurlencode($stepPath);
            ?>
            <span class="mx-2 text-[#a19f9d] font-normal text-xs">/</span>
            <a href="<?php echo $link; ?>" class="hover:underline"><?php echo htmlspecialchars($pa); ?></a>
        <?php endforeach; ?>
    </nav>

    <!-- 文件列表主体卡片 -->
    <div class="bg-white rounded-lg shadow-[0_1.6px_3.6px_0_rgba(0,0,0,0.132),0_0.3px_0.9px_0_rgba(0,0,0,0.108)] border border-[#edebe9] overflow-hidden">

        <!-- 表头：固定在卡片顶部 -->
        <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-white border-b border-[#f3f2f1] text-lg font-semibold text-[#605e5c] uppercase tracking-wider sticky top-2 z-20">
            <div class="col-span-8 sm:col-span-7 md:col-span-6">名称</div>
            <div class="hidden sm:block col-span-3 md:col-span-4">修改日期</div>
            <div class="col-span-4 sm:col-span-2 text-right pr-2">大小</div>
        </div>

        <!-- 文件列表 -->
        <div class="divide-y divide-[#f3f2f1]">
            <?php
            $displayPath = '/' . ltrim($currentPath, '/');
            $keyword = $_POST['search']??'';
            foreach ($datas as $item):
                if ($excludePattern && preg_match($excludePattern, $item['name'])) {
                    continue;
                }
                if ($keyword && !str_contains($item['name'], $keyword)) {
                    continue;
                }
                $cleanPath = rtrim($displayPath, '/');
                $itemUrl = "?" . rawurlencode($cleanPath . '/' . $item['name']);
                ?>
                <div class="grid grid-cols-12 gap-4 px-4 py-2.5 items-center hover:bg-[#f3f2f1] transition-colors group cursor-default">

                    <!-- 文件名列 -->
                    <div class="col-span-8 sm:col-span-7 md:col-span-6 flex items-center min-w-0">
                        <div class="w-8 h-8 mr-3 flex-shrink-0">
                            <?php if(isset($item['folder'])){ ?>
<!--                                <img class="w-full h-full" src="https://res-1.cdn.office.net">-->
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 7.5C2 5.567 3.567 4 5.5 4h7.104c.83 0 1.611.343 2.164.945l2.464 2.682a.5.5 0 0 0 .368.173H26.5c1.933 0 3.5 1.567 3.5 3.5v12.7c0 1.933-1.567 3.5-3.5 3.5H5.5C3.567 27.5 2 25.933 2 24V7.5z" fill="#FFD041"/>
                                    <path d="M2 13.5c0-1.933 1.567-3.5 3.5-3.5h21c1.933 0 3.5 1.567 3.5 3.5V24c0 1.933-1.567 3.5-3.5 3.5H5.5C3.567 27.5 2 25.933 2 24V13.5z" fill="#FEB800"/>
                                </svg>
                            <?php } else { ?>
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 6C5.79086 6 4 7.79086 4 10v12c0 2.2091 1.79086 4 4 4h20c2.2091 4 4-1.7909 4-4V10c0-2.2091-1.7909-4-4-4H8z" fill="#F3F2F1"/>
                                    <path d="M8 12C5.79086 12 4 13.7909 4 16v10c0 2.2091 1.79086 4 4 4h20c2.2091 4 4-1.7909 4-4V16c0-2.2091-1.7909-4-4-4H8z" fill="#E4E4E4"/>
                                    <path d="M12 10h12" stroke="#696969" stroke-width="1.33" stroke-linecap="round"/>
                                </svg>
                            <?php } ?>
                        </div>
                        <div class="truncate">
                            <?php if(isset($item['folder'])){ ?>
                                <a href="<?php echo $itemUrl;?>" class="text-sm font-semibold text-[#323130] hover:text-[#0078d4] hover:underline truncate block">
                                    <?php echo $item['name'];?>
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $itemUrl;?>&preview=<?php echo $item['id'];?>" class="text-sm text-[#323130] hover:text-[#0078d4] hover:underline truncate block">
                                    <?php echo $item['name'];?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- 修改时间列 -->
                    <div class="hidden sm:block col-span-3 md:col-span-4 text-xs text-[#605e5c]">
                        <?php echo date('Y/m/d H:i', strtotime($item['lastModifiedDateTime']));?>
                    </div>

                    <!-- 大小列 -->
                    <div class="col-span-4 sm:col-span-2 text-right pr-2 text-xs text-[#605e5c] font-light">
                        <?php echo isset($item['file']) ? formatSize($item['size']) : '—';?>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
    </div>

    <!-- 页脚 -->
    <footer class="mt-8 mb-4 text-center text-[12px] text-[#a19f9d]">
        &copy; <?php echo date('Y');?> <?php echo $config['DRIVE_NAME'];?> · 已连接 OneDrive
    </footer>
</main>


</body>
</html>
