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

import("/SDK/CCPRestSDK");




/**
  * 发送模板短信
  * @param to 手机号码集合,用英文逗号分开
  * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
  * @param $tempId 模板Id
  */       
function sendTemplateSMS($to,$datas,$tempId)
{
     // 初始化REST SDK
//     global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
    //主帐号
    $accountSid= '8a48b5515124598801513d926b294c96';

//主帐号Token
    $accountToken= '75cb597652f64a81997fc4af91e271da';

//应用Id
    $appId='aaf98f89512446e201513d94eaa04b82';

//请求地址，格式如下，不需要写https://sandboxapp.cloopen.com
//$serverIP='sandboxapp.cloopen.com';
    $serverIP='sandboxapp.cloopen.com';

//请求端口
    $serverPort='8883';

//REST版本号
    $softVersion='2013-12-26';

     $rest = new REST($serverIP,$serverPort,$softVersion);
     $rest->setAccount($accountSid,$accountToken);
     $rest->setAppId($appId);

     // 发送模板短信
     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
    if($result == NULL ) {
        echo "result error!";
        exit();
    }
    if($result->statusCode!=0) {
        echo "error code :" . $result->statusCode . "<br>";
        echo "error msg :" . $result->statusMsg . "<br>";
        //TODO 添加错误处理逻辑
    }else{
        echo "Sendind TemplateSMS success!<br/>";
        // 获取返回信息
        $smsmessage = $result->TemplateSMS;
        echo "dateCreated:".$smsmessage->dateCreated."<br/>";
        echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
        //TODO 添加成功处理逻辑
    }
//     if($result == NULL ) {
//         return 400;
//     }
//     if($result->statusCode!=0) {
//         return 400;
//         //TODO 添加错误处理逻辑
//     }else{
//        return 200;
//         //TODO 添加成功处理逻辑
//     }
}

//Demo调用,参数填入正确后，放开注释可以调用 
//sendTemplateSMS("手机号码","内容数据","模板Id");
?>
