<?php
class WechatAction extends Action{
    
    public $access_token;
    
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    
    /**
     * 用于验证是否是微信服务器发来的消息
     * @return bool
     */
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
        $token=123456;
        $tmpArr=array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr=implode($tmpArr);
        $tmpStr=sha1($tmpStr);
        
        if ($tmpStr==$signature){
            return true;
        }else {
            return false;
        }
    }
    
    public function sendMsg_bak(){
        $this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".$this->access_token;
        $curlPost='{ "filter":{"is_to_all":true},"text":{"content":"地铁微信测试"},"msgtype":"text"}';
        $ch = curl_init();
        /* curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); */
        
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        
        
        $output = curl_exec($ch);
        curl_close($ch);
        $response=json_decode($output);
        dump($response);
    }
    
    //获取公众号发送的信息
    public function getMsg(){
        $this->getAccessToken();
        $url="https://api.weixin.qq.com/datacube/getarticlesummary?access_token=$this->access_token";
        
        /*********************************************************************************/
        $curlPost='{ "begin_date":"'.date("Y-m-d", strtotime("-1 day")).'","end_date":"'.date("Y-m-d", strtotime("-1 day")).'"}'; //获取前一天的数据
        /*********************************************************************************/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        
        $output = curl_exec($ch);
        curl_close($ch);
        $response=json_decode($output);
        //dump($response);
        //保存取得的微信数据
        $Wechat=M("wechat");
        foreach($response->list as $v){
            $msgid=$data['msgid']=$v->msgid;
            $ref_date=$data['ref_date']=$v->ref_date;
            $res=$Wechat->where("msgid='$msgid' and ref_date='$ref_date'")->find();
            if(!$res){
                $data['title']=$v->title;
                $data['int_page_read_user']=$v->int_page_read_user;
                $data['int_page_read_count']=$v->int_page_read_count;
                $data['share_user']=$v->share_user;
                $data['share_count']=$v->share_count;
                $data['add_to_fav_user']=$v->add_to_fav_user;
                $data['add_to_fav_count']=$v->add_to_fav_count;
                $Wechat->add($data);
            }
            unset($data);
        }
    }
    
    //获取公众号凭证
    public function getAccessToken(){
        //$appID="wxdec037d1bab93e94";
        //$appsecret="179a11f6bd53b757adacde4681bb7dd4";
        $appID="wx213bdf219eef0d8f";
        //$appsecret="aa309f549dacb55f9411e6bc0b2ceac9";
        $appsecret="f500d297a2e8ec77f8f6e909da0a17f4";
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appID&secret=$appsecret";
        $res=json_decode(file_get_contents($url));
        //dump($res);
        $this->access_token=$res->access_token;
    }
}