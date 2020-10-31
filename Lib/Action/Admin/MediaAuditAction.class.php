<?php
class MediaAuditAction extends CommonAction{
    //稿件流程
    //素材库
    //1.创建状态 2待审核 3审核通过
    
    //稿库
    //1创建状态     2媒体部待审核     3版权待审核     4领导待审核 5待发布稿件 
    
    //  创建人          媒体部审核人         版权审核        领导审核     发布稿件人
        
	/**
	+--------------------------------
	* 媒体审核主页列表
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function media_audit_table(){
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
    public function media_audit_choose_audit_person_layer(){
        $d_id=$_GET['d_id'];
        $User=M('user');
        $LeaderAuditList=M("leaderauditlist");
        $leader=$LeaderAuditList->where("d_id='$d_id'")->join("left join t_user on t_user.u_id=t_leaderauditlist.l_userID")->order("l_num")->select();
        if($leader){
            foreach ($leader as $key=>$v){
                if($key+1<count($leader)){
                    $leadername.=$v[u_realname]."、";
                }else{
                    $leadername.=$v[u_realname];
                }
            }
        }else{
            $leadername="无";
        }
        $user=$User->where("u_id!=177")->select();
        $this->assign('user',$user);
        $this->assign('leadername',$leadername);
        $this->assign("d_id",$d_id);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 稿件提交到领导审核
    * 版权审核之后需要查询是否存在有领导审核，如果存在领导审核，按照leaderauditlist表中的顺序走审核流程
    +--------------------------------
    * @date: 2019年7月23日 下午2:58:25
    * @author: zt
    * @param: variable
    * @return:
    */
    public function media_audit_submit_handler(){
        unset($_SESSION["tempPassReason"]);
        $d_id=$_POST['d_id'];
        $Document=M("document");
        $LeaderAuditList=M('leaderauditlist');
        $ifAudit=$LeaderAuditList->where("d_id='$d_id' and l_status=0")->order("l_num")->find();
        $document=$Document->where("d_id='$d_id'")->find();

        //保存审核日志
        $this->auditLog($d_id,$document['d_content']);
        
        //如果通过理由未填写，默认理由为“通过”
        if(!$_POST['d_passReason']){
            $_POST['d_passReason']="通过";
        }

        //是否需要领导审核,按顺序查询到下一个需要审核的领导，
        //如果未查询到，那么稿件就进入代发稿库
        if($ifAudit){
            if($document['d_classify']=="统发稿"&&$document['d_status']==3){
                $res=$Document->where("d_id='$d_id'")
                ->setField(array("d_status","d_leaderAuditPerson","d_passReason","d_copyrightAuditReason","d_copyrightAuditTime")
                ,array(4,$ifAudit['l_userID'],$_POST['d_passReason'],$_POST['d_passReason'],date("Y-m-d H:i:s",time())));
            }else{
                $res=$Document->where("d_id='$d_id'")
                ->setField(array("d_status","d_leaderAuditPerson","d_passReason","d_mediaAuditReason","d_mediaAuditTime")
                ,array(4,$ifAudit['l_userID'],$_POST['d_passReason'],$_POST['d_passReason'],date("Y-m-d H:i:s",time())));
            }
             //保存稿件流程信息
             $this->workflowRecord($_POST['d_id'],"责编审核通过",$_POST['d_passReason']);
        }else{
            //记录通过审核时间
            if($document['d_classify']=="统发稿"&&$document['d_status']==3){
                $res=$Document->where("d_id='$d_id'")
                ->setField(array("d_status","d_passTime","d_passReason","d_copyrightAuditReason","d_copyrightAuditTime")
                ,array(5,date("Y-m-d H:i:s",time()),$_POST['d_passReason'],$_POST['d_passReason'],date("Y-m-d H:i:s",time())));
            }else{
                $res=$Document->where("d_id='$d_id'")
                ->setField(array("d_status","d_passTime","d_passReason","d_mediaAuditReason","d_mediaAuditTime")
                ,array(5,date("Y-m-d H:i:s",time()),$_POST['d_passReason'],$_POST['d_passReason'],date("Y-m-d H:i:s",time())));
            }
             //保存稿件流程信息
             $this->workflowRecord($_POST['d_id'],"责编审核通过",$_POST['d_passReason']);
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
    public function media_audit_failed_reason_layer(){
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
    public function media_audit_failed_handler(){
        $d_id=$_POST['d_id'];
        $reason=$_POST['d_failedReason'];
        $Document=M("document");
        $res=$Document->where("d_id='$d_id'")
        ->setField(array("d_status","d_mediaAuditPerson","d_copyrightAuditPerson","d_leaderAuditPerson","d_failedPerson","d_failedReason","d_mediaAuditReason","d_mediaAuditTime"),
            array(-1,'','','',$_SESSION['userID'],$reason,$reason,date("Y-m-d H:i:s",time())));

              //保存稿件流程信息
              $this->workflowRecord($_POST['d_id'],"责编审核不通过",$_POST['d_failedReason']);
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    /**
    +--------------------------------
    * 领导审核层
    +--------------------------------
    * @date: 2019年7月25日 下午2:40:29
    * @author: zt
    * @param: variable
    * @return:
    */
    public function media_audit_choose_leader_layer(){
        $this->assign("d_id",$_GET['d_id']);
        unset($_SESSION["tempPassReason"]);
        $_SESSION["tempPassReason"]=$_GET['passReason'];
        $this->display();
    }
    
    /**
    +--------------------------------
    * 添加领导审核表操作
    +--------------------------------
    * @date: 2019年7月25日 下午3:44:09
    * @author: zt
    * @param: variable
    * @return:
    */
    public function media_audit_choose_leader_handler(){
        $d_id=$_GET['d_id'];
        $LeaderAuditList=M("leaderauditlist");
        $count=$LeaderAuditList->where("d_id='$d_id'")->count();
        $i=1;
        foreach ($_POST['data'] as $v){
            $res=$LeaderAuditList->where("d_id='$d_id' and l_userID='$v[u_id]'")->find();
            if(!$res){
                $data['d_id']=$d_id;
                $data['l_num']=$i+$count;
                $data['l_userID']=$v['u_id'];
                $data['l_status']=0;
                $LeaderAuditList->add($data);
                $i++;
            }
        }
        $this->ajaxReturn('','',1);
    }
    
    /**
     +--------------------------------
     * 删除领导审核表操作
     +--------------------------------
     * @date: 2019年7月25日 下午3:44:09
     * @author: zt
     * @param: variable
     * @return:
     */
    public function media_audit_delete_leader_handler(){
        $d_id=$_GET['d_id'];
        $LeaderAuditList=M("leaderauditlist");
        foreach ($_POST['data'] as $v){
            $LeaderAuditList->where("d_id='$d_id' and l_userID='$v[u_id]'")->delete();
        }
        //选择剩下的数据
        $leaders=$LeaderAuditList->where("d_id='$d_id'")->order('l_num')->select();
        //删除原来的数据
        $LeaderAuditList->where("d_id='$d_id'")->delete();
        //重新排序加入数据库
        foreach($leaders as $key=>$v){
            $data['d_id']=$d_id;
            $data['l_num']=$key+1;
            $data['l_userID']=$v['l_userID'];
            $data['l_status']=0;
            $LeaderAuditList->add($data);
        }
        $this->ajaxReturn('','',1);
    }
    
    /**
    +--------------------------------
    * 领导审核表移动操作
    +--------------------------------
    * @date: 2019年7月25日 下午4:29:38
    * @author: zt
    * @param: variable
    * @return:
    */
    public function media_audit_move_leader_handler(){
        $d_id=$_GET['d_id'];
        $move=$_GET['move'];
        $num=$_GET['num'];
        $userID=$_GET['userID'];
        $LeaderAuditList=M("leaderauditlist");
        if($move=="up"){
            if($num==1){
                $this->ajaxReturn('','',1);
            }
            //得到上一条的userID
            $lastNum=$num-1;
            $lastLeader=$LeaderAuditList->where("d_id='$d_id' and l_num='$lastNum'")->find();
            $lastUserID=$lastLeader['l_userID'];
            $LeaderAuditList->where("d_id='$d_id' and l_userID='$userID'")->setField("l_num",$num-1);
            $LeaderAuditList->where("d_id='$d_id' and l_userID='$lastUserID'")->setField("l_num",$num);
            $this->ajaxReturn('','',1);
        }elseif($move=="down"){
            $count=$LeaderAuditList->where("d_id='$d_id'")->count();
            if($count==$num){
                $this->ajaxReturn('','',1);
            }
            //得到下一条的userID
            $nextNum=$num+1;
            $nextLeader=$LeaderAuditList->where("d_id='$d_id' and l_num='$nextNum'")->find();
            $nextUserID=$nextLeader['l_userID'];
            $LeaderAuditList->where("d_id='$d_id' and l_userID='$userID'")->setField("l_num",$num+1);
            $LeaderAuditList->where("d_id='$d_id' and l_userID='$nextUserID'")->setField("l_num",$num);
            $this->ajaxReturn('','',1);
        }
    }
    
}