<?php
class OrderAction extends CommonAction{
    /*******************************************************订单新建区*********************************************/
	/**
	+--------------------------------
	* 订单表格页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function order_editing_table(){
        $this->display();
    }

    /**
    +--------------------------------
    * 新建订单页
    +--------------------------------
    * @date: 2019年4月25日 下午4:27:47
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_add_layer(){
        $this->assign("user",$this->getData("user","CompanyID=$_SESSION[company_id] and U_IfUse=1",1));
        $this->assign("customer",$this->getData("customers","CompanyID=$_SESSION[company_id] and C_Enabled=1",1));
        $this->assign("orderID",time().$_SESSION['company_id'].mt_rand(10000,99999));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 编辑订单页
     +--------------------------------
     * @date: 2019年4月25日 下午4:27:47
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_edit_layer(){
        $this->assign("user",$this->getData("user","CompanyID=$_SESSION[company_id] and U_IfUse=1",1));
        $this->assign("customer",$this->getData("customers","CompanyID=$_SESSION[company_id] and C_Enabled=1",1));
        $this->assign("orderID",$_GET['O_ID']);
        $this->assign("data",$this->getData("order","O_ID=$_GET[O_ID]"));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 弹出选择机顶盒页
    +--------------------------------
    * @date: 2019年4月26日 上午10:20:21
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_add_order_player_rel_layer(){
        $this->assign("orderID",$_GET['id']);
        $this->assign("playertype",$this->getData("playertype","CompanyID=$_SESSION[company_id]",1));
        $this->assign("area",$this->getData("area","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加订单机顶盒关系操作
     +--------------------------------
     * @date: 2019年4月26日 上午10:20:21
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_add_order_player_rel_handler(){
       $orderID=$data['O_ID']=$_GET['id'];
       $data['CompanyID']=$_SESSION['company_id'];
       $Orderplayerrel=M('orderplayerrel');
       foreach($_POST['data'] as $v){
           if(!$Orderplayerrel->where("O_ID=$orderID and P_ID=$v[P_ID] and CompanyID=$_SESSION[company_id]")->find()){
               $data['P_ID']=$v['P_ID'];
               $Orderplayerrel->add($data);
           }
       }
       $this->ajaxReturn("","success",1);
    }
    
    /**
     +--------------------------------
     * 删除订单机顶盒关系操作
     +--------------------------------
     * @date: 2019年4月26日 上午10:20:21
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_del_order_player_rel_handler(){
        $condition="O_ID=$_GET[O_ID] and P_ID=$_GET[P_ID] and CompanyID=$_SESSION[company_id]";
        $result=$this->delData("orderplayerrel",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 弹出选择视频页
     +--------------------------------
     * @date: 2019年4月26日 上午10:20:21
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_add_order_clip_rel_layer(){
        $this->assign("orderID",$_GET['id']);
        $this->assign("cliptype",$this->getData("cliptype","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加订单视频关系操作
     +--------------------------------
     * @date: 2019年4月26日 上午10:20:21
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_add_order_clip_rel_handler(){
        $orderID=$data['O_ID']=$_GET['id'];
        $data['CompanyID']=$_SESSION['company_id'];
        $Ordercliprel=M('ordercliprel');
        foreach($_POST['data'] as $v){
            if(!$Ordercliprel->where("O_ID=$orderID and C_ID=$v[C_ID] and CompanyID=$_SESSION[company_id]")->find()){
                $data['C_ID']=$v['C_ID'];
                $data['Length']=timeToSeconds(substr($v['C_Duration'],0,8)); //长度秒
                $Ordercliprel->add($data);
            }
        }
        $this->ajaxReturn("","success",1);
    }
    
    /**
     +--------------------------------
     * 删除订单视频关系操作
     +--------------------------------
     * @date: 2019年4月26日 上午10:20:21
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_del_order_clip_rel_handler(){
        $condition="O_ID=$_GET[O_ID] and C_ID=$_GET[C_ID] and CompanyID=$_SESSION[company_id]";
        $result=$this->delData("ordercliprel",$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
    +--------------------------------
    * 订单提交操作
    +--------------------------------
    * @date: 2019年5月6日 下午1:23:26
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_submit_editing_handler(){
        $result=$this->order_data_handler($_POST,"add");
        $this->ajaxReturn("",$result['info'],$result['success']);
    }
    
    /**
    +--------------------------------
    * 删除order
    +--------------------------------
    * @date: 2019年5月24日 下午4:41:09
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_del_order_handler(){
        $O_ID=$_GET['O_ID'];
        $condition="O_ID=$O_ID";
        $this->delData("order",$condition);
        $this->delData("orderplayerrel",$condition);
        $this->delData("playercliprel",$condition);
        $this->delData("ordercliprel",$condition);
        $this->ajaxReturn("","success",1);
    }
    
    /**
    +--------------------------------
    * order提交审核操作
    +--------------------------------
    * @date: 2019年5月24日 下午4:53:19
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_editing_submit_order_handler(){
        $O_ID=$_GET['O_ID'];
        $order=$this->getData("order","O_ID=$O_ID");
        //计算订单视频是否超过时长
        $result=$this->order_data_handler($order,"check");
        if($result['success']===1){
            //修改订单状态位 1:录入区2.审核区3:过审区4:审核不通过',
            $Order=M("order");
            $Order->where("O_ID=$O_ID")->setField("O_Status",2);
            $this->ajaxReturn("",$result['info'],1);
        }elseif($result['success']===0){
            $this->ajaxReturn("",$result['info'],0);
        }
    }
    
    /**
    +--------------------------------
    * clipsinfo layer 订单视频列表页
    +--------------------------------
    * @date: 2019年5月14日 下午2:34:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_clips_info_layer(){
        $this->assign("O_ID","$_GET[O_ID]");
        $this->display();
    }
    
    /**
     +--------------------------------
     * playerinfo layer 订单player列表页
     +--------------------------------
     * @date: 2019年5月14日 下午2:34:28
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_player_info_layer(){
        $this->assign("O_ID","$_GET[O_ID]");
        $this->display();
    }
    
    /**
    +--------------------------------
    * 订单编辑操作
    +--------------------------------
    * @date: 2019年5月16日 下午2:15:28
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_edit_editing_handler(){
        $result=$this->order_data_handler($_POST,"edit");
        $this->ajaxReturn("",$result['info'],$result['success']);
    }
    
    /**
    +--------------------------------
    * 订单数据处理
    +--------------------------------
    * @date: 2019年5月16日 下午3:31:36
    * @author: zt
    * @param: variable
    * @return:
    */
    private function order_data_handler($orderdata,$type){
        $O_ID=$data['O_ID']=$data2['O_ID']=$orderdata['O_ID'];
        $data['CompanyID']=$_SESSION['company_id'];
        $data['O_Contract']=$orderdata['O_Contract'];
        $data['O_Payment']=$orderdata['O_Payment'];    //O_Payment int default 0 comment '0:未支付1:已支付2:部分支付',
        $startDate=$data['O_StartDate']=$data2['O_StartDate']=$orderdata['O_StartDate'];
        $endDate=$data['O_EndDate']=$data2['O_EndDate']=$orderdata['O_EndDate'];
        switch($orderdata['PT']){  //'0:滚动1:整点2:半点3:整点+半点4:定点滚动5:定点一次',
            case "Rolling":
                $data['O_PlayType']=$data2['O_PlayType']=0;
                break;
            case "Integral point":
                $data['O_PlayType']=$data2['O_PlayType']=1;
                break;
            case "Half point":
                $data['O_PlayType']=$data2['O_PlayType']=2;
                break;
            case "Integral Half":
                $data['O_PlayType']=$data2['O_PlayType']=3;
                break;
            case "Fixed point":
                $data['O_PlayType']=$data2['O_PlayType']=4;
                $data["O_FixTime"]=$data2["O_FixedTime"]=$orderdata['O_FixTime'];
                break;
            case "Stipulated time":
                $orderdata['O_OnceDateTime']=substr( $orderdata['O_OnceDateTime'],0,17)."00";
                $data['O_PlayType']=$data2['O_PlayType']=5;
                $data['O_OnceDateTime']=$data2['O_OnceDateTime']
                =$data['O_StartDate']=$data2['O_StartDate']
                =$data['O_EndDate']=$data2['O_EndDate']
                =$orderdata['O_OnceDateTime'];
                $data['O_Days']=1;
                break;
        }
        
        //播放天数
        if($data['O_PlayType']==5){
            $data['O_Days']=1;
        }else{
            $data['O_Days']=((strtotime($data['O_EndDate'])-strtotime($data['O_StartDate']))/86400)+1;
        }
        $data['O_SalesID']=$orderdata['O_SalesID'];
        $data['O_CustomerID']=$orderdata['O_CustomerID'];
        $data['O_Value']=$orderdata['O_Value'];
        $data['O_Creater']=$_SESSION['username'];
        $data['O_CreateTime']=date("Y-m-d H:i:s",time());
        
        //读取orderplayerrel表
        $players=$this->getData("orderplayerrel","O_ID='$O_ID' and CompanyID=$_SESSION[company_id]",1);
        $data['O_Players']=count($players);
        
        //读取ordercliprel表
        $clips=$this->getData("ordercliprel","O_ID='$O_ID' and CompanyID=$_SESSION[company_id]",1);
        $data['O_Clips']=count($clips);
        $data['O_TotalLength']=0;
        foreach($clips as $v){
            $data['O_TotalLength']+=$v['Length'];
        }

        $result=array("success"=>1,"info"=>"success");  //返回值
        
        $unitMaxLength=C('UNIT_MAX_LENGTH'); //最大长度单元(秒)
        if($data['O_TotalLength']>$unitMaxLength){  //判断新添加的所有节目时长是否超过单元长度
            $result['success']=0;
            $result['info']="clips total length over $unitMaxLength seconds";
            return $result;
        }
        $addNewTotalLength=$data['O_TotalLength']; //当前加入的时长
                
        //判断加入的广告和之前加入的广告最大长度超过限定值
        $Order=M("order");
        $Player=M("player");
        foreach($players as $p){
            $P_ID=$p['P_ID'];
            $player=$Player->where("P_ID=$P_ID")->find();
            
           /*  if($data['O_PlayType']==5){ //一次播放
                break;
            } */
            
            //整点 查询是否已经存在整点或者整点半点
            if($data['O_PlayType']==1){
                $samePlayerOrders=$Order
                ->Distinct(true)
                ->where("((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=1 OR O_PlayType=3)")
                ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                ->order("O_StartDate")
                ->select();
                foreach ($samePlayerOrders as $k=>$v){
                    if(!$v['P_ID']){
                        unset($samePlayerOrders[$k]);
                    }
                }  
               if($samePlayerOrders){
                   $result['success']=0;
                   $result['info']="已经存在整点节目 "."Player:".$player['P_Location'];
                   break;
               }
            }elseif($data['O_PlayType']==2){ //半点
                $samePlayerOrders=$Order
                ->Distinct(true)
                ->where("((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=2 OR O_PlayType=3)")
                ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                ->order("O_StartDate")
                ->select();
                foreach ($samePlayerOrders as $k=>$v){
                    if(!$v['P_ID']){
                        unset($samePlayerOrders[$k]);
                    }
                }
                if($samePlayerOrders){
                    $result['success']=0;
                    $result['info']="已经存在半点节目 "."Player:".$player['P_Location'];
                    break;
                }
            }elseif($data['O_PlayType']==3){ //半点+整点
                $samePlayerOrders=$Order
                ->Distinct(true)
                ->where("((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=1 OR O_PlayType=2 OR O_PlayType=3)")
                ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                ->order("O_StartDate")
                ->select();
                foreach ($samePlayerOrders as $k=>$v){
                    if(!$v['P_ID']){
                        unset($samePlayerOrders[$k]);
                    }
                }
                if($samePlayerOrders){
                    $result['success']=0;
                    $result['info']="已经存在半点整点节目 "."Player:".$player['P_Location'];
                    break;
                }
            }elseif($data['O_PlayType']==4){ //定点滚动
                $seconds=timeToSeconds($data["O_FixTime"]);
                if($seconds%3600==0){  //定点为整点节目
                    $samePlayerOrders=$Order
                    ->Distinct(true)
                    ->where("((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=1 OR O_PlayType=3)")
                    ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                    ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                    ->order("O_StartDate")
                    ->select();
                    foreach ($samePlayerOrders as $k=>$v){
                        if(!$v['P_ID']){
                            unset($samePlayerOrders[$k]);
                        }
                    }
                    if($samePlayerOrders){
                        $result['success']=0;
                        $result['info']="已经存在整点节目 "."Player:".$player['P_Location'];
                        break;
                    }
                }
                
                if($seconds%1800==0){ //定点为半点节目
                    $samePlayerOrders=$Order
                    ->Distinct(true)
                    ->where("((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=2 OR O_PlayType=3)")
                    ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                    ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                    ->order("O_StartDate")
                    ->select();
                    foreach ($samePlayerOrders as $k=>$v){
                        if(!$v['P_ID']){
                            unset($samePlayerOrders[$k]);
                        }
                    }
                    if($samePlayerOrders){
                        $result['success']=0;
                        $result['info']="已经存在半点节目 "."Player:".$player['P_Location'];
                        break;
                    }
                } 
            }elseif($data['O_PlayType']==5){ //定点一次
                $once_data_array=explode(" ",$data['O_OnceDateTime']);
                $onceStartDate=$onceEndDate=$once_data_array[0];
                $onceSeconds=timeToSeconds($once_data_array[1]);
                if($onceSeconds%3600==0){  //定点为整点节目
                    $samePlayerOrders=$Order
                    ->Distinct(true)
                    ->where("((O_StartDate<='$onceStartDate' and O_EndDate>='$onceStartDate') or (O_StartDate>='$onceStartDate' and O_StartDate<='$onceEndDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=1 OR O_PlayType=3)")
                    ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                    ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                    ->order("O_StartDate")
                    ->select();
                    foreach ($samePlayerOrders as $k=>$v){
                        if(!$v['P_ID']){
                            unset($samePlayerOrders[$k]);
                        }
                    }
                    if($samePlayerOrders){
                        $result['success']=0;
                        $result['info']="已经存在整点节目 "."Player:".$player['P_Location'];
                        break;
                    }
                }
                
                if($onceSeconds%1800==0){ //定点为半点节目
                    $samePlayerOrders=$Order
                    ->Distinct(true)
                    ->where("((O_StartDate<='$onceStartDate' and O_EndDate>='$onceStartDate') or (O_StartDate>='$onceStartDate' and O_StartDate<='$onceEndDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2) AND (O_PlayType=2 OR O_PlayType=3)")
                    ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                    ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                    ->order("O_StartDate")
                    ->select();
                    foreach ($samePlayerOrders as $k=>$v){
                        if(!$v['P_ID']){
                            unset($samePlayerOrders[$k]);
                        }
                    }
                    if($samePlayerOrders){
                        $result['success']=0;
                        $result['info']="已经存在半点节目 "."Player:".$player['P_Location'];
                        break;
                    }
                } 
                break;
            }    
           
            //从每个盒子逐个比较，找出所有与当前盒子时间重合的订单
           $samePlayerOrders=$Order
            ->Distinct(true)
            ->where("((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND t_order.O_ID<>$O_ID AND (O_Status=3 OR O_Status=2)")
            ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
            ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
            ->order("O_StartDate")
            ->select();
           // $sql="SELECT *,(SELECT P_ID FROM t_orderplayerrel WHERE t_orderplayerrel.O_ID=t_order.O_ID AND P_ID=$P_ID) as newP FROM t_order WHERE ((O_StartDate<='$startDate' and O_EndDate>='$startDate') or (O_StartDate>='$startDate' and O_StartDate<='$endDate')) AND O_PlayType<>5 AND O_ID<>$O_ID";
            //$samePlayerOrders=$Order->query($sql);
           // echo $sql;
            //dump($samePlayerOrders);
            
            foreach ($samePlayerOrders as $k=>$v){
                if(!$v['P_ID']){
                    unset($samePlayerOrders[$k]);
                }
            }  
            
            if(!$samePlayerOrders){
                break;
            }
            
            //查询循环中盒子所有相交的订单
            foreach($samePlayerOrders as $s){
                $startDate>=$s['O_StartDate']?$tempStartDate=$startDate:$tempStartDate=$s['O_StartDate'];
                $endDate>=$s['O_EndDate']?$tempEndDate=$s['O_EndDate']:$tempEndDate=$endDate;
                //查询新增订单和当前循环订单相交的订单
                $tempSamePlayerClips=$Order
                ->Distinct(true)
                ->where("((O_StartDate<='$tempStartDate' and O_EndDate>='$tempStartDate') or (O_StartDate>='$tempStartDate' and O_StartDate<='$tempEndDate')) AND O_PlayType<>5 AND t_order.O_ID<>$s[O_ID] AND t_order.O_ID<>$O_ID  AND (O_Status=3 OR O_Status=2)")
                ->join("left join t_orderplayerrel on t_orderplayerrel.P_ID=$P_ID and t_orderplayerrel.O_ID=t_order.O_ID")
                ->field("O_StartDate,O_EndDate,O_TotalLength,P_ID,t_order.O_ID")
                ->order("O_StartDate")
                ->select();
                
                foreach ($tempSamePlayerClips as $k=>$v){
                    if(!$v['P_ID']){
                        unset($tempSamePlayerClips[$k]);
                    }
                }  
                
                //如果存在同时相交订单，计算出相交订单的时长总和
                if($tempSamePlayerClips){
                    $tempSamePlayerClipsTotalLength=0;
                    $tempMaxStartDate=null;
                    $tempMinEndDate="2099-12-31";
                    foreach($tempSamePlayerClips as $tempClip){   //找到最大的开始时间，和最小的结束时间
                        $tempSamePlayerClipsTotalLength+=$tempClip['O_TotalLength'];
                        $tempClip['O_StartDate']>$tempMaxStartDate?$tempMaxStartDate=$tempClip['O_StartDate']:null;
                        $tempClip['O_EndDate']<$tempMinEndDate?$tempMinEndDate=$tempClip['O_EndDate']:null;
                    }
                    if(($addNewTotalLength+$tempSamePlayerClipsTotalLength)>C('UNIT_MAX_LENGTH')){
                        $result['success']=0;
                        $result['info']=$tempMaxStartDate." - ".$tempMinEndDate."Player:".$player['P_Location'];
                        break 2;
                    }
                }else{  //不存在同时相交的订单，计算新增订单和当前循环订单的总时长
                    if(($addNewTotalLength+$s['O_TotalLength'])>C('UNIT_MAX_LENGTH')){  //如果超过总时长,返回当前循环订单的信息
                        $result['success']=0;
                        $result['info']=$s['O_StartDate']." - ".$s['O_EndDate']."Player:".$player['P_Location'];
                        break 2;
                    }
                }
            }
        }
        
        if($result['success']===0){
            return $result;
        }
        
         //新增订单
        if($type=="add"){ 
            $this->addData("order",$data);  //保存到order表
        }elseif($type=="check"){
            return $result;
        //编辑订单
        }elseif($type=="edit"){
            $this->editData("order",$data,"O_ID='$O_ID'");
            //重置playercliprel表
            $this->delData("playercliprel", "O_ID='$O_ID'");
        }
        //保存到playercliprel表
        $data2['O_IfVerify']=0;
        foreach ($players as $v){
            $data2['P_ID']=$v['P_ID'];
            $data2['CompanyID']=$_SESSION['company_id'];
            $player=$Player->where("P_ID=$v[P_ID]")->find();
            $data2['P_SN']=$player['P_SN'];
            foreach($clips as $j){
                $data2['C_ID']=$j['C_ID'];
                $data2['O_Length']=$j['Length'];
                $this->addData("playercliprel",$data2);
            }
        }
        
        return $result;
    }
    /*******************************************************END订单新建区*********************************************/
    
    
    /*******************************************************订单审核区*********************************************/
    /**
	+--------------------------------
	* 订单审核表格页
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function order_auditing_table(){
        $this->display();
    }
    
    /**
     +--------------------------------
     * 订单通过审核操作
     +--------------------------------
     * @date: 2019年4月23日 下午3:37:03
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_approve_handler(){
        $data['O_Auditor']=$_SESSION['username'];
        $data['O_AuditTime']=date("Y-m-d H:i:s",time());
        $data['O_Status']=3;
        $condition="O_ID=$_GET[O_ID]";
        $result=$this->editData("order",$data,$condition);
        unset($data);
        //过审订单的视频加入t_playerdownload表
        $OrderPlayerRel=M('orderplayerrel');
        $PlayerClipRel=M('playercliprel');
        $Player=M('player');
        $PlayerDownload=M('playerdownload');
        $O_ID=$_GET[O_ID];
        $Order=M('order');
        
        //更新playercliprel中ifVerify状态为1
        $PlayerClipRel->where("O_ID=$O_ID")->setField("O_IfVerify",1);
        
        //判断订单的开始时间是否等于今天，等于生成txt文件
        $order=$Order->where("O_ID='$O_ID'")->find();
        $currentDate=date("Y-m-d",time());
        if($order['O_StartDate']==$currentDate){
            fopen(C('DATE_EQUAL_TXT'), "a") or die("Unable to open file!");
        }
        
        $players=$OrderPlayerRel->where("O_ID=$O_ID")->select();
        foreach($players as $p){
            $P_ID=$p['P_ID'];
            $player=$Player->where("P_ID=$P_ID")->find();
            $P_SN=$player['P_SN'];
            $clips=$PlayerClipRel->where("P_ID=$P_ID and O_ID=$O_ID")->select();
            foreach($clips as $c){
                $data['P_ID']=$P_ID;
                $data['P_SN']=$P_SN;
                $data['C_ID']=$c['C_ID'];
                $data['PD_Status']=0;
                $data['PD_Date']=date("Y-m-d",time());
                $PlayerDownload->add($data);
                unset($data);
            }
        }
        
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 订单审核不通过，建议页
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_audit_remark_layer(){
        $this->assign("O_ID",$_GET['O_ID']);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 素材审核不通过操作
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_audit_remark_handler(){
        $data['O_AuditRemark']=$_POST['O_AuditRemark'];
        $data['O_Auditor']=$_SESSION['username'];
        $data['O_AuditTime']=date("Y-m-d H:i:s",time());
        $data['O_Status']=4;
        $condition="O_ID=$_POST[O_ID]";
        $result=$this->editData("order",$data,$condition);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    /*******************************************************END 订单审核区*********************************************/
    
    /*******************************************************订单过审区*********************************************/
    /**
     +--------------------------------
     * 订单审核表格页
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_audited_table(){
        $this->display();
    }
    
    /**
     +--------------------------------
     * 订单删除操作
     +--------------------------------
     * @date: 2019年4月23日 下午3:37:03
     * @author: zt
     * @param: variable
     * @return:
     */
    public function order_del_audited_order_handler(){
        $O_ID=$_GET['O_ID'];
        $condition="O_ID=$O_ID";
        
        
        //判断当前日期是否在删除的订单日期区间内，在区间内生成txt文件
        $Order=M('order');
        $order=$Order->where("O_ID=$O_ID")->find();
        $currentDate=date("Y-m-d",time());
        if($order['O_StartDate']<=$currentDate&&$currentDate<=$order['O_EndDate']){
            fopen(C('DATE_EQUAL_TXT'), "a") or die("Unable to open file!");
        }
        
        $this->delData("order",$condition);
        $this->delData("orderplayerrel",$condition);
        $this->delData("playercliprel",$condition);
        $this->delData("ordercliprel",$condition);
        $this->ajaxReturn("","success",1);
    }
    
    /**
    +--------------------------------
    * 订单预览
    +--------------------------------
    * @date: 2019年6月28日 上午10:23:10
    * @author: zt
    * @param: variable
    * @return:
    */
    public function order_preview_layer(){
        $O_ID=$_GET['O_ID'];
        $Order=M('order');
        $Customer=M('customers');
        $Salesman=M('user');
        $Company=M('company');
        $Area=M('area');
        $PlayerType=M('playertype');
        $PlayerMac=M('playermac');
        
        $order=$Order->where("O_ID='$O_ID'")->find();
        //客户信息，客户公司信息
        $customer=$Customer->where("t_customers.C_ID=$order[O_CustomerID]")->find();
        $customerCompany=$Company->where("C_ID=$customer[CompanyID]")->find();
        //销售信息，销售公司信息
        $salesman=$Salesman->where("U_ID=$order[O_SalesID]")->find();
        $salesmanCompany=$Company->where("C_ID=$salesman[CompanyID]")->find();
        
        //video信息
        $OrderClipRel=M('ordercliprel');
        $video=$OrderClipRel
        ->where("O_ID='$O_ID'")
        ->join("left join t_clips on t_clips.C_ID=t_ordercliprel.C_ID")
        ->select();
        foreach($video as $k=>$v){
            $video[$k]['unit']=$v['C_Length']/15;
        }
        
        //order信息
        switch($order['O_PlayType']){
            case 0:
                $order['mode']="Rotation";
                $order['dateRange']=$order['O_StartDate']." to ".$order['O_EndDate'];
                break;
            case 1:
                $order['mode']="On hour";
                $order['dateRange']=$order['O_StartDate']." to ".$order['O_EndDate'];
                break;
            case 2:
                $order['mode']="On half hour";
                $order['dateRange']=$order['O_StartDate']." to ".$order['O_EndDate'];
                break;
            case 3:
                $order['mode']="On hour+half";
                $order['dateRange']=$order['O_StartDate']." to ".$order['O_EndDate'];
                break;
            case 4:
                $order['mode']="Scheduled";
                $order['time']=$order['O_FixTime'];
                break;
            case 5:
                $order['mode']="Specific point";
                $order['time']=$order['O_OnceDateTime'];
                break;
        }
        
        //player信息
        $OrderPlayerRel=M('orderplayerrel');
        $player=$OrderPlayerRel
        ->where("O_ID='$O_ID'")
        ->join("left join t_player on t_player.P_ID=t_orderplayerrel.P_ID")
        ->select();
        foreach($player as $k=>$p){
           $area=$Area->where("A_ID='$p[A_ID]'")->find();
           $playertype=$PlayerType->where("PT_ID='$p[PT_ID]'")->find();
           $playermac=$PlayerMac->where("PM_Mac='$p[P_SN]'")->find();
           $player[$k]['area']=$area['A_Name'];
           $player[$k]['premises']=$playertype['PT_Name'];
           $player[$k]['playerID']=$playermac['PM_PID'];
        }
        
        
        $this->assign("video",$video);
        $this->assign("customer",$customer);
        $this->assign("customerCompany",$customerCompany);
        $this->assign("salesman",$salesman);
        $this->assign("salesmanCompany",$salesmanCompany);
        $this->assign("order",$order);
        $this->assign("player",$player);
        $this->display();
    }
    
    /*******************************************************END 订单过审区*********************************************/
}