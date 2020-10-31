<?php
/**
 +--------------------------------
 * 微信类
 +--------------------------------
 * @date: 2016-4-27 上午10:42:02
 * @author: Str
 * @param: $GLOBALS
 * @return:
 */
class Wechat
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        
        //extract post data
        if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
             the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";
            if(!empty( $keyword ))
            {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo "Input something...";
            }
            
        }else {
            echo "xxxx";
            exit;
        }
    }
    
    //保存发送的信息
    public  function saveMsg(){
        //获取得到的信息
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;//发送者OpenID
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        
        //获得发送者信息
        $userInfo=$this->getUserInfo($fromUsername);
        
        //图片保存到本地
        $return_content = http_get_data($userInfo['headimgurl']);
        $file=$userInfo['openid'].".jpg";
        $filename = "./weChatImage/".$file;
        $fp= @fopen($filename,"a"); //将文件绑定到流 
        fwrite($fp,$return_content); //写入文件
        
        //保存到字幕机读取的文件夹
        $file2=GetRandStr(8);
        $filename2="./weChatNew/".$file2.".jpg";
        $fp= @fopen($filename2,"a"); //将文件绑定到流 
        fwrite($fp,$return_content); //写入文件
        //缩小图片
        import('ORG.Net.Image');
        Image::thumb($filename2,$filename2,'','200','200',true,100);
        //文字保存到text
        $myfile = fopen("./weChatNew/".$file2.".txt", "w");
        fwrite($myfile, $keyword);
        fclose($myfile);
        
        $ReceiveMessage=M("ReceiveMessage");
        $data['nickname']=$userInfo['nickname'];
        $data['headimgurl']=$file;
        $data['openid']=$userInfo['openid'];
        $data['city']=$userInfo['city'];
        $data['sex']=$userInfo['sex'];
        $data['province']=$userInfo['province'];
        $data['country']=$userInfo['country'];
        $data['info']=$keyword;
        $data['receiveTime']=date("Y-m-d H:i:s",time());
        $data['status']=0;
        $id=$ReceiveMessage->add($data);
        $ReceiveMsg=new ReceiveMessageModel();
        $ReceiveMsg->MsgHandler($id);//信息自动处理
        //echo $ReceiveMessage->getLastSql();
    }
    
    private function checkSignature()
    {
        define('TOKEN',"str");
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     +--------------------------------
     * 获取访问令牌
     * 超过1小时重新获取一次
     +--------------------------------
     * @date: 2016-4-27 上午11:02:00
     * @author: Str
     * @param: variable
     * @return:
     */
    public function getAccessToken(){
         $Token=M("Token");
        $res=$Token->where("id=1")->find();
        //如果间隔小于3600s
        if((time()-$res["getTime"])<3600){
            return $res["token"];
        }else{
            //间隔大于3600s 重新获取令牌
            $appid = "wxe61bfc95d5c337a0";
            $appsecret = "b42dcb8b4a4baf9841fffbce1bbfdcf4";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $jsoninfo = json_decode($output, true);
            $access_token = $jsoninfo["access_token"];

             //加入数据库
            $data['id']=1;
            $data['token']=$access_token;
            $data['getTime']=time();
            $Token->save($data); 
			//获取jsapi_ticket
			$this->getTicket($access_token);
            return $access_token;
        }
    }
    
	/**
     +--------------------------------
     * 获取jsapi_ticket
     * 超过2小时重新获取一次
     +--------------------------------
     * @date: 2016-4-27 下午12:37:50
     * @author: Str
     * @param: $GLOBALS
     * @return:
     */
	 public function getTicket($token){
		$url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		 $ticket=json_decode($output);
		 if($ticket->errcode==0){
			 $Ticket=M("ticket");
			 $Ticket->where("id=1")->setField("ticket",$ticket->ticket);
		 }
	 }
	
    /**
     +--------------------------------
     * 获取发送者信息
     +--------------------------------
     * @date: 2016-4-27 下午12:37:50
     * @author: Str
     * @param: $GLOBALS
     * @return:
     */
    private function getUserInfo($openID){
        //获取令牌
        $token=$this->getAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openID&lang=zh_CN";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        return $jsoninfo;
    }
    
}