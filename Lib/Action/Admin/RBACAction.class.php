<?php
class RBACAction extends CommonAction
{
    //*********************************************用户管理**************************************************
    //用户表格
    public function rbac_user_table(){
        $this->display();
    }
    
    //添加用户页
    public function rbac_add_user_layer(){
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
    public function rbac_add_user_handler(){
        $_POST['u_password']=md5(111111);
        $result=$this->addData("user",$_POST);
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
    public function rbac_edit_user_layer(){
        $u_id=$_GET['id'];
        $condition="u_id='$u_id'";
        $this->assign("data",$this->getData("user", $condition));
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
    public function rbac_edit_user_handler(){
        $condition="u_id='$_POST[u_id]'";
        $result=$this->editData("user",$_POST,$condition);
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
    public function rbac_del_user_handler(){
        $condition="u_id='$_GET[id]'";
        $result=$this->delData("user",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 用户角色分配页
    +--------------------------------
    * @date: 2019年6月12日 下午4:39:43
    * @author: zt
    * @param: variable
    * @return:
    */
    public function rbac_role_access_layer(){
        $Role=M('role');
        $roles=$Role->where("CompanyID='$_SESSION[company_id]'")->select(); //公司下所有角色
        $this->assign("roles",$roles);
        $this->assign("userID",$_GET['id']);
        
        $role_user=M('role_user');
        $userRoles=$role_user->where("user_id='$_GET[id]'")->select(); //用户拥有的角色
        $this->assign('userRoles',$userRoles);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 用户角色分配操作
    +--------------------------------
    * @date: 2019年6月13日 上午10:25:26
    * @author: zt
    * @param: variable
    * @return:
    */
    public function rbac_role_access_handler(){
        $userID=$_POST['userID'];
        //删除该用户已分配的角色
        $role_user=M('role_user');
        $role_user->where("user_id=$userID")->delete();
        
        //添加选中的角色
        foreach($_POST[role_id] as $role_id){
            $role_user->add(array('role_id'=>$role_id,'user_id'=>$userID));
        }
        $this->ajaxReturn("","success",1);
    }
    
    //*********************************************END 用户管理**************************************************
 
    
   
    
    //*************************************************************节点管理******************************************
   
    
    //********************************************************角色管理************************************
    //角色表格
    public function rbac_role_table(){
        $this->display();
    }
    
    //添加角色页
    public function rbac_add_role_layer(){
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加角色操作
     +--------------------------------
     * @date: 2019年6月12日 下午2:39:50
     * @author: zt
     * @param: variable
     * @return:
     */
    public function rbac_add_role_handler(){
        $_POST['CompanyID']=$_SESSION['company_id'];
        $result=$this->addData("role",$_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 编辑角色页
     +--------------------------------
     * @date: 2019年6月12日 下午3:12:01
     * @author: zt
     * @param: variable
     * @return:
     */
    public function rbac_edit_role_layer(){
        $id=$_GET['id'];
        $condition="id='$id'";
        $this->assign("data",$this->getData("role", $condition));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 角色编辑操作
     +--------------------------------
     * @date: 2019年6月12日 下午1:41:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function rbac_edit_role_handler(){
        $condition="id='$_POST[id]'";
        $result=$this->editData("role",$_POST,$condition);
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
    public function rbac_del_role_handler(){
        $condition="id='$_GET[id]'";
        $result=$this->delData("role",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 权限分配页
     +--------------------------------
     * @date: 2019年6月12日 下午1:41:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function rbac_right_access_layer(){
        $roleId=$_GET[id];
        $Node=M('node');
        //取出全部控制器节点
        $res=$Node->where('pid=1')->select();
        //取出控制器所有对应的操作节点
        foreach($res as $k=>$v){
            $child_nodes=$Node->where("pid=$v[id]")->select();
            $res[$k][]=$child_nodes;
        }
        //取出该角色对应的所有权限
        $Access=M('access');
        $rightNodes=$Access->where("role_id=$roleId")->field('node_id')->select();
        $this->assign('rightNodes',$rightNodes);
        $this->assign('roleId',$roleId);
        $this->assign('nodes',$res);
        $this->display();
    }
    
    //权限分配处理
    public function rbac_right_access_handler(){
        //删除所有权限
        $Access=M('access');
        $Access->where("role_id=$_POST[roleId]")->delete();
        
        //再分配权限，取出控制器节点ID
        //加入Admin模块根节点
        //$Access->add(array('role_id'=>$_POST[roleId],'node_id'=>1,'level'=>1,'pid'=>0));
        foreach($_POST as $k=>$node){
            //如果为数字,为控制器id
            if(is_numeric($k)){
                //添加控制器权限到access表
                $Access->add(array('role_id'=>$_POST[roleId],'node_id'=>$k,'level'=>2,'pid'=>1));
                //取出操作的节点ID
                foreach($node as $nodeId){
                    //添加操作权限
                    $Access->add(array('role_id'=>$_POST[roleId],'node_id'=>$nodeId,'level'=>3,'pid'=>$k));
                }
            }
        }
        $this->ajaxReturn("","success",1);
    }
    
    //********************************************************END 角色管理************************************
    
    
    //菜单显示分配页
    public function rbac_user_menu_layer(){
        $u_id=$_GET['id'];
        $Menu=M("menu");
        $UserMenu=M("user_menu");
        $userMenu=$UserMenu->where("u_id='$u_id'")->select();
        $menu=$Menu->order("m_id")->select();
        $this->assign("menu",$menu);
        $this->assign("id",$u_id);
        $this->assign("userMenu",$userMenu);
        $this->display();
    }
    
    //菜单分配人员操作
    public function rbac_user_menu_handler(){
        $UserMenu=M("user_menu");
        $data['u_id']=$u_id=$_POST['u_id'];
        $UserMenu->where("u_id='$u_id'")->delete();
        foreach ($_POST['menu_id'] as $v){
            $data['m_id']=$v;
            $UserMenu->add($data);
        }
        $this->ajaxReturn("","操作成功",1);
    }
}
?>