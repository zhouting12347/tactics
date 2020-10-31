<?php
//前端公共类
class CommonAction extends Action{
	/**
	 +------------------------------------------------------------------------------
	 * 登录初始化判断
	 +------------------------------------------------------------------------------
	 *
	 +------------------------------------------------------------------------------
	 */
	function _initialize(){
	    /* if(time()>$_SESSION['logintime']){
            unset($_SESSION);
	    } */
	    /* $action=ACTION_NAME;
	    //判断当前操作是否在需要验证的操作中
	    if(in_array($action,$_SESSION['nodes'])){
	        //判断是否有权限
	        if(in_array($action,$_SESSION['accessList'])){
	            return true;
	        }else{
	            $this->error("You have no operation authority");
	        }
	    } */
	}
	
	/**
	+--------------------------------
	* 通用添加数据
	+--------------------------------
	* @date: 2019年3月20日 下午2:41:26
	* @author: zt
	* @param: variable
	* @return:
	*/
	public function addData($table,$data){
	    $Table=D($table);
	    if (!$Table->create($data,1)){
	        $res['info']="操作失败".$Table->getError();
	        $res['status']=0;
	        return $res;
	    }else{
	        $result=$Table->add();
	        if($result){
	           $res['info']="操作成功";
			   $res['status']=1;
	        }else{
	            $res['info']="操作失败";
	            $res['status']=1;
	        }
	        return $res;
	    }
	}
	
	/**
	 +--------------------------------
	 * 通用编辑数据
	 +--------------------------------
	 * @date: 2019年3月20日 下午2:41:26
	 * @author: zt
	 * @param: variable
	 * @return:
	 */
	public function editData($table,$data,$condition){
	    $Table=M($table);
	    $res=$Table->where($condition)->save($data);
	    if($res!==false){
	        $data['info']="操作成功";
	        $data['status']=1;
	    }else{
	        $data['info']="操作失败";
	        $data['status']=0;
	    }
	    return $data;
	}
	
	/**
	 +--------------------------------
	 * 通用删除数据
	 +--------------------------------
	 * @date: 2019年3月20日 下午2:41:26
	 * @author: zt
	 * @param: variable
	 * @return:
	 */
	public function delData($table,$condition){
	    $Table=M($table);
	    $res=$Table->where($condition)->delete();
	    if($res){
	        $data['info']="操作成功";
	        $data['status']=1;
	    }else{
	        $data['info']="操作失败";
	        $data['status']=0;
	    }
	    return $data;
	}
	
	
	/**
	 +--------------------------------
	 * 读取数据
	 +--------------------------------
	 * @date: 2019年3月20日 下午3:47:23
	 * @author: zt
	 * @param: variable
	 * @return:
	 */
	public function getData($tableName,$condition,$multi=0){
	    $Table=M($tableName);
	    if($multi){
	        $res=$Table->where($condition)->select();
	    }else{
	        $res=$Table->where($condition)->find();
	    }
	    return $res;
	}
	
	/**
	+--------------------------------
	* ajax获取数据
	+--------------------------------
	* @date: 2019年4月1日 下午2:16:20
	* @author: zt
	* @param: variable
	* @return:
	*/
	public function ajaxGetData(){
	    $tableName=$_POST['tableName'];
	    $condition=$_POST['condition'];
	    $type=$_POST['type'];
	    $Table=M($tableName);
	    switch($type){
	        case "computerroom":
	            $res=$Table
	            ->join("left join t_person on t_person.P_ID=t_computerroom.P_ID")
	            ->where($condition)->select();
	            break;
	        default:
	            $res=$Table->where($condition)->select();
	    }
	    $this->ajaxReturn($res,'',1);
	}
	
	/**
	 +--------------------------------
	 * 获取数据个数
	 +--------------------------------
	 * @date: 2019年4月1日 下午2:16:20
	 * @author: zt
	 * @param: variable
	 * @return:
	 */
	public function getDataCount($tableName,$condition){
	    $Table=M($tableName);
	    $count=$Table->where($condition)->count();
	    return $count;
	}

	/**
	 +--------------------------------
	 * 工作流程记录
	 +--------------------------------
	 * @date: 2019年4月1日 下午2:16:20
	 * @author: zt
	 * @param: variable
	 * @return:
	 */
	public function workflowRecord($d_id,$flowname,$auditreson){
		$WorkflowRecord=M("workflow_record");
		$data['d_id']=$d_id;
		$data['u_id']=$_SESSION['userID'];
		$data['w_flowname']=$flowname;
		$data['w_auditreason']=$auditreson;
		$data['w_operatetime']=date("Y-m-d H:i:s",time());
		$WorkflowRecord->add($data);
		//echo $WorkflowRecord->getLastSql();
	}

	/**
	 +--------------------------------
	 * 审核日志
	 +--------------------------------
	 * @date: 2019年4月1日 下午2:16:20
	 * @author: zt
	 * @param: variable
	 * @return:
	 */
	public function auditLog($d_id,$content){
		$foldername=date("Ym",time());
        $path=C("WEIBO_LOG_PATH").$foldername."/";
        if (!is_dir($path)){
            $res=mkdir(iconv("UTF-8", "GBK", $path),0777,true); //创建当月的文件夹
        }
        $time="[".date("Y-m-d H:i:s",time())."]\r\n"; //接口访问时间
        $logname=date("Ymd",time())."_audit.txt"; //日志名称
        $filepath=$path.$logname;
        $myfile=fopen($filepath, "a") or die("Unable to open file!");
        fwrite($myfile,$time."id:".$d_id.$content."\r\n\r\n#########################################################################################################################\r\n\r\n");
        fclose($myfile);
	}

}