<?php
class PublicAction extends Action
{
    //跳转
    public function jump(){
        $id=$_GET[id];
        $this->redirect('wx/Index/index?id='.$id);
    }
    
    //登录页面
    public function login()
    {
        //import('ORG.Net.RBAC');
        //$map['username']="user_test";
        //$result=RBAC::authenticate($map);
        //$_SESSION[C('USER_AUTH_KEY')]=3;
        //RBAC::saveAccessList();
        header("content-type:text/html");
        header("location:http://172.20.31.24/login.jsp");
        exit();
        //$this->display('login');
    }
    
    //登录页
    public function xunnuo(){
        $this->display('login');
    }
    
    //注销
    public function logout()
    {
        session_unset();
        session_destroy();
        $this->redirect('Public/login');
    }
    
    //检测用户登录
    public function checkLogin(){
        ini_set('session.gc_maxlifetime', "14400"); // 秒
        ini_set("session.cookie_lifetime","14400");
        $username=$_POST['u_username'];
        $password=$_POST['u_password'];
        if(!($username&&$password)){
            $this->ajaxReturn("","请填写用户名密码",0);
        }
        $User=M("user");
        $res=$User->where("u_username='$username'")->find();
        if(!$res){
            $this->ajaxReturn("","用户名不存在",0);
        }else{
            $md5_password=md5($password);
            $res=$User->where("u_username='$username' and u_password='$md5_password'")->find();
            if(!$res){
                $this->ajaxReturn("","密码错误",0);
            }elseif($res[u_ifuse]==0){
                $this->ajaxReturn("","账号被禁用",0);
            }else{
                $UserMenu=M("user_menu");
                $userMenu=$UserMenu
                ->where("u_id='$res[u_id]'")
                ->join("left join t_menu on t_menu.m_id=t_user_menu.m_id")
                ->order("t_menu.m_id")
                ->select();
                $_SESSION['username']=$username;
                $_SESSION['realname']=$res['u_realname'];
                $_SESSION['userID']=$res['u_id'];
                $_SESSION['duty']=$res['u_duty'];
                $_SESSION['duty2']=$res['u_duty2'];
                $_SESSION['logintime']=time()+3600*4;
                $_SESSION['baoliaoaudit']=$res['u_ifbaoliaoaudit'];
                $_SESSION['station']=$res['u_station'];
                foreach ($userMenu as $v){
                    $_SESSION['menu'][$v[m_function]]="show";
                }
                $_SESSION['iframeFunction']=$userMenu[0]['m_function'];
                $_SESSION['iframeController']=$userMenu[0]['m_controller'];
                //dump($_SESSION);
             /*    //查询用户对应的角色
                $RoleUser=M('role_user');
                $roles=$RoleUser->where("user_id='$res[U_ID]'")->select();
                $Node=M('node');
                $nodes=$Node->where("level=3")->field('name')->select();
                
                $_SESSION['nodes']=array(); //所有需要的权限
                foreach ($nodes as $n){
                    $_SESSION['nodes'][]=$n['name'];
                }
                $_SESSION['accessList']=array(); //用户拥有的权限列表
                if($roles){
                    $Access=M('access');
                    foreach ($roles as $v){  //查询角色对应的权限
                        $access=$Access->where("t_access.role_id=$v[role_id] and t_access.level=3")->join("left join t_node on t_node.id=t_access.node_id")->select();
                       //dump($access);
                        if($access){
                            foreach ($access as $a){
                                $_SESSION['accessList'][]=$a['name'];
                            }
                        }
                    }
                } */
                
               /*  //加载RBAC
                import('ORG.Net.RBAC');
                $map['username']=$username;
                //加载RBAC类
                //通过authenticate读取用户信息
                RBAC::authenticate($map);
                //是否为管理员账户
                if($res['username']==C('RBAC_ADMIN')){
                    $_SESSION[C('ADMIN_AUTH_KEY')]=true;
                }
                $_SESSION[C('USER_AUTH_KEY')]=$res['id'];
                RBAC::saveAccessList(); */
                $this->ajaxReturn("","success",1);
            }
        }
    }
    
    /**
    +--------------------------------
    * 地铁用户对接
    +--------------------------------
    * @date: 2019年8月20日 下午1:11:49
    * @author: zt
    * @param: variable
    * @return:
    */
    public function verify(){
        $name=$_GET['name'];
        $token=$_GET['token'];
        $key="W1aTN&JSm9"."$"."YaV5p";
        if($token==md5($name.$key)){
            ini_set('session.gc_maxlifetime', "14400"); // 秒
            ini_set("session.cookie_lifetime","14400");
            $User=M('user');
            $user=$User->where("u_metroID='$name'")->find();  //用户信息
            $UserMenu=M("user_menu");
            $userMenu=$UserMenu
            ->where("u_id='$user[u_id]'")
            ->join("left join t_menu on t_menu.m_id=t_user_menu.m_id")
            ->order("t_menu.m_id")
            ->select();   //用户菜单权限
                 
            $_SESSION['username']=$user['u_username'];
            $_SESSION['realname']=$user['u_realname'];
            $_SESSION['userID']=$user['u_id'];
            $_SESSION['duty']=$user['u_duty'];
            $_SESSION['duty2']=$user['u_duty2'];
            $_SESSION['logintime']=time()+3600*4;
            foreach ($userMenu as $v){
                $_SESSION['menu'][$v[m_function]]="show";
            }
            $_SESSION['iframeFunction']=$userMenu[0]['m_function'];
            $_SESSION['iframeController']=$userMenu[0]['m_controller'];
           $this->redirect('Index/index');
        }else{
            echo "用户验证失败";
        }
    }


    //逐行读取txt
    public function readLog(){
        $file_arr = file("C:/Users/ZT\Desktop/new/test.log"); ###得到数组
        dump($file_arr);
    }

}
?>