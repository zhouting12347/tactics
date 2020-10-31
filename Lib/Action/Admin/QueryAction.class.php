<?php
class QueryAction extends CommonAction{
    /***************************************by clips**********************************************/
	/**
	+--------------------------------
	* query by clips 
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function query_clip_table(){
        $this->assign("cliptype",$this->getData("cliptype","1=1",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * 查询clip相关视频
    +--------------------------------
    * @date: 2019年5月28日 下午3:54:32
    * @author: zt
    * @param: variable
    * @return:
    */
    public function query_clip_order_relative_layer(){
        $this->assign("C_ID",$_GET['id']);
        $this->display();
    }
    
    /**
    +--------------------------------
    * 查询clip相关player
    +--------------------------------
    * @date: 2019年5月29日 上午11:08:53
    * @author: zt
    * @param: variable
    * @return:
    */
    public function query_clip_player_relative_layer(){
        $this->assign("C_ID",$_GET['id']);
        $this->assign("playertype",$this->getData("playertype","CompanyID=$_SESSION[company_id]",1));
        $this->assign("area",$this->getData("area","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    /***************************************END by clips**********************************************/
    
    /***************************************by orders**********************************************/
    /**
     +--------------------------------
     * query by orders
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function query_order_table(){
        $this->assign("cliptype",$this->getData("cliptype","1=1",1));
        $this->display();
    }
    /***************************************END by orders**********************************************/
    
    /***************************************by players**********************************************/
    /**
     +--------------------------------
     * query by players
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: variable
     * @return:
     */
    public function query_player_table(){
        $this->assign("playertype",$this->getData("playertype","CompanyID=$_SESSION[company_id]",1));
        $this->assign("area",$this->getData("area","CompanyID=$_SESSION[company_id]",1));
        $this->display();
    }
    
    /**
    +--------------------------------
    * player视频单元显示页
    +--------------------------------
    * @date: 2019年5月30日 下午2:14:29
    * @author: zt
    * @param: variable
    * @return:
    */
    public function query_unit_layer(){
        $P_ID=$_GET['id'];
        if($_GET['status']==='0'){ //待播订单
            $OrderClipRel=M('ordercliprel');
            $O_ID=$_GET['O_ID'];
            $clips=$OrderClipRel
            ->where("O_ID=$O_ID")
            ->join("left join t_clips on t_clips.C_ID=t_ordercliprel.C_ID")
            ->select();
        }else{
            $PlayerClipRel=M('playercliprel');
            $currentDate=date("Y-m-d",time());
            $clips=$PlayerClipRel
            ->join("left join t_clips on t_clips.C_ID=t_playercliprel.C_ID")
            ->where("P_ID='$P_ID' and (O_StartDate<='$currentDate' and O_EndDate>='$currentDate')")
            ->select();
        }
        //拼接图片地址,视频分类
        $ClipType=M('cliptype');
        foreach($clips as $k=>$v){
            $res=$ClipType->where("CT_ID=$v[CT_ID]")->find();
            $clips[$k]['imgurl']=C("VIDEO_URL")."image/".$v['C_Thumbnail'].".jpg";
            $clips[$k]['type']=$res['CT_Name'];
        }
        //echo $PlayerClipRel->getLastSql();
        //dump($clips);
        $this->assign("clips",json_encode($clips));
        $this->display();
    }
    
    /**
    +--------------------------------
    * player当前完整节目单页
    +--------------------------------
    * @date: 2019年5月31日 上午11:21:48
    * @author: zt
    * @param: variable
    * @return:
    */
    public function query_whole_layer(){
        $sn=$_GET['sn']; //机顶盒序列号
        $xml_path=C('PLAYLIST_XML_PATH').$sn."_T.xml";
        $xml = simplexml_load_file($xml_path);
        $tables="";
        $i=0;
        $j=1;
        $Clips=M('clips');
        foreach($xml as $v){
            $starttime=$xml[0]->Timer[$i]->Start;
            $table="<h1 style='margin-top:20px;marign-bottom:10px;'>".$xml[0]->Timer[$i]->Start."-".$xml[0]->Timer[$i]->End."</h1>";
            $table.='<table class="layui-table"><thead>
                            <tr>
                              <th>NO.</th>
                              <th>Title</th>
                              <th>Start time</th>
                              <th>End time</th>
                              <th>Duration</th>
                              <th>Type</th>
                            </tr>
                          </thead>
                          <tbody>';
            foreach($v as $k=>$s){
                //echo $s[0];
                //echo $s->ID;
                if($s->ID){
                    $C_ID=substr($s->ID,0,-4);
                    $clip=$Clips
                    ->where("C_ID='$C_ID'")
                    ->join("left join t_clipType on t_clipType.CT_ID=t_clips.CT_ID")
                    ->find();
                    $endtime=secondsToTime2(timeToSeconds($starttime)+$clip[C_Length]);
                    $table.="<tr><td>$j</td><td><a href='#' onclick='videoLayer($C_ID)'>$clip[C_Title]</a></td><td>$starttime</td><td>$endtime</td><td>$clip[C_Duration]</td><td>$clip[CT_Name]</td></tr>";
                    $starttime=$endtime;
                    $j++;
                }
                //dump($s);
            }
           $table.="</tbody></table>";
           $tables.=$table;
           unset($table);
           $i++;
           $j=1;
        }
        $this->assign("table",$tables);
        $this->display();
    }
    
    /**
    +--------------------------------
    * player高级查询
    +--------------------------------
    * @date: 2019年5月31日 下午3:56:46
    * @author: zt
    * @param: variable
    * @return:
    */
    public function query_advance_layer(){
        $this->assign("P_ID",$_GET['id']); //player id
        $this->display();
    }
    
    /***************************************END by players**********************************************/
}