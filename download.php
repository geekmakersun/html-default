<?php
$url = $_GET['url'] ?? '';
if (empty($url)) {
    echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图片下载工具</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        button {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-view {
            background-color: #4CAF50;
            color: white;
        }
        .btn-view:hover {
            background-color: #45a049;
        }
        .btn-download {
            background-color: #2196F3;
            color: white;
        }
        .btn-download:hover {
            background-color: #0b7dda;
        }
        .preview {
            margin-top: 30px;
            text-align: center;
        }
        .preview img {
            max-width: 100%;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .error {
            color: #f44336;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 4px;
            margin-top: 20px;
        }
        .success {
            color: #4CAF50;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>图片下载工具</h1>
        <form method="GET">
            <div class="form-group">
                <label for="url">图片URL地址：</label>
                <input type="text" id="url" name="url" placeholder="https://example.com/image.jpg" required>
            </div>
            <div class="btn-group">
                <button type="submit" name="action" value="view" class="btn-view">预览图片</button>
                <button type="submit" name="action" value="download" class="btn-download">下载图片</button>
            </div>
        </form>
    </div>
</body>
</html>';
    exit;
}

$action = $_GET['action'] ?? 'view';

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Referer: ' . $url,
            'Accept: image/*'
        ],
        'timeout' => 30,
        'follow_location' => true,
        'max_redirects' => 5
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];

$context = stream_context_create($opts);
$imageData = @file_get_contents($url, false, $context);

if ($imageData === false) {
    $httpCode = 0;
    $contentType = '';
} else {
    $httpCode = 200;
    $contentType = 'image/jpeg';
    $headers = get_headers($url, 1, $context);
    if ($headers !== false) {
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'content-type') {
                $contentType = is_array($value) ? end($value) : $value;
                break;
            }
        }
    }
}

if ($httpCode != 200 || empty($imageData)) {
    echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图片下载工具</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error {
            color: #f44336;
            padding: 15px;
            background-color: #ffebee;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        a {
            color: #2196F3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error">
            <strong>错误：</strong> 无法获取图片资源 (HTTP状态码: ' . $httpCode . ')<br>
            <small>请检查URL是否正确，或者该资源是否需要特殊权限访问。</small>
        </div>
        <p><a href="?">返回</a></p>
    </div>
</body>
</html>';
    exit;
}

if ($action == 'download') {
    $filename = basename(parse_url($url, PHP_URL_PATH));
    if (empty($filename) || strpos($filename, '.') === false) {
        $filename = 'image.' . str_replace('image/', '', $contentType);
    }
    
    $customFilename = $_GET['filename'] ?? '';
    if (!empty($customFilename)) {
        $filename = $customFilename;
        if (strpos($filename, '.') === false) {
            $extension = pathinfo(basename(parse_url($url, PHP_URL_PATH)), PATHINFO_EXTENSION);
            if (!empty($extension)) {
                $filename .= '.' . $extension;
            } else {
                $filename .= '.' . str_replace('image/', '', $contentType);
            }
        }
    }
    
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($imageData));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo $imageData;
    exit;
}

echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>图片预览</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e3f2fd;
            border-radius: 4px;
        }
        .preview {
            text-align: center;
            margin: 20px 0;
        }
        .preview img {
            max-width: 100%;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0b7dda;
        }
        .back-link {
            display: inline-block;
            margin-right: 15px;
            color: #555;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="?" class="back-link">← 返回</a>
        <div class="info">
            <strong>URL：</strong> ' . htmlspecialchars($url) . '<br>
            <strong>类型：</strong> ' . htmlspecialchars($contentType) . '<br>
            <strong>大小：</strong> ' . number_format(strlen($imageData)) . ' 字节
        </div>
        <div class="preview">
            <img src="data:' . $contentType . ';base64,' . base64_encode($imageData) . '" alt="预览图片">
        </div>
        <form method="GET" style="margin-top: 20px;">
            <input type="hidden" name="url" value="' . htmlspecialchars($url) . '">
            <input type="hidden" name="action" value="download">
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="filename" placeholder="文件名（留空使用默认）" style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <button type="submit" class="btn">下载图片</button>
            </div>
        </form>
    </div>
</body>
</html>';
