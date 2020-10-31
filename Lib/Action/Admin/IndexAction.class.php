<?php
class IndexAction extends CommonAction{
	/**
	+--------------------------------
	* 地铁主页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function index(){
		//dump($_SESSION);die();
        $this->assign("menu",$_SESSION['menu']);
        $this->assign("username",$_SESSION['username']);
        $this->display();
	}
	

	
	public function ab1(){
		$this->display('ab/ab1');
	}

	public function ab1_add_layer(){
		$this->display('ab/ab1_add_layer');
	}

	public function ab1_edit_layer(){
		$Ab_1=M("ab_1");
		$ab_id=$_GET['ab_id'];
		$res=$Ab_1->where("ab_id=$ab_id")->find();
		$this->assign('ab_1',$res);
		$this->display('ab/ab1_edit_layer');
	}

	public function ab1_add_handler(){
		$Ab_1=M("ab_1");
		$res=$Ab_1->add($_POST);
		//echo $Ab_1->getLastSql();
		if($res){
			$this->ajaxReturn("","操作成功",1);
		}else{
			$this->ajaxReturn("","操作失败",0);
		}
	}

	public function ab1_del_handler(){
		$Ab_1=M("ab_1");
		$ab_id=$_GET['ab_id'];
		$res=$Ab_1->where("ab_id=$ab_id")->delete();
		if($res){
			$this->ajaxReturn("","操作成功",1);
		}else{
			$this->ajaxReturn("","操作失败",0);
		}
	}

	public function ab1_edit_handler(){
		$Ab_1=M("ab_1");
		$ab_id=$_POST['ab_id'];
		$res=$Ab_1->where("ab_id=$ab_id")->save($_POST);
		if($res){
			$this->ajaxReturn("","操作成功",1);
		}else{
			$this->ajaxReturn("","操作失败",0);
		}
	}

	/***********************ab 7000_000002 ***************************/
	public function ab2(){
		$this->display('ab/ab2');
	}

	public function ab2_add_layer(){
		$this->display('ab/ab2_add_layer');
	}

	public function ab2_edit_layer(){
		$Ab_2=M("ab_2");
		$ab_id=$_GET['ab_id'];
		$res=$Ab_2->where("ab_id=$ab_id")->find();
		$this->assign('ab_2',$res);
		$this->display('ab/ab2_edit_layer');
	}

	public function ab2_add_handler(){
		$Ab_2=M("ab_2");
		$res=$Ab_2->add($_POST);
		//echo $Ab_2->getLastSql();
		if($res){
			$this->ajaxReturn("","操作成功",1);
		}else{
			$this->ajaxReturn("","操作失败",0);
		}
	}

	public function ab2_del_handler(){
		$Ab_2=M("ab_2");
		$ab_id=$_GET['ab_id'];
		$res=$Ab_2->where("ab_id=$ab_id")->delete();
		if($res){
			$this->ajaxReturn("","操作成功",1);
		}else{
			$this->ajaxReturn("","操作失败",0);
		}
	}

	public function ab2_edit_handler(){
		$Ab_2=M("ab_2");
		$ab_id=$_POST['ab_id'];
		$res=$Ab_2->where("ab_id=$ab_id")->save($_POST);
		if($res){
			$this->ajaxReturn("","操作成功",1);
		}else{
			$this->ajaxReturn("","操作失败",0);
		}
	}

		/***********************ab 7000_000007 ***************************/
		public function ab7(){
			$this->display('ab/ab7');
		}
	
		public function ab7_add_layer(){
			$this->display('ab/ab7_add_layer');
		}
	
		public function ab7_edit_layer(){
			$Ab_7=M("ab_7");
			$ab_id=$_GET['ab_id'];
			$res=$Ab_7->where("ab_id=$ab_id")->find();
			$this->assign('ab_7',$res);
			$this->display('ab/ab7_edit_layer');
		}
	
		public function ab7_add_handler(){
			$Ab_7=M("ab_7");
			$res=$Ab_7->add($_POST);
			//echo $Ab_7->getLastSql();
			if($res){
				$this->ajaxReturn("","操作成功",1);
			}else{
				$this->ajaxReturn("","操作失败",0);
			}
		}
	
		public function ab7_del_handler(){
			$Ab_7=M("ab_7");
			$ab_id=$_GET['ab_id'];
			$res=$Ab_7->where("ab_id=$ab_id")->delete();
			if($res){
				$this->ajaxReturn("","操作成功",1);
			}else{
				$this->ajaxReturn("","操作失败",0);
			}
		}
	
		public function ab7_edit_handler(){
			$Ab_7=M("ab_7");
			$ab_id=$_POST['ab_id'];
			$res=$Ab_7->where("ab_id=$ab_id")->save($_POST);
			if($res){
				$this->ajaxReturn("","操作成功",1);
			}else{
				$this->ajaxReturn("","操作失败",0);
			}
		}
}