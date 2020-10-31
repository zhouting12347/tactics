<?php
class AddressbookAction extends CommonAction{
	/**
	+--------------------------------
	* 通讯录主页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function addressbook_table(){
        $this->display();
    }
   
    
    /**
     +--------------------------------
     * 添加用户操作
     +--------------------------------
     * @date: 2019年6月12日 下午2:39:50
     * @author: zt
     * @param: variable
     * @return:
     */
    public function addressbook_add_handler(){
        $result=$this->addData("addressbook",$_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    
    
    /**
     +--------------------------------
     * 编辑用户页
     +--------------------------------
     * @date: 2019年6月12日 下午3:12:01
     * @author: zt
     * @param: variable
     * @return:
     */
    public function addressbook_edit_layer(){
        $a_id=$_GET['id'];
        $condition="a_id='$a_id'";
        $this->assign("data",$this->getData("addressbook", $condition));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 用户编辑操作
     +--------------------------------
     * @date: 2019年6月12日 下午1:41:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function addressbook_edit_handler(){
        $condition="a_id='$_POST[a_id]'";
        $result=$this->editData("addressbook",$_POST,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 用户删除操作
     +--------------------------------
     * @date: 2019年6月12日 下午1:41:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function addressbook_del_handler(){
        $condition="a_id='$_GET[id]'";
        $result=$this->delData("addressbook",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
}