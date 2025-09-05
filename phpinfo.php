<?php
/**
 * PHP环境检查文件
 * 检查PHP版本和指定扩展是否安装
 */

// 设置HTML头部
header('Content-Type: text/html; charset=utf-8');

// 确保只有本地访问或通过特定IP访问此文件（可选的安全措施）
$allowed_ips = ['127.0.0.1', '::1'];
$client_ip = $_SERVER['REMOTE_ADDR'];

if (!in_array($client_ip, $allowed_ips)) {
    die('访问被拒绝：此文件仅允许本地访问。');
}

// 获取PHP版本信息
$php_version = phpversion();

// 检查是否为PHP 7.4版本
$is_php74 = (strpos($php_version, '7.4') === 0);

// 要检查的扩展列表
$extensions_to_check = [
    'fileinfo' => 'PHP Fileinfo 扩展',
    'redis' => 'PHP Redis 扩展',
    'swoole' => 'PHP Swoole 扩展（要求版本 4.x）'
];

// 开始输出HTML
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP扩展检查结果</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .extension {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .installed {
            background-color: #d4edda;
            border-color: #28a745;
        }
        .not-installed {
            background-color: #f8d7da;
            border-color: #dc3545;
        }
        .version {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>PHP扩展检查结果</h1>
    
    <div class="info">
        <p><strong>PHP版本:</strong> <?php echo $php_version; ?></p>
        <p><strong>版本要求:</strong> PHP 7.4</p>
        <p><strong>版本状态:</strong> <span style="color: <?php echo $is_php74 ? '#28a745' : '#dc3545'; ?>">
            <?php echo $is_php74 ? '✓ 符合要求' : '✗ 不符合要求'; ?>
        </span></p>
        <p><strong>检查时间:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>

    <?php
    // 遍历检查每个扩展
    foreach ($extensions_to_check as $ext_name => $ext_description) {
        $is_installed = extension_loaded($ext_name);
        $ext_class = $is_installed ? 'installed' : 'not-installed';
        $status_text = $is_installed ? '已安装' : '未安装';
        
        echo "<div class='extension {$ext_class}'>";
        echo "<h3>{$ext_description}: <span style='color: " . ($is_installed ? '#28a745' : '#dc3545') . ";'>{$status_text}</span></h3>";
        
        // 如果是已安装的扩展，显示版本信息
        if ($is_installed) {
            if ($ext_name === 'swoole') {
                // 特殊处理swoole扩展，获取更详细的版本信息
                $swoole_version = phpversion('swoole');
                echo "<div class='version'>版本: {$swoole_version}</div>";
                
                // 检查是否是swoole 4.x版本
                if (version_compare($swoole_version, '4.0.0', '>=') && version_compare($swoole_version, '5.0.0', '<')) {
                    echo "<div class='version'>✓ 满足要求的Swoole 4.x版本</div>";
                } else {
                    echo "<div class='version'>✗ 不满足Swoole 4.x版本要求</div>";
                }
            } else {
                // 其他扩展的版本信息
                $version = phpversion($ext_name);
                echo "<div class='version'>版本: {$version}</div>";
            }
        }
        
        echo "</div>";
    }
    ?>

    <div style="margin-top: 30px; text-align: center; color: #666;">
        <p>提示: 如需查看完整的PHP配置信息，请取消下方代码的注释。</p>
    </div>
    
    <?php
    /*
    // 如需查看完整的PHP配置信息，请取消下面这行的注释
    phpinfo();
    */
    
    // 结束脚本执行
exit;
    ?>
</body>
</html>