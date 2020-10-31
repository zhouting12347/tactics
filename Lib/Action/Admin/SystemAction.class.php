<?php
class SystemAction extends CommonAction{
    /**
    +--------------------------------
    * 节目树配置
    +--------------------------------
    * @date: 2018年4月16日 下午4:24:31
    * @author: Str
    * @param: variable
    * @return:
    */
    public function programConfig(){
        import("@.Action.Admin.UploadAction");
        $Upload=new UploadAction();
        $tree=$Upload->getTree();
        $tree_json=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($tree));
        $this->assign("tree",$tree_json);
        $this->assign("level8","active open");
        $this->assign("level8_1","active");
        $this->display("video_tree");
    }
    
    /**
     +--------------------------------
     * 图片树配置
     +--------------------------------
     * @date: 2018年4月16日 下午4:24:31
     * @author: Str
     * @param: variable
     * @return:
     */
    public function imageConfig(){
        import("@.Action.Admin.GraphicAction");
        $Graphic=new GraphicAction();
        $tree=$Graphic->getImageTree();
        $tree_json=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($tree));
        $this->assign("tree",$tree_json);
        $this->assign("level8","active open");
        $this->assign("level8_5","active");
        $this->display("image_tree");
    }
    
    /**
     +--------------------------------
     * 文本树配置
     +--------------------------------
     * @date: 2018年4月16日 下午4:24:31
     * @author: Str
     * @param: variable
     * @return:
     */
    public function textConfig(){
        import("@.Action.Admin.GraphicAction");
        $Graphic=new GraphicAction();
        $tree=$Graphic->getTextTree();
        $tree_json=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($tree));
        $this->assign("tree",$tree_json);
        $this->assign("level8","active open");
        $this->assign("level8_6","active");
        $this->display("text_tree");
    }
    
    /**
    +--------------------------------
    * 终端树形配置
    +--------------------------------
    * @date: 2018年4月16日 下午4:25:25
    * @author: Str
    * @param: variable
    * @return:
    */
    public function deviceConfig(){
        $tree=getDeviceTree();
        $tree_json=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($tree));
        $this->assign("tree",$tree_json);
        $this->assign("level8","active open");
        $this->assign("level8_2","active");
        $this->display("device_tree");
    }
    
    /**
    +--------------------------------
    * 终端回传设置
    +--------------------------------
    * @date: 2018年4月17日 上午10:35:12
    * @author: Str
    * @param: variable
    * @return:
    */
    public function deviceBackConfig(){
        $tree=getDeviceTree();
        $tree_json=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", json_encode($tree));
        $this->assign("tree",$tree_json);
        $this->assign("level8","active open");
        $this->assign("level8_4","active");
        $this->display();
    }
    
    /**
    +--------------------------------
    * 终端回传设置iframe页
    +--------------------------------
    * @date: 2018年4月17日 上午10:45:16
    * @author: Str
    * @param: variable
    * @return:
    */
    public function device_iframe(){
        $nodeID=$_GET['id'];
        if($nodeID){
            $Device=M("device");
            $device=$Device->where("dt_id=$nodeID and c_id=$_SESSION[c_id]")->select();
            $this->assign("device",$device);
            $this->assign("nodeID",$nodeID);
            $this->assign("nodeName",$_GET['nodeName']);
        }
        $this->display();
    }
    
    /**
    +--------------------------------
    * 终端回传配置页
    +--------------------------------
    * @date: 2018年4月17日 上午11:27:35
    * @author: Str
    * @param: variable
    * @return:
    */
    public function deviceBackLayer(){
         $d_id=$_GET['id'];
         $Device=M("device");
         $device=$Device->where("d_id=$d_id and c_id=$_SESSION[c_id]")->find();
         $this->assign("device",$device);
         $this->display();
    }
    
    /**
    +--------------------------------
    * 终端回传操作
    +--------------------------------
    * @date: 2018年4月17日 上午11:23:15
    * @author: Str
    * @param: variable
    * @return:
    */
    public function deviceBackHandler(){
        $Device=M("device");
        //dump($_POST);die();
        if (!$Device->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            exit($this->error($Device->getError()));
        }else{
            // 验证通过 可以进行其他数据操作
            $res=$Device->save();
        }
        if($res){
            $this->success("success");
        }else{
            $this->error("failed");
        }
    }
    
    /**
    +--------------------------------
    * 节目下发设置
    +--------------------------------
    * @date: 2018年4月17日 下午2:26:18
    * @author: Str
    * @param: variable
    * @return:
    */
    public function programDistributionConfig(){
        $DistributionConfig=M("distribution_config");
        $config=$DistributionConfig->where("id=1")->find();
        $this->assign("config",$config);
        $this->assign("level8","active open");
        $this->assign("level8_3","active");
        $this->display();
    }
    
    /**
    +--------------------------------
    * 函数用途描述
    +--------------------------------
    * @date: 2018年4月17日 下午2:37:06
    * @author: Str
    * @param: variable
    * @return:
    */
    public function programDistributionConfigHandler(){
        //dump($_POST);die();
        $DistributionConfig=M("distribution_config");
        $DistributionConfig->where("id=1")->setField(array("startTime","endTime"),array($_POST["startTime"],$_POST["endTime"]));
        $this->redirect("programDistributionConfig");
    }
    
    /**
    +--------------------------------
    * 配置页面
    +--------------------------------
    * @date: 2019年6月5日 上午10:48:54
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_configuration_page(){
        $Config=M('config');
        $this->assign("data",$Config->select());
        $this->display();
    }
    
    /**
    +--------------------------------
    * 配置页面编辑操作
    +--------------------------------
    * @date: 2019年6月5日 上午11:14:47
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_configuration_edit_handler(){
        $Config=M('config');
        $res=$Config->where("C_Set='DownloadDay'")->setField("C_Value",$_POST['DownloadDay']);
        $res=$Config->where("C_Set='PlayUnit'")->setField("C_Value",$_POST['PlayUnit']);
        $res=$Config->where("C_Set='PlayStartTime'")->setField("C_Value",$_POST['PlayStartTime']);
        $res=$Config->where("C_Set='PlayEndTime'")->setField("C_Value",$_POST['PlayEndTime']);
        $this->ajaxReturn('','success',1);
    }
    
    /**
    +--------------------------------
    * 添加公司操作
    +--------------------------------
    * @date: 2019年6月12日 下午1:34:11
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_add_company_handler(){
        $Company=M('company');
        $C_ID=$Company->add($_POST);
        if($C_ID){
            //创建公司的admin账户
            $User=M('user');
            $data['CompanyID']=$C_ID;
            $data['U_Username']=$_POST['C_CompanyName'];
            $data['U_Loginname']=$_POST['C_CompanyName']."_admin";
            $data['U_Password']=md5("admin");
            $data['U_IfUse']=1;
            $data['U_IfAdmin']=1;
            $User->add($data);
            $this->ajaxReturn('','success',1);
        }else{
            $this->ajaxReturn('','failed',0);
        }
    }
    
    /**
    +--------------------------------
    * 公司编辑页
    +--------------------------------
    * @date: 2019年6月12日 下午1:41:29
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_edit_company_layer(){
        $C_ID=$_GET['id'];
        $condition="C_ID='$C_ID'";
        $this->assign("data",$this->getData("company", $condition));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 公司编辑操作
     +--------------------------------
     * @date: 2019年6月12日 下午1:41:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function system_edit_company_handler(){
        $condition="C_ID='$_POST[C_ID]'";
        $result=$this->editData("company",$_POST,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 公司删除操作
     +--------------------------------
     * @date: 2019年6月12日 下午1:41:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function system_del_company_handler(){
        $id=$_GET['id'];
        $condition="C_ID=$id";
        $result=$this->delData("company",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 修改密码操作
    +--------------------------------
    * @date: 2019年6月12日 下午2:10:04
    * @author: zt
    * @param: variable
    * @return:
    */
    public function changePasswordHandler(){
        $U_ID=$_SESSION['user_id'];//用户ID
        if($_POST['new_password']!=$_POST['confirm_password']){
            $this->ajaxReturn("","New password inconsistency",0);
        }
        if($_POST['new_password']==null||$_POST['confirm_password']==null){
            $this->ajaxReturn("","The password must not be empty!",0);
        }
        $User=M('user');
        $user=$User->where("U_ID='$U_ID'")->find();
        if(md5($_POST['old_password'])!=$user['U_Password']){
            $this->ajaxReturn("","old password error",0);
        }else if(md5($_POST['old_password'])==$user['U_Password']){
            $res=$User->where("U_ID='$U_ID'")->setField("U_Password",md5($_POST['new_password']));
            if($res){
                session_unset();
                session_destroy();
                $this->ajaxReturn("","success",1);
            }else{
                $this->ajaxReturn("","failed",0);
            }
        }
    }
    
    /**
    +--------------------------------
    * playermac table 页
    +--------------------------------
    * @date: 2019年6月19日 上午11:42:12
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_player_mac_table(){
        $this->assign("company",$this->getData("company","1=1",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 新增player mac页面
    +--------------------------------
    * @date: 2019年6月18日 下午4:24:32
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_add_player_mac_layer(){
        $this->assign("company",$this->getData("company","1=1",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 新增player mac操作 
    +--------------------------------
    * @date: 2019年6月18日 下午3:58:43
    * @author: zt
    * @param: variable
    * @return:
    */
    public function system_add_player_mac_handler(){
        $result=$this->addData("playermac",$_POST);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 编辑player mac页面
     +--------------------------------
     * @date: 2019年6月18日 下午4:24:32
     * @author: zt
     * @param: variable
     * @return:
     */
    public function system_edit_player_mac_layer(){
        $this->assign("company",$this->getData("company","1=1",1));
        $this->assign("data",$this->getData("playermac","PM_ID='$_GET[id]'"));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 编辑player mac操作
     +--------------------------------
     * @date: 2019年6月18日 下午3:58:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function system_edit_player_mac_handler(){
        $result=$this->editData("playermac",$_POST,"PM_ID='$_POST[PM_ID]'");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 删除player mac操作
     +--------------------------------
     * @date: 2019年6月18日 下午3:58:43
     * @author: zt
     * @param: variable
     * @return:
     */
    public function system_del_player_mac_handler(){
        if($_GET['ifUse']==1){  //正在被使用的盒子不能删除
            $this->ajaxReturn("","盒子正在被使用不能删除",0);
        }
        $result=$this->delData("playermac","PM_ID='$_GET[id]'");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
}