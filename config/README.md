# config.json使用说明

### 默认文件内容

```json
{
    "Create_a_group_chat_for_all_members": true,
    "Restrict_registration": true, 
    "Restrict_registration_ip": 3, 
    "ban_system": true, 
    "user_name_max": 12,
    "upload_files_max": 150,
    "Session_Duration": 1,
    "email_verify": false,
    "email_verify_api": "https:\/\/api.nbhao.org\/v1\/email\/verify",
    "email_verify_api_Request": "POST",
    "email_verify_api_Verify_parameters": "message.result"
}
```

### 字段详解


- `Create_a_group_chat_for_all_members`: 你希望用户注册后自动创建一个群聊，群聊名称为所有用户的用户名拼接，例如：user1user2user3
- `Restrict_registration`: 是否限制注册（如果配置了这个必须配置下面的Restrict_registration_ip的数量）
- `Restrict_registration_ip`: 你希望一个IP地址最多注册几个账号
- `user_name_max`: 你希望最大设置的用户名长度
- `ban_system`: 是否启用封禁系统
- `upload_files_max`: 你希望用户最大可发送的文件大小（MB）
- `Session_Duration`: 用户会话时长（小时），默认1小时
- `email_verify`: 是否启用邮箱验证（默认关闭）
- `email_verify_api`: 邮箱验证 API 地址
- `email_verify_api_Request`: 邮箱验证 API 请求方法（GET 或 POST）
- `email_verify_api_Verify_parameters`: 邮箱验证 API 返回结果参数名（例如："message.result"）
