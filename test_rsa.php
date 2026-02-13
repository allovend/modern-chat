<?php
/**
 * RSA 加密测试脚本
 */
require_once 'RSAUtil.php';

echo "=== RSA 加密测试 ===\n\n";

try {
    // 创建 RSA 工具实例
    $rsaUtil = new RSAUtil();
    echo "[✓] RSA 工具初始化成功\n";
    
    // 获取公钥
    $publicKey = $rsaUtil->getPublicKey();
    echo "[✓] 公钥已加载\n";
    
    // 获取用于前端的公钥
    $publicKeyForJS = $rsaUtil->getPublicKeyForJS();
    echo "[✓] 前端公钥已生成\n";
    echo "    公钥长度: " . strlen($publicKeyForJS) . " 字符\n";
    
    // 测试加密和解密
    $testPassword = "TestPassword123!";
    echo "\n--- 加密/解密测试 ---\n";
    echo "原始密码: $testPassword\n";
    
    // 加密
    $encrypted = $rsaUtil->encrypt($testPassword);
    echo "加密后: " . substr($encrypted, 0, 50) . "...\n";
    
    // 解密
    $decrypted = $rsaUtil->decrypt($encrypted);
    if ($decrypted === $testPassword) {
        echo "[✓] 解密成功，密码匹配!\n";
    } else {
        echo "[✗] 解密失败，密码不匹配!\n";
        echo "解密结果: $decrypted\n";
    }
    
    // 显示公钥信息
    echo "\n--- 公钥信息 ---\n";
    echo "用于前端的公钥 (前100字符):\n";
    echo substr($publicKeyForJS, 0, 100) . "...\n";
    
    echo "\n=== 测试完成 ===\n";
    
} catch (Exception $e) {
    echo "[✗] 错误: " . $e->getMessage() . "\n";
}
