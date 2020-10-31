<?php
class CustomerAction extends CommonAction{
	/**
	+--------------------------------
	* 客户表页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function customer_customer_table(){
        $this->assign('user',$this->getData("user","CompanyID=$_SESSION[company_id] and U_IfUse=1",1)); //用户-销售
        $this->display();
    }
   
    /**
     +--------------------------------
     * 添加客户页
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function customer_add_customer_layer(){
        $this->assign('user',$this->getData("user","CompanyID=$_SESSION[company_id] and U_IfUse=1",1)); //用户-销售
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加客户操作
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function customer_add_customer_handler(){
        $_POST['C_ID']=time();
        $_POST['C_RegTime']=date("Y-m-d H:i:s",time());
        $result=$this->addData("customers",$_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 编辑客户页
     +--------------------------------
     * @date: 2019年3月28日 下午1:49:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function customer_edit_customer_layer(){
        $id=$_GET['id'];
        $this->assign("data",$this->getData("customers","C_ID=$id"));
        $this->assign('user',$this->getData("user","CompanyID=$_SESSION[company_id] and U_IfUse=1",1)); //用户-销售
        $this->display();
    }
    
    /**
     +--------------------------------
     * 编辑客户操作
     +--------------------------------
     * @date: 2019年3月28日 下午1:49:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function customer_edit_customer_handler(){
        $condition="C_ID=$_POST[C_ID]";
        $result=$this->editData("customers",$_POST,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 删除客户操作
     +--------------------------------
     * @date: 2019年3月20日 下午4:35:32
     * @author: zt
     * @param: variable
     * @return:
     */
    public function customer_del_customer_handler(){
        $id=$_GET['id'];
        $condition="C_ID=$id";
        $result=$this->delData("customers",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 查询customer order 页
    +--------------------------------
    * @date: 2019年5月28日 上午10:18:25
    * @author: zt
    * @param: variable
    * @return:
    */
    public function customer_search_order_layer(){
        $this->assign("C_ID",$_GET['id']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * customer页，新建订单页
     +--------------------------------
     * @date: 2019年4月25日 下午4:27:47
     * @author: zt
     * @param: variable
     * @return:
     */
    public function customer_add_order_layer(){
        $this->assign("user",$this->getData("user","CompanyID=$_SESSION[company_id] and U_IfUse=1",1));
        $this->assign("C_ID",$_GET['id']);
        $this->assign("C_Name",$_GET['name']);
        $this->assign("orderID",time().$_SESSION['company_id']);
        $this->display();
    }
}