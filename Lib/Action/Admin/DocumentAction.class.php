<?php
class DocumentAction extends CommonAction{
    //稿件流程
    //素材库
    //1.创建状态 2待审核 3审核通过
    
    //稿库
    //-1审核未通过     1创建状态     2责编，编审（媒体部审核）    3版权待审核     4领导待审核       5待发布稿件    6编辑配图
    
    //  创建人          媒体部审核人         版权审核        领导审核     发布稿件人
    /*
    目前流程为：编辑-》责编-》版权-》领导；
    调整为：编辑-》版权-》责编-》领导；

    流程最新调整：
    编辑稿件 -》 2、编辑配图（可能为视频） -》 3、版权审核（图片和视频） -》 4、责编审核（通过或者发给领导）
    */    
	/**
	+--------------------------------
	* 新建稿件主页列表
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function document_table(){
        $this->display();
    }

    /**
     +--------------------------------
     * 待发稿件列表
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_audited_table(){
        $this->display();
    }
    
    /**
    +--------------------------------
    * 稿件类型选择层
    +--------------------------------
    * @date: 2019年7月18日 上午10:38:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_type_layer(){
        $this->assign("baoliaoID",$_GET['d_id']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 直播稿件创建层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_add_live_layer(){
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("d_id",time().rand(10000,99999));
        $this->assign("createtime",date("Y-m-d H:i:s",time()));
        if($_GET['baoliaoID']){
            $this->getBaoliao($_GET['baoliaoID']);
        }
        $this->display();
    }
    
    /**
     +--------------------------------
     * 直播稿件编辑层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_edit_live_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");
        $attachmentCount=$this->getDataCount("attachment","d_id='$d_id'");
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("document",$document);
        $this->assign("attachmentCount",$attachmentCount);
        $this->assign("currentStatus",$_GET['currentStatus']);
        $this->display();
    }
   
    /**
     +--------------------------------
     * 微博稿件创建层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_add_weibo_layer(){
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("d_id",time().rand(10000,99999));
        $this->assign("createtime",date("Y-m-d H:i:s",time()));
        if($_GET['baoliaoID']){
            $this->getBaoliao($_GET['baoliaoID']);
        }
        $this->display();
    }
    
    /**
     +--------------------------------
     * 微博稿件编辑层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_edit_weibo_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("document",$document);
        $this->assign("currentStatus",$_GET['currentStatus']); //流程的类型，用于判断按钮
        $this->display();
    }
    
    /**
     +--------------------------------
     * 通发稿件创建层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_add_tongfa_layer(){
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("d_id",time().rand(10000,99999));
        $this->assign("createtime",date("Y-m-d H:i:s",time()));
        if($_GET['baoliaoID']){
            $this->getBaoliao($_GET['baoliaoID']);
        }
        $this->display();
    }
    
    /**
     +--------------------------------
     * 通发稿件编辑层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_edit_tongfa_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("document",$document);
        $this->assign("currentStatus",$_GET['currentStatus']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 其他稿件创建层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_add_other_layer(){
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("d_id",time().rand(10000,99999));
        $this->assign("createtime",date("Y-m-d H:i:s",time()));
        if($_GET['baoliaoID']){
            $this->getBaoliao($_GET['baoliaoID']);
        }
        $this->display();
    }
    
    /**
     +--------------------------------
     * 其他稿件编辑层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_edit_other_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("document",$document);
        $this->assign("currentStatus",$_GET['currentStatus']); //流程的类型，用于判断按钮
        $this->display();
    }
    
    /**
    +--------------------------------
    * 稿件保存操作
    +--------------------------------
    * @date: 2019年7月22日 上午10:59:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_save_handler(){
        $_POST['d_content']=urldecode(base64_decode($_POST['d_content']));//base64解码
        $_POST['d_createTime']=date("Y-m-d H:i:s",time()); //创建时间
        $_POST['d_createPerson']=$_SESSION['userID'];
        if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        {
            $_POST['d_content']=stripslashes($_POST['d_content']);//将字符串进行处理
        }
        $_POST['d_status']=1;
        $result=$this->addData("document", $_POST);

        //保存稿件流程信息
        if($result['status']){
            $this->workflowRecord($_POST['d_id'],"创建稿件","");
        }

        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 稿件编辑操作
    +--------------------------------
    * @date: 2019年7月24日 上午10:01:22
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_edit_handler(){


        /*******************稿件审核时其他环节不允许保存*******************/
        $document=$this->getData("document","d_id=$_POST[d_id]");
        //判断操作人是否为创建者，当稿件状态不为1和-1时，保存失败
        if($_SESSION["userID"]==$document["d_createPerson"]){
            if($document['d_status']>1){
                $this->ajaxReturn("","稿件正在审核中不能保存",0);
            }
        }else{
            //!查询操作人是否存在于审核流程中,如果存在说明当前操作人已操作过，不允许保存，（可能有问题，如果审核人被选择审核2个环节）
            $Document=M("document");
            $res=$Document->where("d_id=$_POST[d_id] and ((d_mediaAuditPerson=$_SESSION[userID] and d_mediaAuditTime is not null) or (d_copyrightAuditPerson=$_SESSION[userID] and d_copyrightAuditTime is not null)  or (d_editAuditPerson=$_SESSION[userID] and d_editAuditTime is not null))")->find();
            //echo $Document->getLastSql();
            if($res){
                $this->ajaxReturn("","你已经审核过稿件",0);
            }
        }
        /**************************************/

        $_POST['d_content']=urldecode(base64_decode($_POST['d_content']));//base64解码
        if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        {
            $_POST['d_content']=stripslashes($_POST['d_content']);//将字符串进行处理
        }
        $result=$this->editData("document", $_POST,"d_id=$_POST[d_id]");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 根据流程选择审核人员
    +--------------------------------
    * @date: 2019年7月23日 上午11:04:37
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_choose_audit_person_layer(){
        $condition="u_id!='$_SESSION[userID]'";
        $User=M('user');

        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");

        //跳过编辑配图直接提交版权审核
        if($_GET['toCopyright']==1){
            $condition.=" and (u_duty='版权审核' or u_duty2='版权审核')";
        }
        //如果是直播稿为创建状态，继续选择编辑配图
        elseif(($document['d_classify']=="直播稿"&&$document['d_status']==1) OR ($document['d_classify']=="直播稿"&&$document['d_status']==-1)){
        
        }elseif($document['d_classify']=="直播稿"&&$document['d_status']==6){  //编辑配图状态，选择版权
            $condition.=" and (u_duty='版权审核' or u_duty2='版权审核')";
            
        }elseif(($document['d_classify']=="统发稿"&&$document['d_status']==1) OR ($document['d_classify']=="统发稿"&&$document['d_status']==-1)){  //统发稿创建状态，选择版权
            $condition.=" and (u_duty='版权审核' or u_duty2='版权审核')";
        }else{
            //其他稿件走原有流程
            //编辑-》版权-》责编-》领导；
            $condition.=" and (u_duty='版权审核' or u_duty2='版权审核')";
        }
        $user=$User->where($condition)->select();
    
        $this->assign('user',$user);
        $this->assign("d_id",$_GET['d_id']);
        $this->assign("toCopyright",$_GET['toCopyright']);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 删除稿件操作
    +--------------------------------
    * @date: 2019年7月23日 下午2:49:06
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_del_document_handler(){
        $d_id=$_GET['d_id'];
        $result=$this->delData("document","d_id='$d_id'");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 稿件提交到版权审核
    +--------------------------------
    * @date: 2019年7月23日 下午2:58:25
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_submit_handler(){
        $u_id=$_POST['u_id'];
        $d_id=$_POST['d_id'];
        $Document=M("document");
        $document=$Document->where("d_id='$d_id'")->find();
        //清空之前的审核意见
        $Document->where("d_id='$d_id'")->setField(array("d_failedReason","d_failedPerson"),array("",""));


        //如果通过理由未填写，默认理由为“通过”
        if(!$_POST['d_passReason']){
            $_POST['d_passReason']="通过";
        }
        //直播稿直接跳过编辑配图到版权
        if($_POST['toCopyright']==1){
            $res=$Document->where("d_id='$d_id'")
            ->setField(array("d_status","d_copyrightAuditPerson","d_passReason","d_editAuditReason","d_editAuditTime")
            ,array(3,$u_id,$_POST['d_passReason'],$_POST['d_passReason'],date("Y-m-d H:i:s",time())));

            //保存稿件流程信息
            $this->workflowRecord($_POST['d_id'],"提交版权审核",$_POST['d_passReason']);

        }
        //直播稿件创建状态，提交到编辑
        elseif(($document['d_classify']=="直播稿"&&$document['d_status']==1) OR ($document['d_classify']=="直播稿"&&$document['d_status']==-1)){
            $res=$Document->where("d_id='$d_id'")->setField(array("d_status","d_editAuditPerson"),array(6,$u_id));

            //保存稿件流程信息
            $this->workflowRecord($_POST['d_id'],"提交编辑审核",$_POST['d_passReason']);

        //直播稿编辑配图状态，提交到版权审核    
        }elseif($document['d_classify']=="直播稿"&&$document['d_status']==6){
            $res=$Document->where("d_id='$d_id'")
            ->setField(array("d_status","d_copyrightAuditPerson","d_passReason","d_editAuditReason","d_editAuditTime")
            ,array(3,$u_id,$_POST['d_passReason'],$_POST['d_passReason'],date("Y-m-d H:i:s",time())));

            //保存稿件流程信息
            $this->workflowRecord($_POST['d_id'],"提交版权审核",$_POST['d_passReason']);
        }else{
            $res=$Document->where("d_id='$d_id'")->setField(array("d_status","d_copyrightAuditPerson"),array(3,$u_id));

            //保存稿件流程信息
            $this->workflowRecord($_POST['d_id'],"提交版权审核",$_POST['d_passReason']);
        }
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    /**
    +--------------------------------
    * 查看稿件页
    +--------------------------------
    * @date: 2019年7月24日 上午10:56:34
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_check_layer(){
        $d_id=$_GET['d_id'];
        $User=M('user');
        $document=$this->getData("document","d_id='$d_id'");
        
       /*  $user=$User->where("u_id=$document[d_createPerson]")->find();
        $document['createPersonName']=$user['u_realname'];
        $user=$User->where("u_id=$document[d_mediaAuditPerson]")->find();
        $document['mediaAuditName']=$user['u_realname'];
        $user=$User->where("u_id=$document[d_copyrightAuditPerson]")->find();
        $document['copyrightAuditName']=$user['u_realname'];
        $user=$User->where("u_id=$document[d_editAuditPerson]")->find();
        $document['editAuditName']=$user['u_realname'];
        
        //查询审核的领导
        $LeaderAuditList=M('leaderauditlist');
        $leaderauditlist=$LeaderAuditList->where("d_id='$d_id'")
        ->join("left join t_user on t_user.u_id=t_leaderauditlist.l_userID")
        ->order("l_num")
        ->select();
        
        $auditLeaderList=explode("##",$document["d_leaderAuditReason"]);
        $auditLeaderTimeList=explode("##",$document["d_leaderAuditTime"]);
        
        $leaderList=array();
        foreach ($leaderauditlist as $key=>$v){
            $leaderList[$key]['leaderName']=$v['u_realname'];
            $leaderList[$key]['leaderAuditReason']=$auditLeaderList[$key];
            $leaderList[$key]['leaderAuditTime']=$auditLeaderTimeList[$key];
        } */

        //查询稿件流程
        $WorkflowRecord=M("workflow_record");
        $record=$WorkflowRecord->where("d_id='$d_id'")
        ->join("left join t_user on t_user.u_id=t_workflow_record.u_id")
        ->select();

        //查询当前稿件关联的微博
        $Weibo=M("weibo");
        $w_id=$document['d_relatedWeibo'];
        if($w_id){
            $weibo=$Weibo->where("w_id='$w_id'")->find();
            $this->assign("weibo",$weibo);
        }
        $this->assign("tag",$this->getData("tag","1=1",1));
        $this->assign("document",$document);
        $this->assign("record",$record);
        //$this->assign("leaderList",$leaderList);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加附件页
     +--------------------------------
     * @date: 2019年4月30日 上午10:28:25
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_add_attachment_layer(){
        $this->assign("d_id",$_GET['id']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 附件上传操作
     +--------------------------------
     * @date: 2018年11月29日 下午3:09:58
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_add_attachment_handler(){
        import('ORG.Net.UploadFile');
        $originName=$_FILES['file']['name'];//原始文件名
        $name_array=explode('.',$originName);
        $count=count($name_array);
        $format=$name_array[$count-1];
        $size=$_FILES['file']['size'];//原始文件大小 */
        $upload = new UploadFile();// 实例化上传类
        
        $upload->savePath=C('ATTACHMENT_PATH'); // 设置附件上传目录
        $randomName=GetRandStr(8);
        $filename=$randomName.".".$format;//文件名
        $upload->saveRule=$randomName;
        $upload->uploadReplace=true;
        if(!$upload->upload()) {
            // 上传错误提示错误信息
            $result=array(
                'code'=>1,
                'msg'=>'上传失败'
            );
            echo json_encode($result);
        }else{
            $data['d_id']=$_GET['id'];
            $data['a_size']=$size;
            $data['a_filename']=$originName;
            $data['a_savename']=$filename;
            $data['a_format']=$format;
            
            //保存到meeting_attachment表
            $Attachment=M("attachment");
            $Attachment->add($data);
            $result=array(
                'code'=>0,
                'msg'=>'上传成功',
            );
            echo json_encode($result);
        }
    }
    
    /**
     +--------------------------------
     * 删除附件操作
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_del_attachment_handler(){
        if(!$_POST['data']){
            $this->ajaxReturn('','操作失败',0);
        }
        $Attachment=M("attachment");
        foreach($_POST['data'] as $v){
            $Attachment->where("d_id=$v[d_id] and a_savename='$v[a_savename]'")->delete();
            //删除附件文件
            //unlink(C("ATTACHMENT_PATH").$v[a_savename]);
        }
        $this->ajaxReturn('','操作成功',1);
    }
    
    /**
     +--------------------------------
     * 附件预览层
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_attachment_show_layer(){
        $format=$_GET['format'];
        $savename=$_GET['savename'];
        if($format=="jpg"){
            $html='<img src="'.C("FILE_URL").$savename.'" width="100%">';
        }elseif($format=="pdf"){
            $html=C("FILE_URL").$savename;
            $this->assign("format","pdf");
        }else{
            $html="该文件格式不支持预览";
        }
        $this->assign("html",$html);
        $this->display();
    }
    
    
    //判断是否为领导
    public function _before_document_all_table(){
        if($_SESSION['username']=='admin'){
            $this->display();
        }
        if($_SESSION['duty']!="领导"&&$_SESSION['duty2']!="领导"){
            echo "你没有查看所有稿件的权限！";
            exit();
        }
    }
    
    /**
     +--------------------------------
     * 复制审核过的稿件到新建稿件库
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_copy_document_handler(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");
        
        $document["d_id"]=time().rand(10000,99999);
        $document["d_createTime"]=date("Y-m-d H:i:s",time());
        $document["d_author"]=$_SESSION['realname'];
        $document["d_createPerson"]=$_SESSION[userID];
        $document["d_status"]=1;
        
        $result=$this->addData("document", $document);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 全部稿件，领导职务才能浏览
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_all_table(){
        $this->display();
    }
    
    /**
     +--------------------------------
     * 领导介入，稿件退回，稿件状态置为新建
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_back_document_handler(){
        $d_id=$_GET['d_id'];
        $Document=M("document");
        //清空之前的审核意见
        $res=$Document->where("d_id='$d_id'")
        ->setField(array("d_status","d_passTime","d_failedReason","d_failedPerson","d_mediaAuditPerson","d_copyrightAuditPerson","d_leaderAuditPerson"
            ,"d_publishPerson","d_failedReason","d_failedPerson","d_passReason","d_mediaAuditReason","d_copyrightAuditReason","d_leaderAuditReason","d_mediaAuditTime","d_copyrightAuditTime","d_leaderAuditTime")
            ,array(1,"","","","","","","","","","","","","","","",""));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    /**
     +--------------------------------
     * 新建稿件页，提交审核时检查是否保存
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_checksave_handler(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("document","d_id='$d_id'");
        if($document){
            $this->ajaxReturn("","",1);
        }else{
            $this->ajaxReturn("","",0);
        }
    }
    
    /**
     +--------------------------------
     * 稿件关联页
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_relate_layer(){
        $this->assign("w_id",$_GET['w_id']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 稿件关联操作
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_relate_handler(){
        $d_id=$_POST['d_id'];
        $w_id=$_POST['w_id'];
        $Document=M("document");
        
        //查询是否有稿件已经关联当前的微博
       /*  $res=$Document->where("d_id='$d_id'")->find();
        if($res['d_relatedWeibo']){
            $this->ajaxReturn("","当前微博已存在关联稿件",0);
        } */
        $Document->where("d_id='$d_id'")->setField("d_relatedWeibo","");
        $res=$Document->where("d_id='$d_id'")->setField("d_relatedWeibo",$w_id);
        if($res){
            $Weibo=M("weibo");
            $Weibo->where("w_id='$w_id'")->setField("w_ifRelate",1);
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    /**
     +--------------------------------
     * 删除关联操作
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function document_del_relate_handler(){
        $d_id=$_POST['d_id'];
        $w_id=$_POST['w_id'];
        $Document=M("document");
        $res=$Document->where("d_id='$d_id'")->setField("d_relatedWeibo","");
        if($res){
            $Weibo=M("weibo");
            $Weibo->where("w_id='$w_id'")->setField("w_ifRelate",0);
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    //获取爆料
    public function getBaoliao($id){
        $Baoliao=M('baoliao');
        $baoliao=$Baoliao->where("d_id='$id'")->find();
        $this->assign("baoliao",$baoliao);
    }

     /**
    +--------------------------------
    * 未通过理由层
    +--------------------------------
    * @date: 2019年7月23日 下午4:11:48
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_audit_failed_reason_layer(){
        $this->assign("d_id",$_GET['d_id']);
        $this->display();
    }

        /**
    +--------------------------------
    * 未通过操作
    +--------------------------------
    * @date: 2019年7月23日 下午4:12:46
    * @author: zt
    * @param: variable
    * @return:
    */
    public function document_audit_failed_handler(){
        $d_id=$_POST['d_id'];
        $reason=$_POST['d_failedReason'];
        $Document=M("document");
        $res=$Document->where("d_id='$d_id'")
        ->setField(array("d_status","d_editAuditPerson","d_mediaAuditPerson","d_copyrightAuditPerson","d_leaderAuditPerson","d_failedPerson","d_failedReason"),
            array(-1,'','','','',$_SESSION['userID'],$reason));

             //保存稿件流程信息
             $this->workflowRecord($_POST['d_id'],"编辑审核未通过",$_POST['d_failedReason']);

        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

     
}