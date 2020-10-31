<?php
class BaoliaoAction extends CommonAction{
    
    /**
	+--------------------------------
	* 新建爆料列表页
	+--------------------------------
	* @date: 2020年6月12日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function baoliao_create_table(){
        $this->display();
    }

    /**
     +--------------------------------
     * 爆料创建层
     +--------------------------------
     * @date: 2020年6月13日 上午00:53:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function baoliao_add_layer(){
        $this->assign("d_id",time().rand(10000,99999));
        $this->assign("createtime",date("Y-m-d H:i:s",time()));
        $this->display();
    }
    

     /**
    +--------------------------------
    * 爆料保存操作
    +--------------------------------
    * @date: 2020年6月12日 上午2:59:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function baoliao_save_handler(){
        $_POST['d_content']=urldecode(base64_decode($_POST['d_content']));//base64解码
        $_POST['d_createTime']=date("Y-m-d H:i:s",time()); //创建时间
        $_POST['d_createPerson']=$_SESSION['userID'];
        if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        {
            $_POST['d_content']=stripslashes($_POST['d_content']);//将字符串进行处理
        }
        $_POST['d_status']=1;
        $result=$this->addData("baoliao", $_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }

    public function baoliao_check_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("baoliao","d_id='$d_id'");
        $this->assign("document",$document);
        $this->display();
    }

    /**
     +--------------------------------
     * 爆料编辑层
     +--------------------------------
     * @date: 2020年6月13日 上午01:28:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function baoliao_edit_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("baoliao","d_id='$d_id'");
        $attachmentCount=$this->getDataCount("attachment","d_id='$d_id'");
        $this->assign("document",$document);
        $this->assign("attachmentCount",$attachmentCount);
        $this->display();
    }

     /**
     +--------------------------------
     * 爆料审核人员编辑层
     +--------------------------------
     * @date: 2020年6月13日 上午01:28:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function baoliao_audit_edit_layer(){
        $d_id=$_GET['d_id'];
        $document=$this->getData("baoliao","d_id='$d_id'");
        $attachmentCount=$this->getDataCount("attachment","d_id='$d_id'");
        $this->assign("document",$document);
        $this->assign("attachmentCount",$attachmentCount);
        $this->display();
    }

    /**
    +--------------------------------
    * 爆料编辑操作
    +--------------------------------
    * @date: 2020年6月13日 上午01:45:22
    * @author: zt
    * @param: variable
    * @return:
    */
    public function baoliao_edit_handler(){
        $_POST['d_content']=urldecode(base64_decode($_POST['d_content']));//base64解码
        if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        {
            $_POST['d_content']=stripslashes($_POST['d_content']);//将字符串进行处理
        }
        $result=$this->editData("baoliao", $_POST,"d_id=$_POST[d_id]");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }

     /**
    +--------------------------------
    * 删除爆料操作
    +--------------------------------
    * @date: 2020年6月13日 上午02:00:22
    * @author: zt
    * @param: variable
    * @return:
    */
    public function baoliao_del_handler(){
        $d_id=$_GET['d_id'];
        $result=$this->delData("baoliao","d_id='$d_id'");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }

    //爆料提交审核
    public function baoliao_submit_handler(){
        $d_id=$_GET['d_id'];
        $Baoliao=M("baoliao");
        $res=$Baoliao->where("d_id='$d_id'")->setField("d_status",2);
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

    //爆料审核列表
    public function baoliao_audit_table(){
        //dump($_SESSION);
        $this->display();
    }

    //爆料通过审核
    public function baoliao_pass_audit_handler(){
        $d_id=$_GET['d_id'];
        $Baoliao=M("baoliao");
        $res=$Baoliao->where("d_id='$d_id'")->setField(array("d_status","d_leaderAuditPerson"),array(3,$_SESSION['userID']));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

      /**
    +--------------------------------
    * 爆料未通过理由层
    +--------------------------------
    * @date: 2019年7月23日 下午4:11:48
    * @author: zt
    * @param: variable
    * @return:
    */
    public function baoliao_audit_failed_reason_layer(){
        $this->assign("d_id",$_GET['d_id']);
        $this->display();
    }

     /**
    +--------------------------------
    * 爆料未通过操作
    +--------------------------------
    * @date: 2019年7月23日 下午4:12:46
    * @author: zt
    * @param: variable
    * @return:
    */
    public function baoliao_audit_failed_handler(){
        $d_id=$_POST['d_id'];
        $reason=$_POST['d_failedReason'];
        $Baoliao=M("baoliao");
        $res=$Baoliao->where("d_id='$d_id'")
        ->setField(array("d_status","d_leaderAuditPerson","d_failedReason"),array(-1,$_SESSION['userID'],$reason));
        if($res!==false){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    
    /**
     +--------------------------------
     * 新建爆料页，提交审核时检查是否保存
     +--------------------------------
     * @date: 2018年11月29日 下午3:35:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function baoliao_checksave_handler(){
        $d_id=$_GET['d_id'];
        $baoliao=$this->getData("baoliao","d_id='$d_id'");
        if($baoliao){
            $this->ajaxReturn("","",1);
        }else{
            $this->ajaxReturn("","",0);
        }
    }
    
}
?>