<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 8.4 环境探针</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 30px; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2); }
        .header p { opacity: 0.9; font-size: 1.1em; }
        .card {
            background: white; border-radius: 12px; padding: 25px;
            margin-bottom: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #333; margin-bottom: 20px; padding-bottom: 10px;
            border-bottom: 2px solid #667eea; display: flex; align-items: center; gap: 10px;
        }
        .card h2::before { content: ''; width: 4px; height: 24px; background: #667eea; border-radius: 2px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; }
        .info-item {
            display: flex; justify-content: space-between; padding: 12px 15px;
            background: #f8f9fa; border-radius: 8px; border-left: 3px solid #667eea;
        }
        .info-item label { color: #666; font-weight: 500; }
        .info-item value { color: #333; font-weight: 600; }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
        .status-item {
            display: flex; align-items: center; gap: 8px; padding: 10px 15px;
            border-radius: 8px; font-size: 0.95em;
        }
        .status-item.installed { background: #d4edda; color: #155724; }
        .status-item.missing { background: #f8d7da; color: #721c24; }
        .status-icon {
            width: 20px; height: 20px; border-radius: 50%; display: flex;
            align-items: center; justify-content: center; font-weight: bold; font-size: 12px;
        }
        .status-item.installed .status-icon { background: #28a745; color: white; }
        .status-item.missing .status-icon { background: #dc3545; color: white; }
        .section-title {
            color: #555; font-size: 1.1em; margin: 20px 0 15px 0;
            padding-left: 10px; border-left: 3px solid #764ba2;
        }
        .footer { text-align: center; color: white; opacity: 0.8; margin-top: 30px; font-size: 0.9em; }
        @media (max-width: 768px) {
            .header h1 { font-size: 1.8em; }
            .card { padding: 20px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 PHP 8.4 环境探针</h1>
            <p>WordPress 运行环境检测与性能分析</p>
        </div>
        <div class="card">
            <h2>服务器基本信息</h2>
            <div class="info-grid">
                <div class="info-item"><label>服务器系统</label><value><?php echo php_uname('s') . ' ' . php_uname('r'); ?></value></div>
                <div class="info-item"><label>服务器软件</label><value><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></value></div>
                <div class="info-item"><label>PHP 版本</label><value><?php echo PHP_VERSION; ?></value></div>
                <div class="info-item"><label>PHP 运行方式</label><value><?php echo php_sapi_name(); ?></value></div>
                <div class="info-item"><label>当前时间</label><value><?php echo date('Y-m-d H:i:s'); ?></value></div>
            </div>
        </div>
        <div class="card">
            <h2>PHP 核心配置</h2>
            <div class="info-grid">
                <div class="info-item"><label>内存限制</label><value><?php echo ini_get('memory_limit'); ?></value></div>
                <div class="info-item"><label>上传限制</label><value><?php echo ini_get('upload_max_filesize'); ?></value></div>
                <div class="info-item"><label>POST 限制</label><value><?php echo ini_get('post_max_size'); ?></value></div>
                <div class="info-item"><label>最大执行时间</label><value><?php echo ini_get('max_execution_time'); ?> 秒</value></div>
                <div class="info-item"><label>时区设置</label><value><?php echo ini_get('date.timezone') ?: '未设置'; ?></value></div>
            </div>
        </div>
        <div class="card">
            <h2>WordPress 必需扩展</h2>
            <?php $required = ['curl'=>'HTTP请求','dom'=>'XML处理','exif'=>'图片元数据','fileinfo'=>'文件类型检测','gd'=>'图像处理','iconv'=>'字符编码','intl'=>'国际化','json'=>'JSON处理','mbstring'=>'多字节字符串','mysqli'=>'MySQL连接','openssl'=>'SSL加密','pcre'=>'正则表达式','pdo_mysql'=>'PDO MySQL','xml'=>'XML解析','zip'=>'压缩文件','zlib'=>'数据压缩']; ?>
            <div class="status-grid">
                <?php foreach ($required as $ext => $desc): ?>
                <div class="status-item <?php echo extension_loaded($ext) ? 'installed' : 'missing'; ?>">
                    <span class="status-icon"><?php echo extension_loaded($ext) ? '✓' : '✗'; ?></span>
                    <span><?php echo $ext; ?> <small>(<?php echo $desc; ?>)</small></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="footer"><p>PHP 8.4 FPM 编译安装部署指南 | 探针页面</p></div>
    </div>
</body>
</html>
