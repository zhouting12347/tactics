<?php
class ArrangeAction extends CommonAction{
    
    /***************************************************************值班岗位**********************************************************/
    /**
     +--------------------------------
     * 值班岗位表格
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_position_table(){
        $user=$this->getData("user","1=1",1);
        $this->assign("user",$user);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加值班岗位页
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_add_position_layer(){
        $user=$this->getData("user","1=1",1);
        $this->assign("user",$user);
        $this->display();
    }
    
    /**
     +--------------------------------
     * 添加值班岗位操作
     +--------------------------------
     * @date: 2019年5月5日 下午1:44:15
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_add_position_handler(){
        $Position=M('position');
        $ifAdd=$Position->where("u_id=$_POST[u_id] and p_name='$_POST[p_name]'")->find();
        if($ifAdd){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $res=$Position->add($_POST);
            if($res){
                $this->ajaxReturn("","操作成功",1);
            }else{
                $this->ajaxReturn("","操作失败",0);
            }
        }
    }
    
    /**
     +--------------------------------
     * 删除值班岗位操作
     +--------------------------------
     * @date: 2019年8月7日 下午3:18:16
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_del_position_handler(){
        $Position=M('position');
        $ifDel=$Position->where("u_id=$_GET[u_id] and p_name='$_GET[p_name]'")->delete();
        if($ifDel){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }
    /***************************************************************END值班岗位**********************************************************/
    
    /***************************************************************周历排班**********************************************************/
    /**
     +--------------------------------
     * 排班页
     +--------------------------------
     * @date: 2019年8月9日 下午1:09:27
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_table(){
        $week=array();
        if($_GET[week_first]){
            if($_GET[action]=="after"){
                $week[]=date('Y-m-d',strtotime($_GET[week_first])+3600*24*7);
            }elseif($_GET[action]=="before"){
                $week[]=date('Y-m-d',strtotime($_GET[week_first])-3600*24*7);
            }
        }else{
            $week[]=date('Y-m-d',(time()-((date('w',time())==0?7:date('w',time()))-1)*24*3600)); //w为星期几的数字形式,这里0为
        }
        for($i=0;$i<6;$i++){
            $week[]=date('Y-m-d',strtotime($week[0])+3600*24*($i+1));
        }
        
        //dump($week);
        //所有职位
        $Position=M('position');
        $position=$Position
        ->join("left join t_user on t_user.u_id=t_position.u_id")
        ->select();
        
        //当前周已排班数据
        $Arrange=M('arrange');
        $arrange=$Arrange->where("a_date>='$week[0]' AND a_date<='$week[6]'")->select();
        //dump($arrange);
        //查询当前周的节假日
        $Holiday=M("holiday");
        $holiday=$Holiday->where("h_date>='$week[0]' AND h_date<='$week[6]'")->select();
        $this->assign("holiday",$holiday);
        $this->assign("position",$position);
        $this->assign("arrange",$arrange);
        $this->assign("week",$week);
        $this->assign("today",date("Y-m-d",time()));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 保存排班
    +--------------------------------
    * @date: 2019年8月9日 下午2:47:43
    * @author: zt
    * @param: variable
    * @return:
    */
    public function arrange_save_arrange_handler(){
        /* if($_SESSION['username']!="admin"){
            $this->ajaxReturn("","没有权限操作",0);
        } */
        $Arrange=M('arrange');
        //删除当前周之前的记录
        $Arrange->where("a_date>='$_GET[week_first]' AND a_date<='$_GET[week_last]'")->delete();
        foreach ($_POST as $key=>$v){
            if($v){
                $key_array=explode("@", $key);
                $data['u_id']=$v;
                $data['p_name']=$key_array[0];
                $data['a_date']=$key_array[1];
                $Arrange->add($data);
            }
        }
        $this->ajaxReturn("","操作成功",1);
    }
    
    /***************************************************************END周历排班**********************************************************/
    
    public function getYearHoliday(){
        $day=$startday="2020-01-01";
        $endday="2020-12-31";
        
        $Holiday=M("holiday");
        
        while(strtotime($day)<=strtotime($endday)){
            $week=date("w",strtotime($day));
            if($week==0){
                $data['h_date']=$day;
                $data['h_week']="星期日";
                echo $day."-星期日";
                echo "<br>";
            }elseif($week==6){
                $data['h_date']=$day;
                $data['h_week']="星期六";
                echo $day."-星期六";
                echo "<br>";
            }
            $Holiday->add($data);
            
            $day=date("Y-m-d",strtotime($day)+3600*24);
            echo "<br>";
        }
    }
    
    /***************************************************************节假日管理**********************************************************/
   /**
   +--------------------------------
   * 节假日列表
   +--------------------------------
   * @date: 2019年8月14日 上午9:40:47
   * @author: zt
   * @param: variable
   * @return:
   */
    public function arrange_holiday_table(){
        $date=date("Y-m",time());
        $this->assign("date",$date);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 添加节假日操作
    +--------------------------------
    * @date: 2019年8月14日 上午10:49:29
    * @author: zt
    * @param: variable
    * @return:
    */
    public function arrange_add_holiday_handler(){
        $data['h_date']=$date=$_POST['date'];
        $data['h_remark']=$_POST['h_remark'];
        $weekarray=array("日","一","二","三","四","五","六");
        $data['h_week']="星期".$weekarray[date("w",strtotime($date))];
        $result=$this->addData("holiday",$data);
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 删除节假日操作
     +--------------------------------
     * @date: 2019年8月14日 上午10:49:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_del_holiday_handler(){
        $date=$_GET['date'];
        $result=$this->delData("holiday","h_date='$date'");
        $this->ajaxReturn("",$result['info'],$result['status']);
    }
    
    /**
     +--------------------------------
     * 微信汇总
     +--------------------------------
     * @date: 2019年8月14日 上午10:49:29
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_wechat_week_table(){
        if(!$_POST['scoreTime']){
            $this->display();
            exit();
        }
        $startday=$startdate=substr($_POST['scoreTime'],0,10);
        $endday=$enddate=substr($_POST['scoreTime'],strlen($_POST['scoreTime'])-10);
        $Wechat=M("wechat");
        $wechat=$Wechat->where("ref_date>='$startdate' AND ref_date<='$enddate'")->order("ref_date")->select();
        //判断微信的发送时间在选择的区间内是否为该条微信的最小日期，如是最小日期则该条微信是最小日期发送的
        $wechatTable=array();
        foreach($wechat as $v){
            $minWechatDate=$Wechat->where("msgid='$v[msgid]'")->min('ref_date');
            if($minWechatDate==$v['ref_date']){
                $wechatTable[]=$v;
            }
        }
        
        //查询当日值班人员,统计微信阅读量，分享数，收藏数
        $Arrange=M("arrange");
        foreach ($wechatTable as $key=>$v){
            $arrange=$Arrange->where("a_date='$v[ref_date]' AND (p_name='微信编辑' OR p_name='值班')")
            ->join("left join t_user on t_user.u_id=t_arrange.u_id")
            ->find();
            
            $sumReadCount=$Wechat->where("msgid='$v[msgid]'")->sum('int_page_read_count');
            $sumShareCount=$Wechat->where("msgid='$v[msgid]'")->sum('share_count');
            $sumFavCount=$Wechat->where("msgid='$v[msgid]'")->sum('add_to_fav_count');
            
            $wechatTable[$key]['name']=$arrange['u_realname'];
            $wechatTable[$key]['sumReadCount']=$sumReadCount;
            $wechatTable[$key]['sumShareCount']=$sumShareCount;
            $wechatTable[$key]['sumFavCount']=$sumFavCount;
        }
        
        $this->assign("wechat",$wechatTable);
        $this->display();
    }
    
    
    /***************************************************************END节假日管理**********************************************************/
   
    /***************************************************************分数统计**********************************************************/
    /**
    +--------------------------------
    * 分数统计页
    +--------------------------------
    * @date: 2019年8月14日 下午3:05:05
    * @author: zt
    * @param: variable
    * @return:
    */
    public function arrange_score_week_table(){
        $workScoreList=array();  //工作日得分表
        $holidayScoreList=array();  //节假日得分表
        $summaryList=array();//汇总统计
        $personalList1=array();//个人统计 早班+中班
        $personalList2=array();//个人统计 常日
        $personalList3=array();//个人统计 值班
        $Holiday=M("holiday");
        if(!$_POST['scoreTime']){
            $this->display();
            exit();
        }
        $startday=$startdate=substr($_POST['scoreTime'],0,10);
        $endday=$enddate=substr($_POST['scoreTime'],strlen($_POST['scoreTime'])-10);
        $scoreDays=array();
        while(strtotime($startday)<(strtotime($endday)+3600*24)){
            $scoreDays[]=$startday;
            $startday=date('Y-m-d',strtotime($startday)+3600*24);
        }
        $summaryList['date']=$scoreDays[0]." 至 ".$scoreDays[count($scoreDays)-1];
        $summaryList['dayCount']=count($scoreDays);
        $summaryList['weiboCount']=0;
        $summaryList['commentCount']=0;
        $summaryList['qualifiedCount']=0;
        $summaryList['unqualifiedCount']=0;
        $summaryList['overWeiboCount']=0;
        $summaryList['overCommentCount']=0;
        /* $week=array();
        if($_GET[week_first]){
            if($_GET[action]=="after"){
                $week[]=date('Y-m-d',strtotime($_GET[week_first])+3600*24*7);
            }elseif($_GET[action]=="before"){
                $week[]=date('Y-m-d',strtotime($_GET[week_first])-3600*24*7);
            }
        }else{
            $week[]=date('Y-m-d',(time()-((date('w',time())==0?7:date('w',time()))-1)*24*3600)); //w为星期几的数字形式,这里0为
        }
        for($i=0;$i<6;$i++){
            $week[]=date('Y-m-d',strtotime($week[0])+3600*24*($i+1));
        } */
        foreach($scoreDays as $v){
            //查询当前日期是否为节假日
            $res=$Holiday->where("h_date='$v'")->find();
            if($res){
                $holidayScoreList[$v]['date']=$v;
                $holidayScoreList[$v]['weibo']=-1;
                $holidayScoreList[$v]['comment']=-1;
                $holidayScoreList[$v]['result']=-1;
                $holidayScoreList[$v]['basescore']=-1;
            }else{
                $workScoreList[$v]['date']=$v;
                $workScoreList[$v]['weibo1']=-1;
                $workScoreList[$v]['weibo2_1']=-1;
                $workScoreList[$v]['weibo2_2']=-1;
                $workScoreList[$v]['weibo3']=-1;
                $workScoreList[$v]['comment1']=-1;
                $workScoreList[$v]['comment2']=-1;
                $workScoreList[$v]['comment3']=-1;
                $workScoreList[$v]['basescore1']=-1;
                $workScoreList[$v]['basescore2']=-1;
                $workScoreList[$v]['basescore3']=-1;
                $workScoreList[$v]['result']=-1;
            }
        }
        
        
        
        //查询排班表
        $Arrange=M('arrange');
        $arrange=$Arrange
        ->where("(a_date>='$startdate' AND a_date<='$enddate') AND (p_name='TV技术' OR p_name='中班' OR p_name='值班')")
        ->join("left join t_user on t_user.u_id=t_arrange.u_id")
        ->order('a_date')
        ->select();
        //评分
        $WeiBo=M("weibo");
        $Comment=M("comment");
        foreach($arrange as $key=>$v){
            $score=100;
            $weiboCount2=0;
            //查询排班当天是否为节假日
            $res=$Holiday->where("h_date='$v[a_date]'")->find();
            if($res){
                $startDateTime=$v['a_date']." 08:00:00";
                $endDateTime=$v['a_date']." 22:00:00";
                $weiboTotal=$weiboCount=$WeiBo->where("w_createTime>='$startDateTime' AND w_createTime<='$endDateTime'")->count();
                $commentCount=$Comment->where("c_createTime>='$startDateTime' AND c_createTime<='$endDateTime'")->count();
                //漏发或多发一条信息或评论加或扣12分，多发或漏发一天信息或评论加或减7分
                //节假日，微博不少于6条，评论量不少于4条
                $weiboCount-6>=0?$score+=($weiboCount-6)*12:$score+=($weiboCount-6)*12;
                $commentCount-4>=0?$score+=($commentCount-4)*7:$score+=($commentCount-4)*7;
                $overWeiboCount=$weiboCount-6;//微博超额量
                $overCommentCount=$commentCount-4; //评论超额量
                $holidayScoreList[$v['a_date']]['name']=$v['u_realname'];
                $holidayScoreList[$v['a_date']]['weibo']=$weiboCount;
                $holidayScoreList[$v['a_date']]['comment']=$commentCount;
                $holidayScoreList[$v['a_date']]['score']=$score;
                $holidayScoreList[$v['a_date']]['overWeibo']=$overWeiboCount;
                $holidayScoreList[$v['a_date']]['overComment']=$overCommentCount;
                
                //节假日天微博发博总数大于等于6条&&当天微博回复总数大于等于4条则合格，其他均为不合格；
                if($weiboCount>=6&&$commentCount>=4){
                    $holidayScoreList[$v['a_date']]['result']="合格";
                }else{
                    $holidayScoreList[$v['a_date']]['result']="不合格";
                }
                
                //基础分=100-扣分项
                $basescore=100;
                $weiboCount-6>=0?"":$basescore=$basescore-abs(($weiboCount-6)*12);
                $commentCount-4>0?"":$basescore=$basescore-abs(($commentCount-4)*7);
                $holidayScoreList[$v['a_date']]['basescore']=$basescore;
                $summaryList['weiboCount']+=$weiboTotal; //汇总发博总数
                $summaryList['commentCount']+=$commentCount;  //汇总回复总数
                $summaryList['overWeiboCount']+=$overWeiboCount; //汇总发博超额数
                $summaryList['overCommentCount']+=$overCommentCount; //汇总评论超额总数
            }else{
                //漏发（多发）一条信息（减）加5分，多发（漏发）一天信息或评论加(减)2分
                switch($v['p_name']){
                    case "TV技术":
                        $startDateTime=$v['a_date']." 00:00:01";
                        $endDateTime=$v['a_date']." 08:30:00";
                        $weiboTotal=$weiboCount=$WeiBo->where("w_createTime>='$startDateTime' AND w_createTime<='$endDateTime'")->count();
                        $commentCount=$Comment->where("c_createTime>='$startDateTime' AND c_createTime<='$endDateTime'")->count();
                        //早班，微博不少于3条，评论量不少于5条
                        $weiboCount-3>=0?$score+=($weiboCount-3)*5:$score+=($weiboCount-3)*5;
                        $commentCount-5>=0?$score+=($commentCount-5)*2:$score+=($commentCount-5)*2;
                        $overWeiboCount=$weiboCount-3;//微博超额量
                        $overCommentCount=$commentCount-5; //评论超额量
                        $workScoreList[$v['a_date']]['overWeibo1']=$overWeiboCount;
                        $workScoreList[$v['a_date']]['overComment1']=$overCommentCount;
                        $workScoreList[$v['a_date']]['name1']=$v['u_realname'];
                        $workScoreList[$v['a_date']]['weibo1']=$weiboCount;
                        $workScoreList[$v['a_date']]['comment1']=$commentCount;
                        $workScoreList[$v['a_date']]['score1']=$score;
                        
                        //基础分=100-扣分项
                        $basescore=100;
                        $weiboCount-3>=0?"":$basescore=$basescore-abs(($weiboCount-3)*5);
                        $commentCount-5>0?"":$basescore=$basescore-abs(($commentCount-5)*2);
                        $workScoreList[$v['a_date']]['basescore1']=$basescore;
                        $summaryList['weiboCount']+=$weiboTotal;  //汇总发博总数
                        $summaryList['commentCount']+=$commentCount;  //汇总回复总数
                        $summaryList['overWeiboCount']+=$overWeiboCount; //汇总发博超额数
                        $summaryList['overCommentCount']+=$overCommentCount; //汇总评论超额总数
                        break;
                    case "值班":
                        $startDateTime1=$v['a_date']." 08:30:01";
                        $endDateTime1=$v['a_date']." 17:15:00";
                        $startDateTime2=$v['a_date']." 19:30:01";
                        $endDateTime2=$v['a_date']." 23:59:59";
                        $weiboCount1=$WeiBo->where("w_createTime>='$startDateTime1' AND w_createTime<='$endDateTime1'")->count();
                        $weiboCount2=$WeiBo->where("w_createTime>='$startDateTime2' AND w_createTime<='$endDateTime2'")->count();
                        $commentCount=$Comment->where("(c_createTime>='$startDateTime1' AND c_createTime<='$endDateTime1') OR (c_createTime>='$startDateTime2' AND c_createTime<='$endDateTime2')")->count();
                       //常日班 白天微博不少于6条，19点后微博不少于2条，评论不少于5条
                        $weiboCount1-6>=0?$score+=($weiboCount1-6)*5:$score+=($weiboCount1-6)*5; //白天
                        $weiboCount2-2>=0?$score+=($weiboCount2-2)*5:$score+=($weiboCount2-2)*5; //晚上
                        $weiboTotal=$weiboCount1+$weiboCount2;
                        $commentCount-5>=0?$score+=($commentCount-5)*2:$score+=($commentCount-5)*2;
                        
                        $overWeiboCount=$weiboTotal-8;//微博超额量
                        $overCommentCount=$commentCount-5; //评论超额量
                        $workScoreList[$v['a_date']]['overWeibo2']=$overWeiboCount;
                        $workScoreList[$v['a_date']]['overComment2']=$overCommentCount;
                        
                        $workScoreList[$v['a_date']]['name2']=$v['u_realname'];
                        $workScoreList[$v['a_date']]['weibo2_1']=$weiboCount1;
                        $workScoreList[$v['a_date']]['weibo2_2']=$weiboCount2;
                        $workScoreList[$v['a_date']]['comment2']=$commentCount;
                        $workScoreList[$v['a_date']]['score2']=$score;
                        
                        //基础分=100-扣分项
                        $basescore=100;
                        $weiboCount1-6>=0?"":$basescore=$basescore-abs(($weiboCount1-6)*5);
                        $weiboCount2-2>=0?"":$basescore=$basescore-abs(($weiboCount2-2)*5);
                        $commentCount-5>0?"":$basescore=$basescore-abs(($commentCount-5)*2);
                        $workScoreList[$v['a_date']]['basescore2']=$basescore;
                        $summaryList['weiboCount']+=$weiboTotal;  //汇总发博总数
                        $summaryList['commentCount']+=$commentCount;  //汇总回复总数
                        $summaryList['overWeiboCount']+=$overWeiboCount; //汇总发博超额数
                        $summaryList['overCommentCount']+=$overCommentCount; //汇总评论超额总数
                        break;
                    case "中班":
                        $startDateTime=$v['a_date']." 17:15:01";
                        $endDateTime=$v['a_date']." 19:30:00";
                        $weiboTotal=$weiboCount=$WeiBo->where("w_createTime>='$startDateTime' AND w_createTime<='$endDateTime'")->count();
                        $commentCount=$Comment->where("c_createTime>='$startDateTime' AND c_createTime<='$endDateTime'")->count();
                        //中班，微博不少于4条，评论量不少于5条
                        $weiboCount-4>=0?$score+=($weiboCount-4)*5:$score+=($weiboCount-4)*5;
                        $commentCount-5>=0?$score+=($commentCount-5)*2:$score+=($commentCount-5)*2;
                        $overWeiboCount=$weiboCount-4;//微博超额量
                        $overCommentCount=$commentCount-5; //评论超额量
                        $workScoreList[$v['a_date']]['overWeibo3']=$overWeiboCount;
                        $workScoreList[$v['a_date']]['overComment3']=$overCommentCount;
                        $workScoreList[$v['a_date']]['name3']=$v['u_realname'];
                        $workScoreList[$v['a_date']]['weibo3']=$weiboCount;
                        $workScoreList[$v['a_date']]['comment3']=$commentCount;
                        $workScoreList[$v['a_date']]['score3']=$score;
                        
                        //基础分=100-扣分项
                        $basescore=100;
                        $weiboCount-4>=0?"":$basescore=$basescore-abs(($weiboCount-4)*5);
                        $commentCount-5>0?"":$basescore=$basescore-abs(($commentCount-5)*2);
                        $workScoreList[$v['a_date']]['basescore3']=$basescore;
                        $summaryList['weiboCount']+=$weiboTotal;  //汇总发博总数
                        $summaryList['commentCount']+=$commentCount;  //汇总回复总数
                        $summaryList['overWeiboCount']+=$overWeiboCount; //汇总发博超额数
                        $summaryList['overCommentCount']+=$overCommentCount; //汇总评论超额总数
                        break;
                }
                //是否合格，工作日当天微博发博总数大于等于15条&&当天微博回复总数大于等于15条，则合格，其他均为不合格
                $totalWeibo=$workScoreList[$v['a_date']]['weibo1']+$workScoreList[$v['a_date']]['weibo2_1']+$workScoreList[$v['a_date']]['weibo2_2']+$workScoreList[$v['a_date']]['weibo3'];
                $totalComment=$workScoreList[$v['a_date']]['comment1']+$workScoreList[$v['a_date']]['comment2']+$workScoreList[$v['a_date']]['comment3'];
                if($totalComment>=15&&$totalWeibo>=15){
                    $workScoreList[$v['a_date']]['result']="合格";
                }else{
                    $workScoreList[$v['a_date']]['result']="不合格";
                }
            }
            $arrange[$key][score]=$score;
            $arrange[$key][weibo]=$weiboTotal;
            $arrange[$key][comment]=$commentCount;
            $arrange[$key][weiboNight]=$weiboCount2;
        }
        //保存数据到socre表
        $Score=M("score");
        foreach ($arrange as $v){
            $res=$Score->where("s_date='$v[a_date]' AND p_name='$v[p_name]' AND u_id='$v[u_id]'")->find();
            if(!$res){
                $data['s_date']=$v[a_date];
                $data['p_name']=$v[p_name];
                $data['u_id']=$v[u_id];
                $data['s_score']=$v[score];
                $data['s_weiboCount']=$v[weibo];
                $data['s_weiboNightCount']=$v[weiboNight];
                $data['s_commentCount']=$v[comment];
                $Score->add($data);
                unset($data);
            }
        }
        $tempWork=array();
        $tempHoliday=array();
        //dump($workScoreList);
        //dump($holidayScoreList);
        foreach($workScoreList as $key=>$v){
            /* if(!$v[name1]){
                continue;
            } */
            $tempWork[]=$v;
            if($v['result']=="合格"){
                $summaryList['qualifiedCount']+=1;
            }elseif($v['result']=="不合格"){
                $summaryList['unqualifiedCount']+=1;
            }

            is_numeric($personalList1[$v[name1]]['totalBanci'])?null:$personalList1[$v[name1]]['totalBanci']=0;
            is_numeric($personalList1[$v[name3]]['totalBanci'])?null:$personalList1[$v[name3]]['totalBanci']=0;
            is_numeric($personalList2[$v[name2]]['totalBanci'])?null:$personalList2[$v[name2]]['totalBanci']=0;
            
            is_numeric($personalList1[$v[name1]]['totalScore'])?null:$personalList1[$v[name1]]['totalScore']=0;
            is_numeric($personalList1[$v[name3]]['totalScore'])?null:$personalList1[$v[name3]]['totalScore']=0;
            is_numeric($personalList2[$v[name2]]['totalScore'])?null:$personalList2[$v[name2]]['totalScore']=0;
            
            is_numeric($personalList1[$v[name1]]['qualifiedCount'])?null:$personalList1[$v[name1]]['qualifiedCount']=0;
            is_numeric($personalList1[$v[name1]]['unqualifiedCount'])?null:$personalList1[$v[name1]]['unqualifiedCount']=0;
            is_numeric($personalList1[$v[name3]]['qualifiedCount'])?null:$personalList1[$v[name3]]['qualifiedCount']=0;
            is_numeric($personalList1[$v[name3]]['unqualifiedCount'])?null:$personalList1[$v[name3]]['unqualifiedCount']=0;
            is_numeric($personalList2[$v[name2]]['qualifiedCount'])?null:$personalList2[$v[name2]]['qualifiedCount']=0;
            is_numeric($personalList2[$v[name2]]['unqualifiedCount'])?null:$personalList2[$v[name2]]['unqualifiedCount']=0;

            
            $personalList1[$v[name1]]['date']=$scoreDays[0]." 至 ".$scoreDays[count($scoreDays)-1];
            $personalList1[$v[name3]]['date']=$scoreDays[0]." 至 ".$scoreDays[count($scoreDays)-1];
            $personalList2[$v[name2]]['date']=$scoreDays[0]." 至 ".$scoreDays[count($scoreDays)-1];
            
            $personalList1[$v[name1]]['name']=$v['name1'];
            $personalList1[$v[name3]]['name']=$v['name3'];
            $personalList2[$v[name2]]['name']=$v['name2'];
            
            $personalList1[$v[name1]]['banci']="早中班";
            $personalList1[$v[name3]]['banci']="早中班";
            $personalList2[$v[name2]]['banci']="常日班";
            
            $personalList1[$v[name1]]['totalBanci']+=1;
            $personalList1[$v[name3]]['totalBanci']+=1;
            $personalList2[$v[name2]]['totalBanci']+=1;
            
            $personalList1[$v[name1]]['weiboCount']==0?$personalList1[$v[name1]]['weiboCount']=$v['weibo1']:$personalList1[$v[name1]]['weiboCount']+=$v['weibo1'];
            $personalList1[$v[name3]]['weiboCount']==0?$personalList1[$v[name3]]['weiboCount']=$v['weibo3']:$personalList1[$v[name3]]['weiboCount']+=$v['weibo3'];
            $personalList2[$v[name2]]['weiboCount']==0?$personalList2[$v[name2]]['weiboCount']=$v['weibo2_1']+$v['weibo2_2']:$personalList2[$v[name2]]['weiboCount']+=($v['weibo2_1']+$v['weibo2_2']);
            
            $personalList1[$v[name1]]['commentCount']==0?$personalList1[$v[name1]]['commentCount']=$v['comment1']:$personalList1[$v[name1]]['commentCount']+=$v['comment1'];
            $personalList1[$v[name3]]['commentCount']==0?$personalList1[$v[name3]]['commentCount']=$v['comment3']:$personalList1[$v[name3]]['commentCount']+=$v['comment3'];
            $personalList2[$v[name2]]['commentCount']==0?$personalList2[$v[name2]]['commentCount']=$v['comment2']:$personalList2[$v[name2]]['commentCount']+=$v['comment2'];
            
            $personalList1[$v[name1]]['totalScore']+=$v['score1'];
            $personalList1[$v[name3]]['totalScore']+=$v['score3'];
            $personalList2[$v[name2]]['totalScore']+=$v['score2'];
            
            if($v['basescore1']<100){
                $personalList1[$v[name1]]['unqualifiedCount']+=1;
            }else{
                $personalList1[$v[name1]]['qualifiedCount']+=1;
            }
            
            if($v['basescore3']<100){
                $personalList1[$v[name3]]['unqualifiedCount']+=1;
            }else{
                $personalList1[$v[name3]]['qualifiedCount']+=1;
            }
            
            if($v['basescore2']<100){
                $personalList2[$v[name2]]['unqualifiedCount']+=1;
            }else{
                $personalList2[$v[name2]]['qualifiedCount']+=1;
            }
        }
        
        foreach($holidayScoreList as $key=>$v){
            /* if(!$v[name]){
                continue;
            } */
            $tempHoliday[]=$v;
            if($v['result']=="合格"){
                $summaryList['qualifiedCount']+=1;
            }elseif($v['result']=="不合格"){
                $summaryList['unqualifiedCount']+=1;
            }
            
            is_numeric($personalList3[$v[name]]['totalBanci'])?null:$personalList3[$v[name]]['totalBanci']=0;
            is_numeric($personalList3[$v[name]]['totalScore'])?null:$personalList3[$v[name]]['totalScore']=0;
            is_numeric($personalList3[$v[name]]['qualifiedCount'])?null:$personalList3[$v[name]]['qualifiedCount']=0;
            is_numeric($personalList3[$v[name]]['unqualifiedCount'])?null:$personalList3[$v[name]]['unqualifiedCount']=0;
            
            $personalList3[$v[name]]['date']=$scoreDays[0]." 至 ".$scoreDays[count($scoreDays)-1];
            $personalList3[$v[name]]['name']=$v['name'];
            $personalList3[$v[name]]['banci']="节假日值班";
            $personalList3[$v[name]]['weiboCount']+=$v['weibo'];
            $personalList3[$v[name]]['commentCount']+=$v['comment'];
            $personalList3[$v[name]]['totalBanci']+=1;
            $personalList3[$v[name]]['totalScore']+=$v['score'];
            if($v['basescore']<100){
                $personalList3[$v[name]]['unqualifiedCount']+=1;
            }else{
                $personalList3[$v[name]]['qualifiedCount']+=1;
            }
        }
        $summaryList['qualifiedRate']=round($summaryList['qualifiedCount']/($summaryList['qualifiedCount']+$summaryList['unqualifiedCount'])*100)."%";
        //合并早中班，常日班，值班数组
        foreach($personalList1 as $v){
            $personalList[]=$v;
        }
        foreach($personalList2 as $v){
            $personalList[]=$v;
        }
        foreach($personalList3 as $v){
            $personalList[]=$v;
        }
        
        //计算平局分
        foreach ($personalList as $key=>$v){
            if($v['weiboCount']<0){
                unset($personalList[$key]);
                continue;
            }
            $personalList[$key]['avgScore']=round($v['totalScore']/$v['totalBanci'],1);
            $personalList[$key]['qualifiedRate']=round($v['qualifiedCount']/($v['qualifiedCount']+$v['unqualifiedCount'])*100)."%";
        }
        
        //dump($personalList);
        $this->assign("workScore",$tempWork);
        $this->assign("holidayScore",$tempHoliday);
        $this->assign("summary",$summaryList);
        $this->assign("personal",$personalList);
        //$this->assign("week",$week);
        //$this->assign("today",date("Y-m-d",time()));
        $this->assign("scoreTime",$_POST['scoreTime']);
        $this->display();
    }
    
    /***************************************************************END分数统计**********************************************************/
    /**
     +--------------------------------
     * 根据微博打分日历，获取微博或微博的评论
     +--------------------------------
     * @date: 2019年8月1日 上午11:14:01
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_get_weibo_data(){
        $Weibo=M('weibo');
        $Comment=M('comment');
        $startDateTime=$_GET['scoreDate']." ".$_GET['scoreStartTime'];
        $endDateTime=$_GET['scoreDate']." ".$_GET['scoreEndTime'];
        $startDateTime2=$_GET['scoreDate']." ".$_GET['scoreStartTime2'];
        $endDateTime2=$_GET['scoreDate']." ".$_GET['scoreEndTime2'];
        switch ($_GET['type']){
            case "weibo":
                $weibo=$Weibo->where("w_createTime>='$startDateTime' AND w_createTime<='$endDateTime'")->order('w_id')->select();
                $this->assign("weibo",$weibo);
                $this->display("arrange_weibo_table");
                break;
            case "comment":
                $comment=$Comment->where("c_createTime>='$startDateTime' AND c_createTime<='$endDateTime'")->order('c_id')->select();
                $this->assign("comment",$comment);
                $this->display("arrange_comment_table");
                break;
            case "comment2":
                $comment=$Comment->where("(c_createTime>='$startDateTime' AND c_createTime<='$endDateTime') OR (c_createTime>='$startDateTime2' AND c_createTime<='$endDateTime2')")->order('c_id')->select();
                $this->assign("comment",$comment);
                $this->display("arrange_comment_table");
                break;
        }
    }
    
    /**
     +--------------------------------
     * 根据微博w_id，获取关联的稿件d_id
     +--------------------------------
     * @date: 2019年8月1日 上午11:14:01
     * @author: zt
     * @param: variable
     * @return:
     */
    public function arrange_get_document_id_handler(){
        $w_id=$_POST['w_id'];
        $Document=M('document');
        $res=$Document->where("d_relatedWeibo='$w_id'")->find();
        if($res){
            $this->ajaxReturn($res['d_id'],"操作成功",1);
        }else{
            $this->ajaxReturn("","未查询到关联的稿件",0);
        }
    }
}