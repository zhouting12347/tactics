<?php
class PersonalAction extends CommonAction{
	//修改密码页
	public function changePassword(){
		$this->display('changePassword');
	}
	
	//修改密码操作
	public function changePasswordHandler(){
		$M_ID=$_SESSION['userID'];//用户ID
		if($_POST['new_password']!=$_POST['confirm_password']){
			$this->ajaxReturn("","新密码输入不一致!",0);
		}
		if($_POST['new_password']==null||$_POST['confirm_password']==null){
			$this->ajaxReturn("","密码不能为空!",0);
		}
		$User=M('user');
		$user=$User->where("u_id='$M_ID'")->find();
		if(md5($_POST['old_password'])!=$user['u_password']){
			$this->ajaxReturn("","旧密码错误!",0);
		}else if(md5($_POST['old_password'])==$user['u_password']){
		    $res=$User->where("u_id='$M_ID'")->setField("u_password",md5($_POST['new_password']));
			if($res){
			    session_unset();
			    session_destroy();
				$this->ajaxReturn("","修改成功!",1);
			}else{
				$this->ajaxReturn("","操作失败!",0);
			}
		}
	}
	
	
}