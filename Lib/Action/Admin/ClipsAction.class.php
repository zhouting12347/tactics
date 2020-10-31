<?php
class ClipsAction extends CommonAction{
    //1:上传区   2.审核区  3:过审区  4:审核不通过'
    
    /*************************************************************************************************/
    /******************************************素材上传区********************************************/
    /************************************************************************************************/
    
	/**
	+--------------------------------
	* 节目素材表格页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function clips_uploading_table(){
        $this->assign("cliptype",$this->getData("cliptype","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 素材上传页
    +--------------------------------
    * @date: 2019年4月19日 上午11:09:14
    * @author: zt
    * @param: variable
    * @return:
    */
    public function clips_upload_layer(){
        $this->assign("cliptype",$this->getData("cliptype","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 素材编辑页
    +--------------------------------
    * @date: 2019年4月22日 下午1:10:13
    * @author: zt
    * @param: variable
    * @return:
    */
    public function clips_edit_layer(){
        $this->assign("cliptype",$this->getData("cliptype","CompanyID=$_SESSION[company_id]",1));
        $id=$_GET['id'];
        $this->assign("clip",$this->getData("clips","C_ID=$id",0));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 素材编辑操作
     +--------------------------------
     * @date: 2019年4月22日 下午1:10:13
     * @author: zt
     * @param: variable
     * @return:
     */
    public function clips_edit_handler(){
        $condition="C_ID=$_POST[C_ID]";
        $result=$this->editData("clips",$_POST,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 素材删除操作
    +--------------------------------
    * @date: 2019年4月22日 下午1:50:59
    * @author: zt
    * @param: variable
    * @return:
    */
    public function clips_del_clip_handler(){
        $id=$_GET['id'];
        $condition="C_ID=$id";
        $result=$this->delData("clips",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 素材提交审核操作
     +--------------------------------
     * @date: 2019年4月22日 下午1:50:59
     * @author: zt
     * @param: variable
     * @return:
     */
    public function clips_submit_auditing_handler(){
        $data['C_Status']=2;
        $data['C_AuditRemark']="";
        $data['C_AuditTime']="";
        $condition="C_ID=$_GET[id]";
        $result=$this->editData("clips",$data,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 视频播放页
    +--------------------------------
    * @date: 2019年4月22日 下午4:40:24
    * @author: zt
    * @param: variable
    * @return:
    */
    public function clips_video_layer(){
        $id=$_GET['id'];
        $clip=$this->getData("clips","C_ID=$id",0);
        $this->assign("videourl",C("VIDEO_URL").$clip["C_Thumbnail"].".mp4");
        $this->display();
    }
    
    /**
    +--------------------------------
    * 素材上传操作
    +--------------------------------
    * @date: 2019年4月19日 上午11:54:02
    * @author: zt
    * @param: variable
    * @return:
    */
    public function clips_upload_handler(){
        import('ORG.Net.UploadFile');
        $originName=$_FILES['file']['name'];//原始文件名
        $name_array=explode('.',$originName);
        $count=count($name_array);
        $format=$name_array[$count-1];
        $size=$_FILES['file']['size'];//原始文件大小 */
        $upload = new UploadFile();// 实例化上传类
        $upload->savePath = C('VIDEO_UPLOAD_PATH'); // 设置附件上传目录
        $ID=time().$_SESSION['company_id'].mt_rand(10000,99999);
        $filename=$ID.".".$format;//文件名
        $upload->saveRule=$ID;
        $upload->uploadReplace=true;
        if(!$upload->upload()) {
            // 上传错误提示错误信息
            $result=array(
                'code'=>1,
                'msg'=>$upload->getErrorMsg()
            );
            echo json_encode($result);
        }else{
            //检测上传视频是否存在,不存在返回失败
            if(!file_exists(C("VIDEO_UPLOAD_PATH").$filename)){
                $result=array(
                    'code'=>1,
                    'msg'=>'upload failed'
                );
                echo json_encode($result);
                return false;
            } 

            // 上传成功 获取上传文件信息
            $fileInfo=$upload->getUploadFileInfo();
            $filePath=$fileInfo[0]['savepath'].$fileInfo[0]['savename'];
            
            //获取视频文件的长度
            extension_loaded('ffmpeg');
            try {
                $ffmpegInstance = new ffmpeg_movie($filePath);
            } catch (Exception $e) {
                exit();
            }
            
            //视频截图
            if($ffmpegInstance){
                 $ff_frame=$ffmpegInstance->getFrame(1);
                 $gd_image = $ff_frame->toGDImage();
                 imagejpeg($gd_image, C('VIDEO_UPLOAD_PATH')."image/".$ID.".jpg"); //创建jpg图像
                 imagedestroy($gd_image);//销毁一图像
                 
                $data['C_ID']=$ID;
                $data['C_Title']=$_SESSION['title'];
                $data['CT_ID']=$_SESSION['typeID'];
                $data['C_Remark']=$_SESSION['remark'];
                $data['CompanyID']=$_SESSION['company_id'];
                $data['C_OriginName']=$originName;
                $data['C_Duration']=frameToTime($ffmpegInstance->getFrameCount());
                $data['C_Length']=timeToSeconds(substr($data['C_Duration'],0,8));
                $data['C_Format']=$format;
                $data['C_Width']=$ffmpegInstance->getFrameWidth();
                $data['C_Height']=$ffmpegInstance->getFrameHeight();
                $data['C_Size']=round($size/1024/1024,1);
                $data['C_Thumbnail']=$ID;
                $data['C_CreateTime']=date("Y-m-d H:i:s",time());
                $data['C_Status']=1;
                $data['C_Creator']=$_SESSION['username'];
                $data['C_IfDP']=$_SESSION['ifDP'];
                
                //保存到video表
                $Video=M("clips");
                $Video->add($data);
                $result=array(
                    'code'=>0,
                    'msg'=>'success',
                    'folder'=>date(Ymd),
                    'savename'=>$filename
                );
                echo json_encode($result);
            }else{
                $result=array(
                    'code'=>1,
                    'msg'=>'upload failed,ffmpeg error'
                );
                echo json_encode($result);
            }
            
        }
    }
    
    /*************************************************************************************************/
     /******************************************END素材上传区***************************************/
     /************************************************************************************************/
    
    /*************************************************************************************************/
    /******************************************审核区************************************************/
    /************************************************************************************************/
    
    /**
     +--------------------------------
     * 待审核素材表格页
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function clips_auditing_table(){
        $this->assign("cliptype",$this->getData("cliptype","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 素材审核不通过，建议页
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function clips_audit_remark_layer(){
        $this->assign("C_ID",$_GET['id']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 素材审核不通过操作
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function clips_audit_remark_handler(){
        $data['C_AuditRemark']=$_POST['C_AuditRemark'];
        $data['C_Auditor']=$_SESSION['username'];
        $data['C_AuditTime']=date("Y-m-d H:i:s",time());
        $data['C_Status']=4;
        $condition="C_ID=$_POST[C_ID]";
        $result=$this->editData("clips",$data,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 素材通过审核操作
    +--------------------------------
    * @date: 2019年4月23日 下午3:37:03
    * @author: zt
    * @param: variable
    * @return:
    */
    public function clips_approve_handler(){
        $data['C_AuditRemark']=$_POST['C_AuditRemark'];
        $data['C_Auditor']=$_SESSION['username'];
        $data['C_AuditTime']=date("Y-m-d H:i:s",time());
        $data['C_Status']=3;
        $condition="C_ID=$_GET[id]";
        $result=$this->editData("clips",$data,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /*************************************************************************************************/
    /**************************************END审核区************************************************/
    /************************************************************************************************/
    
    /*************************************************************************************************/
    /******************************************过审区************************************************/
    /************************************************************************************************/
    /**
     +--------------------------------
     * 过审素材表格页
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function clips_audited_table(){
        $this->assign("cliptype",$this->getData("cliptype","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /*************************************************************************************************/
    /******************************************END过审区*******************************************/
    /************************************************************************************************/
    
    public function ajaxSaveClipsInfo(){
        $_SESSION['title']=$_GET['title'];
        $_SESSION['typeID']=$_GET['typeID'];
        $_SESSION['remark']=$_GET['remark'];
        $_SESSION['ifDP']=$_GET['ifDP'];
    }
}