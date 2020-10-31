<?php
class PlayerAction extends CommonAction{
	/**
	+--------------------------------
	* 机顶盒表格页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function player_player_table(){
        $this->assign("playertype",$this->getData("playertype","CompanyID=$_SESSION[company_id]",1));
        $this->assign("area",$this->getData("area","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 机顶盒添加页
    +--------------------------------
    * @date: 2019年4月24日 下午1:27:23
    * @author: zt
    * @param: variable
    * @return:
    */
    public function player_add_layer(){
        $this->assign("playertype",$this->getData("playertype","CompanyID=$_SESSION[company_id]",1));
        $this->assign("area",$this->getData("area","CompanyID=$_SESSION[company_id]",1));
        $this->assign("user",$this->getData("user","CompanyID=$_SESSION[company_id]",1));
        $this->assign("playermac",$this->getData("playermac","PM_CompanyID=$_SESSION[company_id] and PM_IfUse=0",1));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 机顶盒添加操作
     +--------------------------------
     * @date: 2019年4月24日 下午1:27:23
     * @author: zt
     * @param: variable
     * @return:
     */
    public function player_add_handler(){
        $_POST['P_ID']=time();
        $_POST['P_Activation']=date("Y-m-d H:i:s",time());
        $result=$this->addData("player",$_POST);
        if($result['status']==1){
            //更新playermac表状态
            $PlayerMac=M('playermac');
            $PlayerMac->where("PM_Mac='$_POST[P_SN]'")->setField(array("PM_ActiveTime","PM_IfUse"),array(date("Y-m-d H:i:s",time()),1));
        }
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 机顶盒编辑页
     +--------------------------------
     * @date: 2019年4月24日 下午1:27:23
     * @author: zt
     * @param: variable
     * @return:
     */
    public function player_edit_layer(){
        $id=$_GET['id'];
        $this->assign("player",$this->getData("player","P_ID=$id"));
        $this->assign("playertype",$this->getData("playertype","CompanyID=$_SESSION[company_id]",1));
        $this->assign("area",$this->getData("area","CompanyID=$_SESSION[company_id]",1));
        $this->assign("user",$this->getData("user","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 机顶盒编辑操作
     +--------------------------------
     * @date: 2019年4月24日 下午1:27:23
     * @author: zt
     * @param: variable
     * @return:
     */
    public function player_edit_handler(){
        $condition="P_ID=$_POST[P_ID]";
        $result=$this->editData("player",$_POST,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 机顶盒删除操作
     +--------------------------------
     * @date: 2019年4月24日 下午1:27:23
     * @author: zt
     * @param: variable
     * @return:
     */
    public function player_del_handler(){
        $id=$_GET['id'];
        $condition="P_ID=$id";
        $result=$this->delData("player",$condition);
        if($result['status']==1){
            //更新playermac表状态
            $PlayerMac=M('playermac');
            $PlayerMac->where("PM_Mac='$_GET[mac]'")->setField("PM_IfUse",0);
        }
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * ajax获取playermac信息 
    +--------------------------------
    * @date: 2019年6月19日 上午11:13:17
    * @author: zt
    * @param: variable
    * @return:
    */
    public function player_ajax_playermac(){
        $ID=$_POST['ID'];
        $PlayerMac=M('playermac');
        $playermac=$PlayerMac->where("PM_PID='$ID'")->find();
        $this->ajaxReturn($playermac,'',1);
    }
}