<?php
/**
 * Modern Chat API
 * 
 * 统一API入口，处理所有客户端请求。
 * 采用 RESTful 风格设计（尽管使用 POST 参数模拟资源和动作）。
 * 
 * 使用说明:
 * 1. 所有请求均应发送至 api.php
 * 2. 请求方法推荐使用 POST
 * 3. 必须包含 'resource' 和 'action' 参数
 * 4. 数据可以通过 JSON Body 或 POST 表单提交
 * 5. 身份验证基于 Session Cookie
 */

// 开启错误日志，关闭页面错误显示
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'api_error.log');
error_reporting(E_ALL);

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // 生产环境建议修改为指定域名
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// 处理 OPTIONS 预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 启动 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 包含核心文件
require_once 'config.php';
require_once 'db.php';
require_once 'User.php';
require_once 'Friend.php';
require_once 'Message.php';
require_once 'Group.php';
require_once 'FileUpload.php';

// 初始化核心服务类
try {
    $user = new User($conn);
    $friend = new Friend($conn);
    $message = new Message($conn);
    $group = new Group($conn);
    $fileUpload = new FileUpload($conn);
} catch (Exception $e) {
    response_error('服务器初始化失败: ' . $e->getMessage(), 500);
}

// ==========================================
// 辅助函数
// ==========================================

/**
 * 返回成功响应
 */
function response_success($data = [], $message = '操作成功') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 返回错误响应
 */
function response_error($message = '操作失败', $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 验证用户是否登录
 */
function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        response_error('未登录或会话已过期', 401);
    }
    return $_SESSION['user_id'];
}

/**
 * 获取请求数据 (兼容 JSON 和 Form Data)
 */
function get_request_data() {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($content_type, 'application/json') !== false) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
    
    return $_POST;
}

// ==========================================
// 请求处理逻辑
// ==========================================

try {
    $data = get_request_data();
    $resource = $data['resource'] ?? '';
    $action = $data['action'] ?? '';
    
    // 全局参数
    $id = $data['id'] ?? null;

    // 路由分发
    switch ($resource) {
        // ------------------------------------------
        // 认证模块 (Auth)
        // ------------------------------------------
        case 'auth':
            switch ($action) {
                case 'login':
                    $email = trim($data['email'] ?? '');
                    $password = $data['password'] ?? '';
                    
                    if (empty($email) || empty($password)) {
                        response_error('邮箱和密码不能为空');
                    }
                    
                    $result = $user->login($email, $password);
                    if ($result['success']) {
                        $_SESSION['user_id'] = $result['user']['id'];
                        $_SESSION['username'] = $result['user']['username'];
                        $_SESSION['email'] = $result['user']['email'];
                        
                        // 移除敏感信息
                        unset($result['user']['password']);
                        
                        // 更新状态为在线
                        $user->updateStatus($result['user']['id'], 'online');
                        
                        response_success($result['user'], '登录成功');
                    } else {
                        response_error($result['message']);
                    }
                    break;
                    
                case 'register':
                    $username = trim($data['username'] ?? '');
                    $email = trim($data['email'] ?? '');
                    $password = $data['password'] ?? '';
                    $phone = trim($data['phone'] ?? ''); // 支持手机号
                    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
                    
                    if (empty($username) || empty($email) || empty($password)) {
                        response_error('用户名、邮箱和密码不能为空');
                    }
                    
                    $result = $user->register($username, $email, $password, $phone, $ip_address);
                    if ($result['success']) {
                        response_success(['user_id' => $result['user_id']], '注册成功');
                    } else {
                        response_error($result['message']);
                    }
                    break;
                    
                case 'logout':
                    if (isset($_SESSION['user_id'])) {
                        $user->updateStatus($_SESSION['user_id'], 'offline');
                    }
                    session_unset();
                    session_destroy();
                    response_success([], '退出成功');
                    break;
                    
                case 'check_status':
                    if (isset($_SESSION['user_id'])) {
                        response_success(['is_logged_in' => true, 'user_id' => $_SESSION['user_id']]);
                    } else {
                        response_success(['is_logged_in' => false]);
                    }
                    break;
                    
                default:
                    response_error("Auth 模块不支持操作: $action");
            }
            break;

        // ------------------------------------------
        // 用户模块 (User)
        // ------------------------------------------
        case 'user':
            $current_user_id = check_auth();
            
            switch ($action) {
                case 'get_info':
                    // 默认获取当前用户，也可获取指定用户
                    $target_user_id = $data['user_id'] ?? $current_user_id;
                    $user_info = $user->getUserById($target_user_id);
                    
                    if ($user_info) {
                        unset($user_info['password']);
                        response_success($user_info);
                    } else {
                        response_error('用户不存在', 404);
                    }
                    break;
                    
                case 'update_info':
                    // 更新当前用户信息
                    // 允许更新的字段应在 User::updateUser 中控制
                    $update_data = $data;
                    unset($update_data['resource'], $update_data['action'], $update_data['id']); // 移除控制参数
                    
                    if ($user->updateUser($current_user_id, $update_data)) {
                        // 更新 Session 中的信息（如果修改了）
                        $updated_user = $user->getUserById($current_user_id);
                        $_SESSION['username'] = $updated_user['username'];
                        $_SESSION['email'] = $updated_user['email'];
                        
                        response_success([], '个人信息更新成功');
                    } else {
                        response_error('更新失败或没有数据变更');
                    }
                    break;
                
                case 'search':
                    $keyword = trim($data['q'] ?? '');
                    if (empty($keyword)) {
                        response_error('搜索关键词不能为空');
                    }
                    $users = $user->searchUsers($keyword, $current_user_id);
                    response_success($users);
                    break;
                    
                default:
                    response_error("User 模块不支持操作: $action");
            }
            break;

        // ------------------------------------------
        // 好友模块 (Friends)
        // ------------------------------------------
        case 'friends':
            $current_user_id = check_auth();
            
            switch ($action) {
                case 'list':
                    $friends = $friend->getFriends($current_user_id);
                    response_success($friends);
                    break;
                    
                case 'send_request':
                    $friend_id = $data['friend_id'] ?? 0;
                    if (empty($friend_id)) response_error('好友ID不能为空');
                    
                    $result = $friend->sendFriendRequest($current_user_id, $friend_id);
                    if ($result['success']) {
                        response_success([], $result['message']);
                    } else {
                        response_error($result['message']);
                    }
                    break;
                    
                case 'delete':
                    $friend_id = $data['friend_id'] ?? 0;
                    if (empty($friend_id)) response_error('好友ID不能为空');
                    
                    $result = $friend->deleteFriend($current_user_id, $friend_id);
                    if ($result['success']) {
                        response_success([], $result['message']);
                    } else {
                        response_error($result['message']);
                    }
                    break;
                
                // 好友请求管理
                case 'get_requests':
                    $requests = $friend->getPendingRequests($current_user_id);
                    response_success($requests);
                    break;
                    
                case 'accept_request':
                    $request_id = $data['request_id'] ?? 0;
                    if (empty($request_id)) response_error('请求ID不能为空');
                    
                    $result = $friend->acceptFriendRequest($current_user_id, $request_id);
                    if ($result['success']) {
                        response_success([], $result['message']);
                    } else {
                        response_error($result['message']);
                    }
                    break;
                    
                case 'reject_request':
                    $request_id = $data['request_id'] ?? 0;
                    if (empty($request_id)) response_error('请求ID不能为空');
                    
                    $result = $friend->rejectFriendRequest($current_user_id, $request_id);
                    if ($result['success']) {
                        response_success([], $result['message']);
                    } else {
                        response_error($result['message']);
                    }
                    break;

                default:
                    response_error("Friends 模块不支持操作: $action");
            }
            break;

        // ------------------------------------------
        // 消息模块 (Messages)
        // ------------------------------------------
        case 'messages':
            $current_user_id = check_auth();
            
            switch ($action) {
                case 'history':
                    $friend_id = $data['friend_id'] ?? 0;
                    if (empty($friend_id)) response_error('好友ID不能为空');
                    
                    $messages = $message->getChatHistory($current_user_id, $friend_id);
                    response_success($messages);
                    break;
                    
                case 'send':
                    $receiver_id = $data['receiver_id'] ?? 0;
                    $content = trim($data['content'] ?? '');
                    
                    if (empty($receiver_id)) response_error('接收者ID不能为空');
                    if (empty($content)) response_error('消息内容不能为空');
                    
                    $result = $message->sendTextMessage($current_user_id, $receiver_id, $content);
                    if ($result['success']) {
                        response_success(['message_id' => $result['message_id']], '消息发送成功');
                    } else {
                        response_error('消息发送失败');
                    }
                    break;
                    
                default:
                    response_error("Messages 模块不支持操作: $action");
            }
            break;

        // ------------------------------------------
        // 群组模块 (Groups)
        // ------------------------------------------
        case 'groups':
            $current_user_id = check_auth();
            
            switch ($action) {
                case 'list':
                    $groups = $group->getUserGroups($current_user_id);
                    response_success($groups);
                    break;
                    
                case 'info':
                    $group_id = $data['group_id'] ?? 0;
                    if (empty($group_id)) response_error('群聊ID不能为空');
                    
                    $group_info = $group->getGroupInfo($group_id);
                    if ($group_info) {
                        response_success($group_info);
                    } else {
                        response_error('群聊不存在', 404);
                    }
                    break;
                    
                case 'create':
                    $name = trim($data['name'] ?? '');
                    $member_ids = $data['member_ids'] ?? [];
                    
                    if (empty($name)) response_error('群聊名称不能为空');
                    
                    $group_id = $group->createGroup($current_user_id, $name, $member_ids);
                    if ($group_id) {
                        response_success(['group_id' => $group_id], '群聊创建成功');
                    } else {
                        response_error('群聊创建失败');
                    }
                    break;
                
                // 群成员管理
                case 'members':
                    $group_id = $data['group_id'] ?? 0;
                    if (empty($group_id)) response_error('群聊ID不能为空');
                    
                    $members = $group->getGroupMembers($group_id);
                    response_success($members);
                    break;
                    
                case 'add_members':
                    $group_id = $data['group_id'] ?? 0;
                    $member_ids = $data['member_ids'] ?? [];
                    
                    if (empty($group_id) || empty($member_ids)) response_error('参数不完整');
                    
                    if ($group->addGroupMembers($group_id, $member_ids)) {
                        response_success([], '成员添加成功');
                    } else {
                        response_error('成员添加失败');
                    }
                    break;

                // 群消息
                case 'messages':
                    $group_id = $data['group_id'] ?? 0;
                    if (empty($group_id)) response_error('群聊ID不能为空');
                    
                    $messages = $group->getGroupMessages($group_id, $current_user_id);
                    response_success($messages);
                    break;
                    
                case 'send_message':
                    $group_id = $data['group_id'] ?? 0;
                    $content = trim($data['content'] ?? '');
                    
                    if (empty($group_id)) response_error('群聊ID不能为空');
                    if (empty($content)) response_error('消息内容不能为空');
                    
                    $result = $group->sendGroupMessage($group_id, $current_user_id, $content);
                    if ($result['success']) {
                        response_success(['message_id' => $result['message_id']], '消息发送成功');
                    } else {
                        response_error($result['message'] ?? '消息发送失败');
                    }
                    break;

                default:
                    response_error("Groups 模块不支持操作: $action");
            }
            break;

        // ------------------------------------------
        // 文件上传模块 (Upload)
        // ------------------------------------------
        case 'upload':
            $current_user_id = check_auth();
            
            if (!isset($_FILES['file'])) {
                response_error('请选择要上传的文件');
            }
            
            $upload_result = $fileUpload->upload($_FILES['file'], $current_user_id);
            if ($upload_result['success']) {
                response_success($upload_result, '文件上传成功');
            } else {
                response_error($upload_result['message']);
            }
            break;

        // ------------------------------------------
        // 默认处理
        // ------------------------------------------
        default:
            response_error('无效的 API 资源 (Resource)', 404);
    }

} catch (Exception $e) {
    // 捕获未处理的异常，防止敏感信息泄露
    error_log("API 异常: " . $e->getMessage());
    response_error('服务器内部错误', 500);
}

// 关闭数据库连接
$conn = null;
