<?php
class WaitAction extends Action{
	/**
	+--------------------------------
	* 待办主页列表
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function wait_table(){
        $this->display();
	}
	
	/**
	+--------------------------------
	* 待办爆料主页列表
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function wait_baoliao_table(){
		if($_SESSION['baoliaoaudit']){
			$this->display("wait_audit_baoliao_table");
		}else{
			$this->display("wait_create_baoliao_table");
		}
        
	}
   
}