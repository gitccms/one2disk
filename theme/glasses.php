<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['DRIVE_NAME'];?></title>
    <link rel="shortcut icon" href="<?php echo $config['FAVICON_SRC'];?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes colorCycle {
            0% { background-color: #fef9c3; } 25% { background-color: #dcfce7; }
            50% { background-color: #dbeafe; } 75% { background-color: #fce7f3; } 100% { background-color: #fef9c3; }
        }
        .animated-bg { animation: colorCycle 25s ease-in-out infinite; position: fixed; inset: 0; z-index: -1; }
        .mac-folder {
            background: linear-gradient(180deg, #93c5fd 0%, #60a5fa 45%, #3b82f6 100%);
            position: relative;
            box-shadow: inset 0 1px 1px rgba(255,255,255,0.5), 0 2px 4px rgba(0,0,0,0.1);
        }
        .mac-folder::before {
            content: '';
            position: absolute;
            top: -6px; left: 0;
            width: 40%; height: 12px;
            background: #93c5fd;
            border-radius: 6px 6px 0 0;
            box-shadow: inset 0 1px 1px rgba(255,255,255,0.4);
        }

        /* 文件图标质感 */
        .mac-file {
            background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), inset 0 0 0 1px rgba(255,255,255,0.8);
        }
    </style>
</head>
<body class="h-screen w-screen flex items-center justify-center font-sans overflow-hidden lg:p-10">

<div class="animated-bg"></div>

<div class="relative w-full h-full lg:w-[88%] lg:h-[88vh] flex flex-col bg-white/70 backdrop-blur-[60px] shadow-[0_40px_100px_rgba(0,0,0,0.06)] border-white/90 lg:border lg:rounded-[3rem] overflow-hidden">
    <!-- Header -->
    <header class="h-20 lg:h-24 flex items-center justify-between px-8 lg:px-12 border-b border-black/[0.03]">
        <div class="flex items-center space-x-6">
            <div class="hidden lg:flex space-x-2">
                <div class="w-3 h-3 rounded-full bg-black/[0.08]"></div>
                <div class="w-3 h-3 rounded-full bg-black/[0.08]"></div>
                <div class="w-3 h-3 rounded-full bg-black/[0.08]"></div>
            </div>
            <h2 class="text-lg font-bold text-gray-800/80"><?php echo $config['DRIVE_NAME'];?></h2>
        </div>
        <div class="flex items-center space-x-4">
            <form method="post" action="?<?php echo $currentPath;?>">
            <input type="text" name="search" value="<?php echo $_POST['search']??'';?>" placeholder="搜索"
                   class="bg-black/[0.03] border border-black/[0.05] rounded-xl px-4 py-2 text-sm focus:outline-none focus:bg-white/50 w-40 lg:w-60 transition-all">
            </form>
            <button class="bg-blue-500 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all">＋</button>

        </div>
    </header>

    <!-- 内容区 -->
    <div class="flex-1 p-8 lg:p-12 overflow-y-auto">
        <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8  2xl:grid-cols-10 gap-x-8 gap-y-12">
            <?php
            $displayPath = '/' . ltrim($currentPath, '/');
            $keyword = $_POST['search']??'';
            foreach ($datas as $item):
                if ($keyword && !str_contains($item['name'], $keyword)) {
                    continue;
                }
                $cleanPath = rtrim($displayPath, '/');
                $itemUrl = "?" . rawurlencode($cleanPath . '/' . $item['name']);
                if(isset($item['folder'])){
            ?>
            <!-- 文件夹 -->
            <div class="group flex flex-col items-center cursor-pointer">
                <a href="<?php echo $itemUrl;?>">
                <div class="relative w-16 h-12 lg:w-20 lg:h-14 mac-folder rounded-br-xl rounded-bl-xl rounded-tr-xl mt-2 group-hover:brightness-110 transition-all">
                    <div class="absolute inset-x-2 top-2 h-0.5 bg-white/20 rounded-full"></div>
                </div>
                <p class="mt-4 text-[11px] lg:text-xs font-semibold text-gray-700"><?php echo $item['name'];?></p>
                </a>
            </div>
            <?php }
            if(isset($item['file'])){?>
            <!-- 通用文档图标 -->
            <div class="group flex flex-col items-center cursor-pointer">
                <a href="<?php echo $itemUrl."&preview".$item['id'];?>">
                <div class="w-14 h-16 lg:w-16 lg:h-20 mac-file rounded-lg flex items-center justify-center group-hover:scale-105 transition-all border border-black/[0.05]">
                    <span class="text-[10px] font-black text-blue-500">PDF</span>
                </div>
                <p class="mt-4 text-[11px] lg:text-xs font-semibold text-gray-700"><?php echo $item['name'];?></p>
                </a>
            </div>
            <?php }endforeach;?>

        </div>
    </div>

    <!-- Footer -->
    <footer class="h-12 px-12 flex items-center justify-center text-[10px] font-bold text-gray-400/60 bg-white/20 border-t border-black/[0.02] tracking-[0.3em]">
        <?php echo count($datas);?>个项目
    </footer>
</div>

</body>
</html>
