<?php
// 本文档自动生成，仅供测试运行
class ApiAction extends CommonAction
{
    /**
    +----------------------------------------------------------
    * 调用接口 Post方法
    +----------------------------------------------------------
    */
    public function curlPost($url,$curlData)
    {   
        $ch = curl_init();
        $headers = array("Content-type: application/json;charset='utf-8'","Accept: application/json","Cache-Control: no-cache","Pragma: no-cache");
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);

        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }

     /**
    +----------------------------------------------------------
    * 调用接口 Get方法
    +----------------------------------------------------------
    */
    public function curlGet($url)
    {   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上

        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }

    //ase加密

}
?>