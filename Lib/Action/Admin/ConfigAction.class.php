<?php
class ConfigAction extends CommonAction{
    /***************************************************************标签**********************************************************/
	/**
	+--------------------------------
	* 标签表格
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function config_tag_table(){
        $this->display();
    }

    /**
    +--------------------------------
    * 添加标签页
    +--------------------------------
    * @date: 2019年5月5日 下午1:44:15
    * @author: zt
    * @param: variable
    * @return:
    */
    public function config_add_tag_layer(){
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加标签操作
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function config_add_tag_handler(){
        $result=$this->addData("tag",$_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 编辑标签页
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function config_edit_tag_layer(){
        $id=$_GET['id'];
        $data=$this->getData("tag","t_id=$id");
        $this->assign("data",$data);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 编辑标签操作
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function config_edit_tag_handler(){
        $condition="t_id=$_POST[t_id]";
        $result=$this->editData("tag",$_POST,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 删除标签操作
     +--------------------------------
     * @date: 2019年3月20日 下午4:35:32
     * @author: zt
     * @param: variable
     * @return:
     */
    public function config_del_tag_handler(){
        $id=$_GET['id'];
        $condition="t_id=$id";
        $result=$this->delData("tag",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    /***************************************************************END标签**********************************************************/
    
}