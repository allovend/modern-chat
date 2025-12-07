<?php
require_once 'config.php';
require_once 'db.php';
require_once 'User.php';

// 检查是否是POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// 获取用户IP地址
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$user_ip = getUserIP();

// 检查是否启用了IP注册限制
$restrict_registration = getConfig('Restrict_registration', false);
$restrict_registration_ip = getConfig('Restrict_registration_ip', 3);

if ($restrict_registration) {
    // 检查该IP地址已经注册的用户数量
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM ip_registrations WHERE ip_address = ?");
    $stmt->execute([$user_ip]);
    $result = $stmt->fetch();
    
    if ($result['count'] >= $restrict_registration_ip) {
        // 超过限制，拒绝注册
        header("Location: register.php?error=" . urlencode("该IP地址已超过注册限制，最多只能注册{$restrict_registration_ip}个账号"));
        exit;
    }
    
    // 检查该IP地址是否已经有用户登录过
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT ir.user_id) as count FROM ip_registrations ir
                           JOIN users u ON ir.user_id = u.id
                           WHERE ir.ip_address = ? AND u.last_active > u.created_at");
    $stmt->execute([$user_ip]);
    $login_result = $stmt->fetch();
    
    if ($login_result['count'] > 0) {
        // 该IP地址已经有用户登录过，拒绝注册
        header("Location: register.php?error=" . urlencode("该IP地址已经有用户登录过，禁止继续注册"));
        exit;
    }
}

// 获取表单数据
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// 验证表单数据
$errors = [];

// 获取用户名最大长度配置
$user_name_max = getUserNameMaxLength();

if (strlen($username) < 3 || strlen($username) > $user_name_max) {
    $errors[] = "用户名长度必须在3-{$user_name_max}个字符之间";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = '请输入有效的邮箱地址';
}

if (strlen($password) < 6) {
    $errors[] = '密码长度必须至少为6个字符';
}

if ($password !== $confirm_password) {
    $errors[] = '两次输入的密码不一致';
}

// 如果有错误，重定向回注册页面
if (!empty($errors)) {
    $error_message = implode('<br>', $errors);
    header("Location: register.php?error=" . urlencode($error_message));
    exit;
}

// 创建User实例
$user = new User($conn);

// 尝试注册用户，传入IP地址
$result = $user->register($username, $email, $password, $user_ip);

if ($result['success']) {
    // 注册成功，将用户添加到所有全员群聊
    require_once 'Group.php';
    $group = new Group($conn);
    $group->addUserToAllUserGroups($result['user_id']);
    
    // 自动添加Admin管理员为好友并自动通过
    require_once 'Friend.php';
    $friend = new Friend($conn);
    
    // 获取Admin用户的ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'Admin' OR username = 'admin' LIMIT 1");
    $stmt->execute();
    $admin_user = $stmt->fetch();
    
    if ($admin_user) {
        $admin_id = $admin_user['id'];
        $new_user_id = $result['user_id'];
        
        // 检查是否已经是好友
        if (!$friend->isFriend($new_user_id, $admin_id)) {
            // 直接创建好友关系，跳过请求步骤
            try {
                // 创建正向关系
                $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'accepted')");
                $stmt->execute([$new_user_id, $admin_id]);
                
                // 创建反向关系
                $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'accepted')");
                $stmt->execute([$admin_id, $new_user_id]);
            } catch (PDOException $e) {
                error_log("自动添加Admin好友失败: " . $e->getMessage());
            }
        }
    }
    
    // 注册成功，重定向到登录页面
    header("Location: login.php?success=" . urlencode('注册成功，请登录'));
    exit;
} else {
    // 注册失败，重定向回注册页面
    header("Location: register.php?error=" . urlencode($result['message']));
    exit;
}