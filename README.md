# Yuntongxun SMS SDK for PHP

[容联云通讯](https://www.yuntongxun.com) SDK

发布说明
# v1.0.0

发布日期: 2020-07-15

功能说明：
- 提供发送模板短信功能。

## 目录结构
```
php-sms-sdk
│ readme.md
├─demo
│      SendTemplateSMS.php    -- 发送短信示例
│
├─SDK
│  │  SmsSDK.php          -- 短信SDK
```
--------------------------------
## 使用示例
```php
    include_once("../SDK/SmsSDK.php");
    
    /**
     * 发送模板短信
     * @param int|string $to 短信接收彿手机号码集合,用英文逗号分开
     * @param array $dataAry 内容数据
     * @param int|string $templateId 模板Id
     */
    function sendTemplateSMS($to, $dataAry, $templateId)
    {
        // 主帐号
        $accountSid = '';
        // 主帐号Token
        $accountToken = '';
        // 应用Id
        $appId = '';
        // 请求地址，格式如下，不需要写https://
        $serverHost = 'app.cloopen.com';
        // 请求端口
        $serverPort = '8883';
        // REST版本号
        $apiVersion = '2013-12-26';
    
        // 初始化REST SDK
        $rest = new REST($serverHost, $serverPort, $apiVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);
    
        // 发送模板短信
        echo "Sending TemplateSMS to {$to} <br/>";
        $result = $rest->sendTemplateSMS($to, $dataAry, $templateId);
        if ($result == NULL) {
            echo "result error!<br>";
        }
        if ($result->statusCode != 0) {
            echo "error code: {$result->statusCode}<br>; error msg: {$result->statusMsg}<br>";
            // TODO 添加错误处理逻辑
        } else {
            echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $resMsg = $result->TemplateSMS;
            echo "dateCreated: {$resMsg->dateCreated}<br/>; smsMessageSid: {$resMsg->smsMessageSid}<br/>";
            // TODO 添加成功处理逻辑
        }
    }
```

## 使用说明
```php
    // 自定义配置及默认
    $bodyType = "xml";       // 包体格式，可填值：json 、xml
    $logEnabled = true;      // 日志开关。可填值：true、false
    $logPath = "../log.txt"; // 日志文件
```

