<?php
class SucaiAction extends CommonAction{
    //稿件流程
    //素材库
    //1.创建状态 2待审核 3审核通过
    
    //稿库
    //1创建状态 2 媒体部待审核 3版权待审核 4领导待审核 5待发布稿件 
    
	/**
	+--------------------------------
	* 供稿人待办主页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function sucai_provide_table(){
        $this->assign("username",$_SESSION['username']);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 素材类型选择层
    +--------------------------------
    * @date: 2019年7月18日 上午10:38:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function sucai_provide_type_layer(){
        $this->display();
    }
    
    /**
     +--------------------------------
     * 素材创建层
     +--------------------------------
     * @date: 2019年7月18日 上午10:38:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function sucai_provide_live_layer(){
        $this->assign("d_id",time().rand(10000,99999));
        $this->display();
    }
   
    /**
    +--------------------------------
    * 素材保存操作
    +--------------------------------
    * @date: 2019年7月22日 上午10:59:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function sucai_provide_save_handler(){
        $_POST['d_createTime']=date("Y-m-d H:i:s",time()); //创建时间
        $_POST['d_createPerson']=$_SESSION['userID'];
        if(get_magic_quotes_gpc())//如果get_magic_quotes_gpc()是打开的
        {
            $_POST['d_content']=stripslashes($_POST['d_content']);//将字符串进行处理
        }
        $_POST['d_position']="sucai";
        $_POST['d_status']=1;
        $result=$this->addData("document", $_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
}