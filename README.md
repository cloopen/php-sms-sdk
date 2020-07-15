Yuntongxun SMS SDK for GO
php模板短信单发接口
  /**
    * 发送模板短信
    * @param to 短信接收彿手机号码集合,用英文逗号分开
    * @param datas 内容数据
    * @param tempId 模板Id
    */
    function sendTemplateSMS($to,$datas,$tempId)

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

    include_once("../SDK/SmsSDK.php");
    /**
    to 手机号码集合,用英文逗号分开
    datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    $tempId 模板Id
    /*
    function sendTemplateSMS($to, $datas, $tempId){
    //主帐号
    $accountSid = 'xxxxx';
    //主帐号Token
    $accountToken = 'xxxxx';
    //应用Id
    $appId = 'xxxxxxx';
    //请求地址，格式如下，不需要写https://,默认为：app.cloopen.com'
    $serverIP = ';
    //请求端口 默认为：8883
    $serverPort = '';
    //REST版本号 默认为：2013-12-26
    $softVersion = '';
    // 初始化REST SDK
    //global $accountSid, $accountToken, $appId, $serverIP, $serverPort, $softVersion;
    $rest = new REST($serverIP, $serverPort, $softVersion);
    $rest->setAccount($accountSid, $accountToken);
    $rest->setAppId($appId);

    // 发送模板短信
    echo "Sending TemplateSMS to $to <br/>";
    $result = $rest->sendTemplateSMS($to, $datas, $tempId);
    if ($result == NULL) {
        echo "result error!";
        break;
    }
    if ($result->statusCode != 0) {
        echo "error code :" . $result->statusCode . "<br>";
        echo "error msg :" . $result->statusMsg . "<br>";
        //TODO 添加错误处理逻辑
    } else {
        echo "Sendind TemplateSMS success!<br/>";
        // 获取返回信息
        $smsmessage = $result->TemplateSMS;
        echo "dateCreated:" . $smsmessage->dateCreated . "<br/>";
        echo "smsMessageSid:" . $smsmessage->smsMessageSid . "<br/>";
        //TODO 添加成功处理逻辑
    }
}

## 使用说明
    * 自定义配置及默认
      $BodyType = "xml";//包体格式，可填值：json 、xml
      $enabeLog = true; //日志开关。可填值：true、
      $Filename = "../log.txt"; //日志文件

