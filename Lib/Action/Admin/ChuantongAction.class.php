<?php
//更新算力-》添加上传任务-》上传文件-》上传回调
class ChuantongAction extends ApiAction{

    private $appId="2e3d4f6c-6acd-4b04-9ac0-7b3f8228c2bb";
    private $platform="PC";
    private $requestId;
    private $_t;
    private $guid="tencent";
    private $id;
    function __construct()
	{
        parent::__construct();
        $this->requestId=$this->uuid();
        $this->_t=time();
        $this->id=$this->uuid();

    }
    
    //上传页面
    public function upload(){
        //echo 123;
        $this->display();
    }

    //更新算力
    public function updateCal(){
        $url="http://192.168.0.120:12361/conference/updateSystemConfig";
        $data=array(
                "requestId"=>$this->requestId,
                "configKey"=>"computing_power_config_key",
                "configValue"=>"[{\"startTime\":\"00:00:00\",\"endTime\":\"24:00:00\",\"value\":2}]",
                "userId"=>"conference.init",
                "eid"=>"private",
                "tc_computing"=>0,
                "conference_computing"=>20
        );
        //echo json_encode($data);die();
        try{
            $result=$this->curlPost($url,json_encode($data));

            if($result->errCode===0){
                echo "success";
            }else{
                echo "faied";
            }
        }catch(Exception $e){
            
        }
    }

    //添加上传任务
    public function addTask(){
        $url="http://192.168.0.120:12353/";
        $data=array(
                "appId"=>$this->appId,
                "-t"=>$this->_t,
                "guid"=>$this->guid,
                "platform"=>$this->platform,
                "action"=>"tcAddTaskBatch",
                "requestId"=>$this->requestId,
                "op"=>"upload",
                "data"=>array(
                    "id"=>$this->id,
                    "appId"=>$this->appId,
                    "taskName"=>"",
                    "source"=>"",
                    "fileType"=>"mp4",
                    "fileKey"=>$this->appId.$this->id."mp4",
                    "taskType"=>"",
                    "fileSize"=>10,
                    "createUser"=>"tencent"
                )
                
        );

        try{
            $result=$this->curlPost($url,json_encode($data));

            if($result->errCode===0){
                echo "success";
            }else{
                echo "faied";
            }
        }catch(Exception $e){
            
        }
    }


    //文件上传
    public function uploadFile(){
        $url="http://192.168.0.120:12481/uploadOneFile";
        
        
    }

    //上传回调
    public function taskComplete(){
        $url="http://192.168.0.120:12353/";
        $data=array(
            "appId"=>$this->appId,
            "-t"=>$this->_t,
            "guid"=>$this->guid,
            "platform"=>$this->platform,
            "action"=>"tcUpdateTaskCompleted",
            "requestId"=>$this->requestId,
            "op"=>"upload",
            "fileKey"=>$this->appId.$this->id."mp4"
            
    );

    try{
        $result=$this->curlPost($url,json_encode($data));

        if($result->errCode===0){
            echo "success";
        }else{
            echo "faied";
        }
    }catch(Exception $e){
        
    }
    }

    public function uuid(){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid  = substr($chars,0,8) . '-';
        $uuid .= substr($chars,8,4) . '-';
        $uuid .= substr($chars,12,4) . '-';
        $uuid .= substr($chars,16,4) . '-';
        $uuid .= substr($chars,20,12);
        return  $uuid;
    }
}