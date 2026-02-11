<?php
require_once 'security_check.php';
header('Content-Type: application/json');
require_once 'config.php';

$playlist_name = isset($_GET['name']) ? $_GET['name'] : '';
$config_file = __DIR__ . '/config/song_config.json';

if (!file_exists($config_file) || empty($playlist_name)) {
    echo json_encode([]);
    exit;
}

$config = json_decode(file_get_contents($config_file), true);

if (!isset($config[$playlist_name])) {
    echo json_encode([]);
    exit;
}

$settings = $config[$playlist_name];
$type = $settings['type'];
$data = $settings['data'];

$music_list = [];

if ($type === 'local') {
    // 本地模式：扫描目�?    // 确保目录安全，防止遍�?    
$base_dir = __DIR__ . '/';
    $target_dir = realpath($base_dir . $data);
    
    if ($target_dir && strpos($target_dir, $base_dir) === 0 && is_dir($target_dir)) {
        $files = scandir($target_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $file_path = $target_dir . '/' . $file;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (in_array($ext, ['mp3', 'm4a', 'flac', 'wav', 'ogg'])) {
                // 尝试解析元数�?                
$title = pathinfo($file, PATHINFO_FILENAME);
                $artist = '未知歌手';
                $cover = 'assets/default_music_cover.png'; // 默认封面
                
                // 这里简单处理，如果有getID3库可以用库解�?                // 为了兼容现有逻辑，我们生成类似get_music_list.php的结�?                // 假设封面接口支持通过文件名获取：get_music_cover.php?file=...
                // 但get_music_cover.php默认是去new_music找�?                // 我们可能需要修改get_music_cover.php或者直接返回默认封�?                // 这里的路径需要是相对于web根目录的
                $web_path = str_replace('\\', '/', substr($file_path, strlen($base_dir)));
                
                $music_list[] = [
                    'title' => $title,
                    'artist' => $artist,
                    'url' => $web_path,
                    'cover' => $cover, // 暂时使用默认封面，或者需要扩展封面获取逻辑
                    'lrc' => ''
                ];
            }
        }
    }
} elseif ($type === 'url') {
    // 链接模式：直接返回列�?    
if (is_array($data)) {
        foreach ($data as $index => $url) {
            $url = trim($url);
            if (empty($url)) continue;
            
            // 尝试从URL获取文件名作为标�?            
$filename = basename(parse_url($url, PHP_URL_PATH));
            $title = $filename ? urldecode(pathinfo($filename, PATHINFO_FILENAME)) : "Track " . ($index + 1);
            
            $music_list[] = [
                'title' => $title,
                'artist' => '网络歌曲',
                'url' => $url,
                'cover' => 'assets/default_music_cover.png',
                'lrc' => ''
            ];
        }
    }
}

echo json_encode($music_list);
