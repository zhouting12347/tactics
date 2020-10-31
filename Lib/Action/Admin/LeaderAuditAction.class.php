<?php
class LeaderAuditAction extends CommonAction{
    //稿件流程
    //素材库
    //1.创建状态 2待审核 3审核通过
    
    //稿库
    //1创建状态     2媒体部待审核     3版权待审核     4领导待审核 5待发布稿件 
    
    //  创建人          媒体部审核人         版权审核        领导审核     发布稿件人
        
	/**
	+--------------------------------
	* 领导审核主页列表
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function leader_audit_table(){
        $this->display();
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
    public function leader_audit_choose_audit_person_layer(){
        $User=M('user');
        $user=$User->where("u_id!=177")->select();
        $this->assign('user',$user);
        $this->assign("d_id",$_GET['d_id']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 通过意见层
     +--------------------------------
     * @date: 2019年7月23日 下午4:11:48
     * @author: zt
     * @param: variable
     * @return:
     */
    public function leader_audit_passed_reason_layer(){
        $this->assign("d_id",$_GET['d_id']);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 稿件提交到待发稿库
    * 该领导通过审核之后。再查询后续是否还有领导需要审核
    +--------------------------------
    * @date: 2019年7月23日 下午2:58:25
    * @author: zt
    * @param: variable
    * @return:
    */
    public function leader_audit_submit_handler(){
        $d_id=$_POST['d_id'];
        $Document=M("document");
        $LeaderAuditList=M('leaderauditlist');
        
        //如果通过理由未填写，默认理由为“通过”
        if(!$_POST['d_passReason']){
            $_POST['d_passReason']="通过";
        }
        
        //设置当前领导的审核状态l_status为1
        $LeaderAuditList->where("d_id='$d_id' and l_userID='$_SESSION[userID]'")->setField("l_status",1);
        
        //是否需要领导审核,按顺序查询到下一个需要审核的领导，
        //如果未查询到，那么稿件就进入代发稿库
        $ifAudit=$LeaderAuditList->where("d_id='$d_id' and l_status=0")->order("l_num")->find();
        
        //领导审核意见和领导审核时间
        $document=$Document->where("d_id='$d_id'")->find();
        $document['d_leaderAuditReason']?$leaderAuditReason=$document['d_leaderAuditReason']."##".$_POST['d_passReason']:$leaderAuditReason=$_POST['d_passReason'];
        $document['d_leaderAuditTime']?$leaderAuditTime=$document['d_leaderAuditTime']."##".date("Y-m-d H:i:s",time()):$leaderAuditTime=date("Y-m-d H:i:s",time());
        
        if($ifAudit){
            $res=$Document->where("d_id='$d_id'")
            ->setField(array("d_status","d_leaderAuditPerson","d_passReason","d_leaderAuditReason","d_leaderAuditTime")
                ,array(4,$ifAudit['l_userID'],$_POST['d_passReason'],$leaderAuditReason,$leaderAuditTime));

                 //保存稿件流程信息
                 $this->workflowRecord($_POST['d_id'],"领导审核通过",$_POST['d_passReason']);
        }else{
            $Document->where("d_id='$d_id'")->setField("d_leaderAuditPerson","");
            //记录通过审核时间
            $res=$Document->where("d_id='$d_id'")
            ->setField(array("d_status","d_passTime","d_passReason","d_leaderAuditReason","d_leaderAuditTime")
                ,array(5,date("Y-m-d H:i:s",time()),$_POST['d_passReason'],$leaderAuditReason,$leaderAuditTime));

                 //保存稿件流程信息
                 $this->workflowRecord($_POST['d_id'],"领导审核通过",$_POST['d_passReason']);
        }
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
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
    public function leader_audit_failed_reason_layer(){
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
    public function leader_audit_failed_handler(){
        $d_id=$_POST['d_id'];
        $reason=$_POST['d_failedReason'];
        $Document=M("document");
        
        if(!$reason){
            $reason="无";
        }
        
        //领导审核意见和领导审核时间
        $document=$Document->where("d_id='$d_id'")->find();
        $document['d_leaderAuditReason']?$leaderAuditReason="##".$reason:$leaderAuditReason=$reason;
        $document['d_leaderAuditTime']?$leaderAuditTime="##".date("Y-m-d H:i:s",time()):$leaderAuditTime=date("Y-m-d H:i:s",time());
        
        $res=$Document->where("d_id='$d_id'")
        ->setField(array("d_status","d_mediaAuditPerson","d_copyrightAuditPerson","d_leaderAuditPerson","d_failedPerson","d_failedReason","d_leaderAuditReason","d_leaderAuditTime")
            ,array(-1,'','','',$_SESSION['userID'],$reason,$leaderAuditReason,$leaderAuditTime));

             //保存稿件流程信息
             $this->workflowRecord($_POST['d_id'],"领导审核不通过",$_POST['d_failedReason']);
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
}