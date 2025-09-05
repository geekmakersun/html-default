<?php
// 仅允许本地IP访问
$allowed_ips = array('127.0.0.1', '::1');

// 检查是否在命令行环境中运行
if (php_sapi_name() !== 'cli') {
    $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    
    if (!in_array($client_ip, $allowed_ips)) {
        die("Access denied. This script can only be accessed from localhost.");
    }
}

// 检查Swoole扩展是否已加载
if (extension_loaded('swoole')) {
    echo "Swoole扩展已成功加载！\n";
    echo "PHP版本: " . phpversion() . "\n";
    echo "Swoole版本: " . phpversion('swoole') . "\n";
    
    // 尝试创建一个简单的Swoole对象来验证功能
    try {
        $server = new Swoole\Process(function() {
            echo "成功创建Swoole Process对象！\n";
        });
        echo "Swoole功能验证通过！\n";
    } catch (Exception $e) {
        echo "Swoole功能验证失败: " . $e->getMessage() . "\n";
    }
} else {
    echo "错误：Swoole扩展未加载！\n";
    echo "已加载的扩展列表：\n";
    print_r(get_loaded_extensions());
}