<?php
// 运行创建封禁表的SQL脚本

// 包含必要的文件
require_once 'config.php';
require_once 'db.php';

try {
    // 读取SQL文件内容
    $sql_content = file_get_contents('create_ban_table.sql');
    
    // 执行SQL语句
    $conn->exec($sql_content);
    
    echo "封禁表创建成功！";
} catch (PDOException $e) {
    echo "错误：" . $e->getMessage();
}

// 关闭数据库连接
$conn = null;
?>