<?php
// 假设 $currentPath 已经过处理，如 "/CODES"
// 为了防止 URL 拼接出现 //，我们统一处理 path
$displayPath = '/' . ltrim($currentPath, '/');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index of <?php echo htmlspecialchars($displayPath); ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.5; }
        h1 { font-size: 30px; margin: 0 0 10px 0; font-weight: normal; }
        hr { border: 0; border-top: 1px solid #ccc; margin: 10px 0; }

        /* 模拟 Nginx 列表样式 */
        pre { font-family: monospace; font-size: 14px; }
        a{ text-decoration: none; }
        a:hover { text-decoration: underline; color: #ff0000; font-weight: bolder}

        /* 表格布局模拟 */
        .file-list { display: table; width: 100%; max-width: 80%; }
        .file-item { display: table-row; }
        .file-item > div { display: table-cell; padding: 2px 10px; }
        .col-name { width: 60%; }
        .col-date { width: 25%; text-align: right; color: #666; }
        .col-size { width: 15%; text-align: right; color: #666; }

        footer { font-size: 12px; color: #888; margin-top: 20px; }

        .marquee{
            width: 100%;
            line-height: 2rem;
            font-size: 2rem;
            overflow: hidden;
            white-space: nowrap;
        }
        .marquee *{
            display: inline-block;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }
        @keyframes marquee {
            from{transform: translate(0,0);font-weight: bolder;color: darkgreen;}
            to{transform: translate(-100%,0);font-weight: bolder;color: darkgreen;text-shadow: 1rem 0 1rem black}
        }
    </style>
</head>
<body>
<?php if($config['needMarquee']){?>
<div class="marquee">
    <?php echo $config['marqueeContent'];?>
</div>
<?php }?>
<h1>Index of <?php echo htmlspecialchars($displayPath); ?></h1>
<hr>

<div class="file-list">
    <!-- 返回上级目录 -->
    <?php if ($displayPath !== '/'): ?>
        <div class="file-item">
            <div class="col-name"><a href="?<?php echo rawurlencode(dirname($displayPath)); ?>">../</a></div>
            <div></div>
            <div></div>
        </div>
    <?php endif; ?>

    <?php foreach ($datas as $item):
        if ($excludePattern && preg_match($excludePattern, $item['name'])) {
            continue;
        }
        $cleanPath = rtrim($displayPath, '/');
        if (isset($item['folder'])) {
            // 如果是文件夹：链接指向当前页面 + 新路径参数
            $itemUrl = "?" . rawurlencode($cleanPath . '/' . $item['name']);
            $displayName = $item['name'] . "/";
        } else {
            // 如果是文件：链接直接指向 OneDrive 的下载地址
            // 注意：必须在之前的 $select 中包含了 @microsoft.graph.downloadUrl 字段
            $itemUrl = $item['@microsoft.graph.downloadUrl'];
            $displayName = $item['name'];
        }
        // --- 核心逻辑结束 ---
        ?>
        <div class="file-item">
            <div class="col-name">
                <!-- 文件夹会跳转页面，文件会直接触发浏览器下载 -->
                <a href="<?php echo $itemUrl; ?>" <?php echo isset($item['file']) ? 'target="_blank"' : ''; ?>>
                    <?php echo htmlspecialchars($displayName); ?>
                </a>
            </div>
            <div class="col-date"><?php echo $item['lastModifiedDateTime'];?></div>
            <div class="col-size"><?php echo isset($item['file']) ? formatSize($item['size']) : '-'; ?></div>
        </div>
    <?php endforeach; ?>

</div>

<hr>
<footer>
    OneDrive File Server / <?php echo date("Y-m-d H:i:s"); ?>
</footer>

</body>
</html>
