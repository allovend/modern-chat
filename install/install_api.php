<?php
/**
 * 安装API接口
 * 处理所有安装相关的AJAX请求
 */

// 引入工具类
require_once __DIR__ . '/utils/Common.php';
require_once __DIR__ . '/utils/Environment.php';
require_once __DIR__ . '/utils/Database.php';

// 设置错误处理
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 获取请求参数
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// 根据action分发请求
switch ($action) {
    case 'check_environment':
        if ($method !== 'GET') {
            InstallCommon::jsonResponse(false, '请求方法错误');
        }
        checkEnvironment();
        break;

    case 'test_db':
        if ($method !== 'POST') {
            InstallCommon::jsonResponse(false, '请求方法错误');
        }
        testDatabase();
        break;

    case 'import_db':
        if ($method !== 'POST') {
            InstallCommon::jsonResponse(false, '请求方法错误');
        }
        importDatabase();
        break;

    case 'complete_install':
        if ($method !== 'POST') {
            InstallCommon::jsonResponse(false, '请求方法错误');
        }
        completeInstall();
        break;

    case 'get_version':
        if ($method !== 'GET') {
            InstallCommon::jsonResponse(false, '请求方法错误');
        }
        getVersionInfo();
        break;

    default:
        InstallCommon::jsonResponse(false, '无效的操作');
}

/**
 * 检查环境
 */
function checkEnvironment() {
    try {
        $checks = InstallEnvironment::checkRequirements();
        $systemInfo = InstallEnvironment::getSystemInfo();

        InstallCommon::jsonResponse(true, '环境检测完成', [
            'checks' => $checks,
            'system_info' => $systemInfo,
            'all_passed' => InstallEnvironment::allPassed($checks)
        ]);
    } catch (Exception $e) {
        InstallCommon::jsonResponse(false, '环境检测失败: ' . $e->getMessage());
    }
}

/**
 * 测试数据库连接
 */
function testDatabase() {
    try {
        $host = $_POST['host'] ?? '';
        $port = $_POST['port'] ?? 3306;
        $database = $_POST['database'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // 验证必填字段
        if (empty($host) || empty($database) || empty($username)) {
            InstallCommon::jsonResponse(false, '请填写完整的数据库配置');
        }

        $db = new InstallDatabase();
        $db->setConfig($host, $port, $database, $username, $password);
        $db->testConnection();

        $dbExists = $db->databaseExists();
        $dbVersion = $db->getDatabaseVersion();

        InstallCommon::jsonResponse(true, '数据库连接成功', [
            'db_exists' => $dbExists,
            'db_version' => $dbVersion
        ]);
    } catch (Exception $e) {
        InstallCommon::jsonResponse(false, $e->getMessage());
    }
}

/**
 * 导入数据库
 */
function importDatabase() {
    try {
        $host = $_POST['host'] ?? '';
        $port = $_POST['port'] ?? 3306;
        $database = $_POST['database'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $overwrite = $_POST['overwrite'] ?? 'false';

        // 验证必填字段
        if (empty($host) || empty($database) || empty($username)) {
            InstallCommon::jsonResponse(false, '请填写完整的数据库配置');
        }

        $db = new InstallDatabase();
        $db->setConfig($host, $port, $database, $username, $password);
        $db->testConnection();

        // 检查数据库是否存在，不存在则创建
        if (!$db->databaseExists()) {
            $db->createDatabase();
        }

        // 检查是否有表
        $hasTables = $db->hasTables();
        if ($hasTables && $overwrite !== 'true') {
            InstallCommon::jsonResponse(false, '数据库已存在数据表', [
                'conflict' => true,
                'message' => '数据库中已存在数据表，是否要清空并重新导入？'
            ]);
        }

        // 如果需要覆盖，先清空数据库
        if ($hasTables && $overwrite === 'true') {
            $db->clearDatabase();
        }

        // 导入SQL文件
        $sqlFile = dirname(__DIR__) . '/db.sql';
        $db->importSql($sqlFile);

        // 更新配置文件
        $configFile = dirname(__DIR__) . '/config.php';
        $db->updateConfigFile($configFile);

        InstallCommon::jsonResponse(true, '数据库导入成功', [
            'tables_imported' => true
        ]);
    } catch (Exception $e) {
        InstallCommon::jsonResponse(false, '数据库导入失败: ' . $e->getMessage());
    }
}

/**
 * 完成安装
 */
function completeInstall() {
    try {
        // 创建安装锁
        $success = InstallCommon::createInstallLock();

        if (!$success) {
            throw new Exception('创建安装锁失败');
        }

        InstallCommon::jsonResponse(true, '安装完成', [
            'lock_created' => true
        ]);
    } catch (Exception $e) {
        InstallCommon::jsonResponse(false, '安装失败: ' . $e->getMessage());
    }
}

/**
 * 获取版本信息
 */
function getVersionInfo() {
    try {
        $version = '2.1.0';
        $releaseDate = '2025-01-16';

        InstallCommon::jsonResponse(true, '获取版本信息成功', [
            'version' => $version,
            'release_date' => $releaseDate,
            'php_version' => PHP_VERSION
        ]);
    } catch (Exception $e) {
        InstallCommon::jsonResponse(false, '获取版本信息失败: ' . $e->getMessage());
    }
}
