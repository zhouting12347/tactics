<?php
class InterviewlistAction extends CommonAction{
    //采访单状态
    //1创建 2审核中 3审核通过 -1审核不通过
    /**
	+--------------------------------
	* 新建采访单列表页
	+--------------------------------
	* @date: 2020年6月12日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function interviewlist_create_table(){
        $this->display();
    }

    /**
     +--------------------------------
     * 采访单创建层
     +--------------------------------
     * @date: 2020年6月13日 上午00:53:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function interviewlist_add_layer(){
        $this->assign("i_id",time().rand(10000,99999));
        $this->display();
    }
    

     /**
    +--------------------------------
    * 采访单保存操作
    +--------------------------------
    * @date: 2020年6月12日 上午2:59:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function interviewlist_save_handler(){
        //$_POST['i_content']=urldecode(base64_decode($_POST['i_content']));//base64解码
        $_POST['i_createTime']=date("Y-m-d H:i:s",time()); //创建时间
        $_POST['i_createPerson']=$_SESSION['userID'];
        //if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        //{
            //$_POST['i_content']=stripslashes($_POST['i_content']);//将字符串进行处理
        //}
        $_POST['i_status']=1;
        $result=$this->addData("interviewlist", $_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }

    public function interviewlist_check_layer(){
        $i_id=$_GET['i_id'];
        $document=$this->getData("interviewlist","i_id='$i_id'");
        $this->assign("document",$document);
        $this->display();
    }

    //采访单保存并提交审核
    public function interviewlist_save_submit_handler(){
        //新建采访单保存
        if($_GET['type']=="new"){
            $_POST['i_createTime']=date("Y-m-d H:i:s",time()); //创建时间
            $_POST['i_createPerson']=$_SESSION['userID'];
            $_POST['i_status']=1;
            $this->addData("interviewlist", $_POST);
        }elseif($_GET['type']=="save"){//采访单编辑保存
            $this->editData("interviewlist", $_POST,"i_id=$_POST[i_id]");
        }

        //提交审核
        $i_id=$_POST['i_id'];
        $interviewlist=M("interviewlist");
        $res=$interviewlist->where("i_id='$i_id'")->setField(array("i_status","i_failedReason"),array(2,""));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

    /**
     +--------------------------------
     * 采访单编辑层
     +--------------------------------
     * @date: 2020年6月13日 上午01:28:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function interviewlist_edit_layer(){
        $i_id=$_GET['i_id'];
        $interviewlist=$this->getData("interviewlist","i_id='$i_id'");
        $this->assign("interviewlist",$interviewlist);
        $this->display();
    }

    

    /**
    +--------------------------------
    * 采访单编辑操作
    +--------------------------------
    * @date: 2020年6月13日 上午01:45:22
    * @author: zt
    * @param: variable
    * @return:
    */
    public function interviewlist_edit_handler(){
        //$_POST['d_content']=urldecode(base64_decode($_POST['d_content']));//base64解码
        //if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        //{
            //$_POST['d_content']=stripslashes($_POST['d_content']);//将字符串进行处理
        //}
        $result=$this->editData("interviewlist", $_POST,"i_id=$_POST[i_id]");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }

     /**
    +--------------------------------
    * 删除采访单操作
    +--------------------------------
    * @date: 2020年6月13日 上午02:00:22
    * @author: zt
    * @param: variable
    * @return:
    */
    public function interviewlist_del_handler(){
        $i_id=$_GET['i_id'];
        $result=$this->delData("interviewlist","i_id='$i_id'");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }

    //采访单提交审核
    public function interviewlist_submit_handler(){
        $i_id=$_GET['i_id'];
        $interviewlist=M("interviewlist");
        $res=$interviewlist->where("i_id='$i_id'")->setField(array("i_status","i_failedReason"),array(2,""));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

    //采访单审核列表
    public function interviewlist_audit_table(){
        //dump($_SESSION);
        $this->display();
    }

    //采访单通过审核
    public function interviewlist_pass_audit_handler(){
        $i_id=$_GET['i_id'];
        $interviewlist=M("interviewlist");
        $res=$interviewlist->where("i_id='$i_id'")->setField(array("i_status","i_auditPerson","i_auditTime"),array(3,$_SESSION['userID'],date("Y-m-d H:i:s",time())));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

      /**
    +--------------------------------
    * 采访单未通过理由层
    +--------------------------------
    * @date: 2019年7月23日 下午4:11:48
    * @author: zt
    * @param: variable
    * @return:
    */
    public function interviewlist_audit_failed_reason_layer(){
        $this->assign("i_id",$_GET['i_id']);
        $this->display();
    }

     /**
    +--------------------------------
    * 采访单未通过操作
    +--------------------------------
    * @date: 2019年7月23日 下午4:12:46
    * @author: zt
    * @param: variable
    * @return:
    */
    public function interviewlist_audit_failed_handler(){
        $i_id=$_POST['i_id'];
        $reason=$_POST['i_failedReason'];
        $interviewlist=M("interviewlist");
        $res=$interviewlist->where("i_id='$i_id'")
        ->setField(array("i_status","i_auditPerson","i_auditTime","i_failedReason"),array(-1,$_SESSION['userID'],date("Y-m-d H:i:s",time()),$reason));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    /**
     +--------------------------------
     * 新建采访单页，提交审核时检查是否保存
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function interviewlist_checksave_handler(){
        $i_id=$_GET['i_id'];
        $interviewlist=$this->getData("interviewlist","i_id='$i_id'");
        if($interviewlist){
            $this->ajaxReturn("","",1);
        }else{
            $this->ajaxReturn("","",0);
        }
    }
    
    /**
     +--------------------------------
     * 采访单预览
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function interviewlist_preview_layer(){
        $i_id=$_GET['i_id'];
        $interviewlist=$this->getData("interviewlist","i_id='$i_id'");
        $this->assign("interviewlist",$interviewlist);
        $this->display();
    }
}
?>