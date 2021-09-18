<?php
/*
 *  Copyright (c) 2014 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.yuntongxun.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */


class REST
{
    private $accountSid;
    private $accountToken;
    private $appId;
    private $serverHost;
    private $serverPort;
    private $apiHost;
    private $apiVersion;
    private $datetime;               // 时间sh
    private $bodyType = "xml";       // 包体格式，可填值：json 、xml
    private $logEnabled = true;      // 日志开关。可填值：true、
    private $logPath = "../log.txt"; // 日志文件
    private $handle;

    function __construct($serverHost, $serverPort, $apiVersion)
    {
        $this->datetime = date("YmdHis");
        $this->serverHost = $serverHost;
        $this->serverPort = $serverPort;
        $this->apiVersion = $apiVersion;
        $this->apiHost = "https://{$serverHost}:{$serverPort}/{$apiVersion}";

        $this->handle = fopen($this->logPath, 'a');
    }

    /**
     * 设置主帐号
     *
     * @param string $accountSid 主帐号
     * @param string $accountToken 主帐号Token
     */
    function setAccount($accountSid, $accountToken)
    {
        $this->accountSid = $accountSid;
        $this->accountToken = $accountToken;
    }

    /**
     * 设置应用ID
     *
     * @param int $appId 应用ID
     */
    function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * 打印日志
     *
     * @param string $log 日志内容
     */
    function writeLog($log)
    {
        if ($this->logEnabled) fwrite($this->handle, "{$log}\n");
    }

    /**
     * 发起HTTPS请求
     * @param string $url
     * @param mixed $data
     * @param mixed $header
     * @param int $post
     * @return bool|string
     */
    function curl_post($url, $data, $header, $post = 1)
    {
        // 初始化curl
        $ch = curl_init();
        // 参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if ($post) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);

        // 连接失败
        if ($result != FALSE) return $result;

        if ($this->bodyType == 'json') return "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
        return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><Response><statusCode>172001</statusCode><statusMsg>网络错误</statusMsg></Response>";
    }
    
    /**
     * 发送模板短信
     * @param int|string $to 短信接收彿手机号码集合,用英文逗号分开
     * @param array $dataAry 内容数据
     * @param int|string $templateId 模板Id
     * @return mixed|SimpleXMLElement|stdClass
     */
    function sendTemplateSMS($to, $dataAry, $templateId)
    {
        $appId = $this->appId;
        $datetime = $this->datetime;
        $bodyType = $this->bodyType;
        $accountSid = $this->accountSid;
        $accountToken = $this->accountToken;

        // 主帐号鉴权信息验证，对必选参数进行判空。
        $auth = $this->accAuth();
        if ($auth != "") return $auth;

        // 拼接请求包体
        if ($bodyType == "json") {
            $data = "'" . implode(',', $dataAry) . "'";
            $body = "{'to':'{$to}','templateId':'{$templateId}','appId':'{$appId}','datas':[{$data}]}";
        } else {
            $data = "<data>" . implode('</data><data>', $dataAry) . "</data>";
            $body = "<TemplateSMS>
                    <to>$to</to> 
                    <appId>{$appId}</appId>
                    <templateId>{$templateId}</templateId>
                    <datas>{$data}</datas>
                  </TemplateSMS>";
        }
        $this->writeLog("request body = {$body}");

        // 大写的sig参数 
        $sig = strtoupper(md5($accountSid . $accountToken . $datetime));
        // 生成请求URL        
        $url = "{$this->apiHost}/Accounts/{$accountSid}/SMS/TemplateSMS?sig={$sig}";
        $this->writeLog("request url = {$url}");

        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authStr = base64_encode("{$accountSid}:{$datetime}");
        // 生成包头  
        $header = [
            "Accept:application/{$bodyType}",
            "Content-Type:application/{$bodyType};charset=utf-8",
            "Authorization:$authStr"
        ];

        // 发送请求
        $result = $this->curl_post($url, $body, $header);
        $this->writeLog("response body = {$result}");

        // JSON格式 or xml格式
        $dataAry = $bodyType == "json" ? json_decode($result) : simplexml_load_string(trim($result, " \t\n\r"));

        // 重新装填数据
        if ($dataAry->statusCode == 0 && $bodyType == "json") {
            $dataAry->TemplateSMS = $dataAry->templateSMS;
            unset($dataAry->templateSMS);
        }

        return $dataAry;
    }

    /**
     * 主帐号鉴权
     */
    function accAuth()
    {
        if ($this->serverHost == "") {
            $data = new stdClass();
            $data->statusCode = '172004';
            $data->statusMsg = 'serverIP为空';
            return $data;
        }
        if ($this->serverPort <= 0) {
            $data = new stdClass();
            $data->statusCode = '172005';
            $data->statusMsg = '端口错误（小于等于0）';
            return $data;
        }
        if ($this->apiVersion == "") {
            $data = new stdClass();
            $data->statusCode = '172013';
            $data->statusMsg = '版本号为空';
            return $data;
        }
        if ($this->accountSid == "") {
            $data = new stdClass();
            $data->statusCode = '172006';
            $data->statusMsg = '主帐号为空';
            return $data;
        }
        if ($this->accountToken == "") {
            $data = new stdClass();
            $data->statusCode = '172007';
            $data->statusMsg = '主帐号令牌为空';
            return $data;
        }
        if ($this->appId == "") {
            $data = new stdClass();
            $data->statusCode = '172012';
            $data->statusMsg = '应用ID为空';
            return $data;
        }
    }
}
?>