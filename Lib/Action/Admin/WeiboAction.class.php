<?php
class WeiboAction extends Action{
	/**
	+--------------------------------
	* 获取微博内容
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
    */
    public function weibo_user_timeline(){
        $Weibo=M('weibo');
        $since_id=$Weibo->max('w_id');
        $my_id=1742987497;
        $token="2.00Zf5xtBVjMI1B6801aad975pF_6cE";
        $url="https://api.weibo.com/2/statuses/user_timeline.json?trim_user=1&access_token=".$token."&since_id=".$since_id;
        
        //最多返回5条
        $weibo_result=file_get_contents($url);
        $this->weibo_log($weibo_result,"weibo");  //记录日志
        $weibo_result=json_decode($weibo_result,true); //json解码
        $count=count($weibo_result['statuses']);
        if($count==0){
            echo "0条更新";
            exit();
        }
        foreach ($weibo_result['statuses'] as $v){
           $w_id=number_format($v['id'],0,'','');
           $data['w_id']=$w_id;
           $data['w_text']=$v['text'];
           $data['w_textLength']=$v['textLength'];
           $data['w_createTime']=$this->changeDateTime($v['created_at']);
           $data['w_recordTime']=date("Y-m-d H:i:s",time());
           $data['w_repostsCount']=$v['reposts_count'];
           $data['w_commentsCount']=$v['comments_count'];
           $data['w_attitudesCount']=$v['attitudes_count'];
           //查询微博是否已经存在，存在则更新，不存在添加
           $res=$Weibo->where("w_id='$w_id'")->find();
           if($res){
               $Weibo->save($data);
           }else{
               $Weibo->add($data);
           }
        }
        echo $count."条更新";
    }
   
    /**
    +--------------------------------
    * 获取自己的评论
    +--------------------------------
    * @date: 2019年8月1日 上午11:14:01
    * @author: zt
    * @param: variable
    * @return:
    */
    public function weibo_comment(){
        $Comment=M('comment');
        $since_id=$Comment->max('c_id');
        $token="2.00Zf5xtBVjMI1B6801aad975pF_6cE";
        $url="https://api.weibo.com/2/comments/by_me.json?access_token=".$token."&since_id=".$since_id;
        
        $comment_result=file_get_contents($url);
        $this->weibo_log($comment_result,"comment"); //记录评论日志
        $comment_result=json_decode($comment_result,true); //json解码
        $count=count($comment_result['comments']);
        if($count==0){
            echo "0条更新";
            exit();
        }
        foreach ($comment_result['comments'] as $v){
            $data['c_id']=number_format($v['id'],0,'','');
            $data['w_id']=number_format($v['status']['id'],0,'','');
            $data['c_text']=$v['text'];
            $data['c_createTime']=$this->changeDateTime($v['created_at']);
            $data['c_recordTime']=date("Y-m-d H:i:s",time());
            $Comment->add($data);
        }
        echo $count."条更新";
    }
    
    
    //微博的时间转换成datetime格式
    public function changeDateTime($time){
        $year=substr($time,-4);
        $month=substr($time,4,3);
        $day=substr($time,8,2);
        $time=substr($time,11,8);
        
        switch($month){
            case "Jan":
                $month="01";
                break;
            case "Feb":
                $month="02";
                break;
            case "Mar":
                $month="03";
                break;
            case "Apr":
                $month="04";
                break;
            case "May":
                $month="05";
                break;
            case "Jun":
                $month="06";
                break;
            case "Jul":
                $month="07";
                break;
            case "Aug":
                $month="08";
                break;
            case "Sep":
                $month="09";
                break;
            case "Oct":
                $month="10";
                break;
            case "Nov":
                $month="11";
                break;
            case "Dec":
                $month="12";
                break;
        }
        $datetime=$year."-".$month."-".$day." ".$time;
        return $datetime;
    }
    
    /**
     +--------------------------------
     * 获取微博、评论接口返回值记录日志
     +--------------------------------
     * @date: 2019年8月1日 上午11:14:01
     * @author: zt
     * @param: $type 日志类型【weibo】微博，【comment】评论
     * @param: $content 接口返回值内容
     * @return:
     */
    private function weibo_log($content,$type){
        $foldername=date("Ym",time());
        $path=C("WEIBO_LOG_PATH").$foldername."/";
        if (!is_dir($path)){
            $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true); //创建当月的文件夹
        }
        $time="[".date("Y-m-d H:i:s",time())."]\r\n"; //接口访问时间
        $logname=date("Ymd",time())."_".$type.".txt"; //日志名称
        $filepath=$path.$logname;
        $myfile=fopen($filepath, "a") or die("Unable to open file!");
        fwrite($myfile,$time.$content."\r\n\r\n#########################################################################################################################\r\n\r\n");
        fclose($myfile);
    }

 
    //微博发布图文
    public function shareWeibo(){
        import('ORG.Net.Weibo');
        $Weibo=new Weibo("3455628279","b0c05b1bbbbf63430739207fb3fcfcd8","2.00J12tbFzl9rlD9b9c733fc4G5zQhB","");

        $content='【地铁爱广播】为进一步增强国防观念和民防意识，更好地检验本市防空警报设施的完好程度和发放防空警报的能力';
        $pic="http://222.66.139.84/Image/158977849845077.jpg";
        //$pic[0]="http://222.66.139.84/Image/158977851587375.jpg";
        //$pic[1]="http://222.66.139.84/Image/158977849845077.jpg";
        $content.="http://www.courtyard007.cn";
        $res=$Weibo->share($content,$pic);
        dump($res);
        die();
        $data1 = '{
            "access_token": "2.00J12tbFzl9rlD9b9c733fc4G5zQhB",
            "status": "新浪微博api接口调asdsadad试_网络_wlp_name的专栏-CSDN博客 http://www.courtyard007.cn",
            }';


        $data['access_token']="2.00J12tbFzl9rlD9b9c733fc4G5zQhB";
        $data['status']="新浪微博api接口调asdsadad试_网络_wlp_name的专栏-CSDN博客 http://www.courtyard007.cn";

        //$data = '{"access_token":"2.00J12tbFzl9rlD9b9c733fc4G5zQhB","":""}';
        //$data['status']=urlencode("2.00J12tbFzl9rlD9b9c733fc4G5zQhB");

        $url = "https://api.weibo.com/2/statuses/share.json";

        $res = $this->http_request($url, $data);
        
        //var_dump($res);
    }

   
    
}