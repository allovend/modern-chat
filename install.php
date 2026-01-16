<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Chat - 安装向导</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .install-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .install-header {
            background: linear-gradient(135deg, #12b7f5 0%, #00a2e8 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }

        .install-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .install-header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .install-body {
            padding: 40px;
        }

        .step-nav {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .step-nav::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50px;
            right: 50px;
            height: 3px;
            background: #e0e0e0;
            z-index: 0;
        }

        .step-item {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }

        .step-number {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s;
        }

        .step-item.active .step-number {
            background: linear-gradient(135deg, #12b7f5 0%, #00a2e8 100%);
            color: white;
            box-shadow: 0 4px 10px rgba(18, 183, 245, 0.4);
        }

        .step-item.completed .step-number {
            background: #52c41a;
            color: white;
        }

        .step-label {
            font-size: 14px;
            color: #666;
        }

        .step-item.active .step-label {
            color: #12b7f5;
            font-weight: 600;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .welcome-content {
            text-align: center;
            padding: 20px 0;
        }

        .welcome-content h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .welcome-content p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .version-info {
            background: #f0f9ff;
            border-left: 4px solid #12b7f5;
            padding: 15px 20px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }

        .version-info p {
            margin: 5px 0;
            color: #555;
        }

        .check-list {
            list-style: none;
            margin: 20px 0;
        }

        .check-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            transition: background 0.2s;
        }

        .check-item:hover {
            background: #f9f9f9;
        }

        .check-item:last-child {
            border-bottom: none;
        }

        .check-icon {
            width: 24px;
            height: 24px;
            margin-right: 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .check-item.success .check-icon {
            background: #52c41a;
            color: white;
        }

        .check-item.error .check-icon {
            background: #ff4d4f;
            color: white;
        }

        .check-item.warning .check-icon {
            background: #faad14;
            color: white;
        }

        .check-info {
            flex: 1;
        }

        .check-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .check-detail {
            font-size: 13px;
            color: #999;
        }

        .check-message {
            font-size: 13px;
            margin-top: 5px;
        }

        .check-item.success .check-message {
            color: #52c41a;
        }

        .check-item.error .check-message {
            color: #ff4d4f;
        }

        .check-item.warning .check-message {
            color: #faad14;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #12b7f5;
            box-shadow: 0 0 0 3px rgba(18, 183, 245, 0.1);
        }

        .form-group .hint {
            font-size: 13px;
            color: #999;
            margin-top: 5px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #12b7f5 0%, #00a2e8 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(18, 183, 245, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(18, 183, 245, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #666;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-group .btn {
            min-width: 120px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #52c41a;
        }

        .alert-error {
            background: #fff2f0;
            border: 1px solid #ffccc7;
            color: #ff4d4f;
        }

        .alert-warning {
            background: #fffbe6;
            border: 1px solid #ffe58f;
            color: #faad14;
        }

        .alert-info {
            background: #e6f7ff;
            border: 1px solid #91d5ff;
            color: #1890ff;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .complete-content {
            text-align: center;
            padding: 20px 0;
        }

        .complete-icon {
            width: 80px;
            height: 80px;
            background: #52c41a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .complete-content h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .complete-content p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .complete-info {
            background: #f0f9ff;
            border-left: 4px solid #12b7f5;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }

        .complete-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .complete-info ul {
            list-style: none;
        }

        .complete-info li {
            padding: 8px 0;
            color: #555;
            border-bottom: 1px dashed #ddd;
        }

        .complete-info li:last-child {
            border-bottom: none;
        }

        .complete-info strong {
            color: #12b7f5;
        }

        @media (max-width: 768px) {
            .install-container {
                border-radius: 0;
            }

            .install-header {
                padding: 30px 20px;
            }

            .install-header h1 {
                font-size: 2em;
            }

            .install-body {
                padding: 20px;
            }

            .step-nav {
                margin-bottom: 30px;
            }

            .step-number {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }

            .step-label {
                font-size: 12px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .btn-group {
                flex-direction: column-reverse;
                gap: 10px;
            }

            .btn-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1>🚀 Modern Chat</h1>
            <p>现代化聊天系统安装向导</p>
        </div>

        <div class="install-body">
            <!-- 步骤导航 -->
            <div class="step-nav">
                <div class="step-item active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">欢迎</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">环境检测</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">数据库</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">完成</div>
                </div>
            </div>

            <!-- 消息提示 -->
            <div id="alert-box" class="alert"></div>

            <!-- 步骤1: 欢迎页 -->
            <div class="step-content active" id="step-1">
                <div class="welcome-content">
                    <h2>欢迎使用 Modern Chat 安装向导</h2>
                    <p>Modern Chat 是一个基于 PHP + MySQL 的现代化聊天系统，具有简洁的界面和丰富的功能。</p>
                    <p>本向导将帮助您完成以下配置：</p>
                    <ul style="text-align: left; margin: 20px 0; padding-left: 30px; color: #666; line-height: 2;">
                        <li>检查服务器环境是否符合要求</li>
                        <li>配置数据库连接信息</li>
                        <li>自动导入数据库表结构</li>
                        <li>完成系统初始化</li>
                    </ul>
                    <div class="version-info" id="version-info">
                        <p>正在加载版本信息...</p>
                    </div>
                    <p style="font-size: 13px; color: #999;">点击"下一步"开始安装流程</p>
                </div>
            </div>

            <!-- 步骤2: 环境检测 -->
            <div class="step-content" id="step-2">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="color: #333; font-size: 24px;">环境检测</h2>
                    <p style="color: #666;">正在检测您的服务器环境是否符合运行要求</p>
                </div>
                <ul class="check-list" id="env-check-list">
                    <li class="check-item">
                        <div class="check-icon">...</div>
                        <div class="check-info">
                            <div class="check-name">正在检测...</div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- 步骤3: 数据库配置 -->
            <div class="step-content" id="step-3">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: #333; font-size: 24px;">数据库配置</h2>
                    <p style="color: #666;">请填写您的MySQL数据库连接信息</p>
                </div>
                <form id="db-config-form">
                    <div class="form-group">
                        <label for="db-host">数据库服务器地址</label>
                        <input type="text" id="db-host" name="host" value="localhost" placeholder="例如: localhost">
                        <div class="hint">如果数据库和网站在同一台服务器上，通常填写 localhost 或 127.0.0.1</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="db-port">端口</label>
                            <input type="number" id="db-port" name="port" value="3306" placeholder="例如: 3306">
                        </div>
                        <div class="form-group">
                            <label for="db-name">数据库名称</label>
                            <input type="text" id="db-name" name="database" placeholder="例如: chat">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="db-user">数据库用户名</label>
                            <input type="text" id="db-user" name="username" placeholder="例如: root">
                        </div>
                        <div class="form-group">
                            <label for="db-pass">数据库密码</label>
                            <input type="password" id="db-pass" name="password" placeholder="请输入密码">
                        </div>
                    </div>
                </form>
            </div>

            <!-- 步骤4: 完成安装 -->
            <div class="step-content" id="step-4">
                <div class="complete-content">
                    <div class="complete-icon">✓</div>
                    <h2>🎉 安装完成！</h2>
                    <p>恭喜您！Modern Chat 已成功安装到您的服务器。</p>
                    <div class="complete-info">
                        <h3>后续操作</h3>
                        <ul>
                            <li><strong>删除安装锁</strong>：如需重新安装，请删除根目录下的 <code>installed.lock</code> 文件</li>
                            <li><strong>配置管理员</strong>：首次注册的用户将自动成为超级管理员</li>
                            <li><strong>访问系统</strong>：点击下方按钮进入聊天系统</li>
                            <li><strong>安全提示</strong>：建议安装完成后修改数据库密码</li>
                        </ul>
                    </div>
                    <p style="font-size: 13px; color: #999;">感谢您使用 Modern Chat！如有问题请访问项目主页获取支持</p>
                </div>
            </div>

            <!-- 按钮组 -->
            <div class="btn-group">
                <button type="button" class="btn btn-secondary" id="prev-btn" style="display: none;">
                    ← 上一步
                </button>
                <button type="button" class="btn btn-primary" id="next-btn">
                    下一步 →
                </button>
            </div>
        </div>
    </div>

    <script>
        // 当前步骤
        let currentStep = 1;
        const totalSteps = 4;

        // 环境检测结果
        let envCheckPassed = false;
        let dbConfig = {};

        // 获取DOM元素
        const alertBox = document.getElementById('alert-box');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        // 显示消息
        function showAlert(type, message) {
            alertBox.className = `alert alert-${type} show`;
            alertBox.textContent = message;

            if (type === 'success' || type === 'error') {
                setTimeout(() => {
                    alertBox.classList.remove('show');
                }, 3000);
            }
        }

        // 隐藏消息
        function hideAlert() {
            alertBox.classList.remove('show');
        }

        // 更新步骤导航
        function updateStepNav(step) {
            document.querySelectorAll('.step-item').forEach((item, index) => {
                const stepNum = index + 1;
                item.classList.remove('active', 'completed');

                if (stepNum < step) {
                    item.classList.add('completed');
                } else if (stepNum === step) {
                    item.classList.add('active');
                }
            });
        }

        // 显示指定步骤
        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`step-${step}`).classList.add('active');
            updateStepNav(step);
            currentStep = step;

            // 更新按钮状态
            prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
            
            if (step === totalSteps) {
                nextBtn.textContent = '进入系统 →';
                nextBtn.onclick = () => {
                    window.location.href = 'login.php';
                };
            } else if (step === 3) {
                nextBtn.textContent = '开始安装 →';
            } else {
                nextBtn.textContent = '下一步 →';
                nextBtn.onclick = handleNext;
            }
        }

        // 处理下一步
        function handleNext() {
            hideAlert();

            switch (currentStep) {
                case 1:
                    showStep(2);
                    checkEnvironment();
                    break;
                case 2:
                    if (!envCheckPassed) {
                        showAlert('error', '请先解决环境检测中的错误再继续');
                        return;
                    }
                    showStep(3);
                    break;
                case 3:
                    saveDatabaseConfig();
                    break;
            }
        }

        // 处理上一步
        prevBtn.onclick = () => {
            hideAlert();
            showStep(currentStep - 1);
        };

        // 获取版本信息
        function getVersionInfo() {
            fetch('install/install_api.php?action=get_version')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const info = data.data;
                        document.getElementById('version-info').innerHTML = `
                            <p><strong>当前版本：</strong>${info.version}</p>
                            <p><strong>发布日期：</strong>${info.release_date}</p>
                            <p><strong>PHP版本：</strong>${info.php_version}</p>
                        `;
                    }
                })
                .catch(err => {
                    console.error('获取版本信息失败:', err);
                });
        }

        // 环境检测
        function checkEnvironment() {
            showAlert('info', '正在进行环境检测...');
            nextBtn.disabled = true;

            fetch('install/install_api.php?action=check_environment')
                .then(res => res.json())
                .then(data => {
                    hideAlert();

                    if (data.success) {
                        const checks = data.data.checks;
                        const systemInfo = data.data.system_info;
                        envCheckPassed = data.data.all_passed;

                        // 显示检测结果
                        const checkList = document.getElementById('env-check-list');
                        checkList.innerHTML = '';

                        Object.entries(checks).forEach(([key, check]) => {
                            const statusClass = check.status ? 'success' : 'warning';
                            const icon = check.status ? '✓' : '!';

                            const li = document.createElement('li');
                            li.className = `check-item ${statusClass}`;
                            li.innerHTML = `
                                <div class="check-icon">${icon}</div>
                                <div class="check-info">
                                    <div class="check-name">${check.name}</div>
                                    <div class="check-detail">当前: ${check.current} | 要求: ${check.required}</div>
                                    <div class="check-message">${check.message}</div>
                                </div>
                            `;
                            checkList.appendChild(li);
                        });

                        // 显示系统信息
                        const sysInfo = document.createElement('li');
                        sysInfo.className = 'check-item success';
                        sysInfo.innerHTML = `
                            <div class="check-icon">ℹ</div>
                            <div class="check-info">
                                <div class="check-name">系统信息</div>
                                <div class="check-detail">PHP ${systemInfo.php_version} | ${systemInfo.server_software} | ${systemInfo.os}</div>
                            </div>
                        `;
                        checkList.appendChild(sysInfo);

                        if (!envCheckPassed) {
                            showAlert('error', '存在必须的环境要求未满足，请先解决以上问题');
                            nextBtn.disabled = false;
                        } else {
                            showAlert('success', '环境检测通过，可以进行下一步');
                            nextBtn.disabled = false;
                        }
                    } else {
                        showAlert('error', data.message);
                        nextBtn.disabled = false;
                    }
                })
                .catch(err => {
                    showAlert('error', '环境检测失败: ' + err.message);
                    nextBtn.disabled = false;
                });
        }

        // 保存数据库配置
        function saveDatabaseConfig() {
            const host = document.getElementById('db-host').value.trim();
            const port = document.getElementById('db-port').value.trim();
            const database = document.getElementById('db-name').value.trim();
            const username = document.getElementById('db-user').value.trim();
            const password = document.getElementById('db-pass').value;

            // 验证必填字段
            if (!host || !database || !username) {
                showAlert('error', '请填写完整的数据库配置信息');
                return;
            }

            dbConfig = { host, port, database, username, password };
            nextBtn.disabled = true;
            nextBtn.innerHTML = '<span class="loading"></span> 安装中...';

            // 先测试连接
            testDatabase();
        }

        // 测试数据库连接
        function testDatabase() {
            const formData = new URLSearchParams(dbConfig);

            fetch('install/install_api.php?action=test_db', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', '数据库连接成功，正在导入数据...');
                    importDatabase();
                } else {
                    showAlert('error', data.message);
                    nextBtn.disabled = false;
                    nextBtn.textContent = '开始安装 →';
                }
            })
            .catch(err => {
                showAlert('error', '数据库连接失败: ' + err.message);
                nextBtn.disabled = false;
                nextBtn.textContent = '开始安装 →';
            });
        }

        // 导入数据库
        function importDatabase() {
            const formData = new URLSearchParams(dbConfig);
            formData.append('overwrite', 'false');

            fetch('install/install_api.php?action=import_db', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    completeInstall();
                } else {
                    // 检查是否是数据冲突
                    if (data.data && data.data.conflict) {
                        if (confirm(data.data.message)) {
                            // 用户确认覆盖
                            const formData2 = new URLSearchParams(dbConfig);
                            formData2.append('overwrite', 'true');

                            return fetch('install/install_api.php?action=import_db', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: formData2
                            }).then(res => res.json());
                        } else {
                            showAlert('warning', '已取消导入，请修改数据库名称后重试');
                            nextBtn.disabled = false;
                            nextBtn.textContent = '开始安装 →';
                            return { success: false };
                        }
                    } else {
                        showAlert('error', data.message);
                        nextBtn.disabled = false;
                        nextBtn.textContent = '开始安装 →';
                    }
                }
            })
            .then(data => {
                if (data && data.success) {
                    completeInstall();
                }
            })
            .catch(err => {
                showAlert('error', '数据库导入失败: ' + err.message);
                nextBtn.disabled = false;
                nextBtn.textContent = '开始安装 →';
            });
        }

        // 完成安装
        function completeInstall() {
            fetch('install/install_api.php?action=complete_install', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', '安装完成！');
                    showStep(4);
                } else {
                    showAlert('error', data.message);
                    nextBtn.disabled = false;
                    nextBtn.textContent = '开始安装 →';
                }
            })
            .catch(err => {
                showAlert('error', '安装失败: ' + err.message);
                nextBtn.disabled = false;
                nextBtn.textContent = '开始安装 →';
            });
        }

        // 初始化
        window.onload = function() {
            getVersionInfo();
            nextBtn.onclick = handleNext;
        };
    </script>
</body>
</html>
