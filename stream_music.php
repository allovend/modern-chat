<?php
require_once 'security_check.php';
require_once 'config.php';

// 检查是否登�?
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$filename = isset($_GET['file']) ? $_GET['file'] : '';
if (empty($filename)) {
    http_response_code(400);
    exit;
}

// 安全检查，防止目录遍历
$filename = basename($filename);
$filepath = __DIR__ . '/new_music/' . $filename;

if (file_exists($filepath)) {
    // 获取文件扩展�?    
$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    
    // 根据扩展名确�?MIME 类型，避免使�?mime_content_type 导致 500 错误
    $mime_types = [
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
        'ogg' => 'audio/ogg',
        'wav' => 'audio/wav',
        'flac' => 'audio/flac',
        'webm' => 'audio/webm'
    ];
    
    $mime_type = isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
    
    // 如果扩展名未匹配，尝试使�?mime_content_type 作为备选，并添加错误抑�?    
if ($mime_type === 'application/octet-stream' && function_exists('mime_content_type')) {
        $detected_type = @mime_content_type($filepath);
        if ($detected_type) {
            $mime_type = $detected_type;
        }
    }

    // 关闭输出缓冲和压缩，确保流式传输正常
    @ini_set('zlib.output_compression', 'Off');
    @ini_set('output_buffering', 'Off');
    @ini_set('output_handler', '');
    
    // 清除之前的缓冲区
    while (ob_get_level()) {
        ob_end_clean();
    }

    $filesize = filesize($filepath);
    
    // 设置缓存�?    
header('Cache-Control: public, max-age=31536000');
    header('Content-Disposition: inline; filename="' . basename($filename) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    
    // 支持 Range 请求 (流式传输)
    $range = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : null;
    
    if ($range) {
        $partial = true;
        list($param, $range) = explode('=', $range);
        if (strtolower(trim($param)) !== 'bytes') {
            http_response_code(400);
            exit;
        }
        
        $range = explode('-', $range);
        $start = intval($range[0]);
        $end = isset($range[1]) && is_numeric($range[1]) ? intval($range[1]) : $filesize - 1;
        
        if ($start > $end || $start >= $filesize) {
            http_response_code(416);
            header("Content-Range: bytes */$filesize");
            exit;
        }
        
        $length = $end - $start + 1;
        
        http_response_code(206);
        header('Content-Type: ' . $mime_type);
        header("Content-Range: bytes $start-$end/$filesize");
        header("Content-Length: $length");
        
        $fp = fopen($filepath, 'rb');
        fseek($fp, $start);
        
        // 分块输出
        $buffer = 1024 * 8;
        while (!feof($fp) && ($p = ftell($fp)) <= $end) {
            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            echo fread($fp, $buffer);
            flush();
        }
        fclose($fp);
        exit;
    } else {
        // 不带 Range 的请求，返回完整文件
        http_response_code(200);
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . $filesize);
        readfile($filepath);
        exit;
    }
} else {
    http_response_code(404);
}
?>