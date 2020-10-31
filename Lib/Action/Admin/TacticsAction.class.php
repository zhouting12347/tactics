<?php
class TacticsAction extends Action{

    //策略页
    public function index(){
        $this->display();
    }
   
    //添加策略操作
    public function tactics_add_handler(){
        $Schedule=M('schedule');
        $i=0;
        foreach($_POST['week'] as $v){
            $i==0?$week.=$v:$week.=";".$v;
            $i=1;
        }
        $_POST['s_week']=$week;
        $_POST['s_startDateTime']=$_POST['s_startDate']." ".$_POST['s_startTime'];
        $_POST['s_endDateTime']=$_POST['s_endDate']." ".$_POST['s_endTime'];
        $_POST['s_tempStartDateTime']=$_POST['s_tempDate']." ".$_POST['s_startTime'];
        $_POST['s_tempEndDateTime']=$_POST['s_tempDate']." ".$_POST['s_endTime'];
        $result=$Schedule->add($_POST);

        if($result){
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
    }

     //删除策略操作
     public function tactics_del_handler(){
        $s_id=$_GET['s_id'];
        $Schedule=M('schedule');
        $result=$Schedule->where("s_id='$s_id'")->delete();
        if($result){
            //删除list列表
            $List=M('list');
            $List->where("s_id='$s_id'")->delete();
            $this->ajaxReturn("","操作成功",1);
        }else{
            $this->ajaxReturn("","操作失败",0);
        }
     }

     //生成策略详情列表
     public function tactics_create_list(){
        $s_id=$_GET['s_id'];
        $Schedule=M('schedule');
        $List=M('list');
        $schedule=$Schedule->where("s_id='$s_id'")->find();
        $datearr=$this->getDateRange($schedule['s_startDate'],$schedule['s_endDate']);
        $count=0;
        foreach($datearr as $v){
            $weekday=$this->getWeekDay($v);
            //echo stripos($schedule['s_week'],$weekday);
            if(is_numeric(stripos($schedule['s_week'],$weekday))){
                $data['s_id']=$schedule['s_id'];
                $data['l_startDateTime']=$v." ".$schedule['s_startTime'];
                $data['l_endDateTime']=$v." ".$schedule['s_endTime'];
                $data['l_channelName']=$schedule['s_channelName'];
                $data['l_channelId']=$schedule['s_channelId'];
                $List->add($data);
                $count++;
            }
        }
        if($count>0){
            $Schedule->where("s_id='$s_id'")->setField("s_status",1);
            $this->ajaxReturn("","生成详细列表成功",1);
        }else{
            $this->ajaxReturn("","生成详细列表失败,周期内没有匹配的日期",0);
        }
     }

     /**
     * 获取指定日期段内每一天的日期
     * @date 2017-02-23 14:50:29
     *
     * @param $startdate
     * @param $enddate
     *
     * @return array
     */
    function getDateRange($startdate, $enddate) {
        $stime = strtotime($startdate);
        $etime = strtotime($enddate);
        $datearr=array();
        while ($stime <= $etime) {
            $datearr []= date('Y-m-d', $stime);//得到dataarr的日期数组。
            $stime = $stime + 86400;
        }
        return $datearr;
    }

    //返回星期几
    function getWeekDay($date) {
        $date = str_replace('/','-',$date);
        $dateArr = explode("-", $date);
        $week=array(
            1=>"星期一",
            2=>"星期二",
            3=>"星期三",
            4=>"星期四",
            5=>"星期五",
            6=>"星期六",
            7=>"星期日",
        );
        $weekday=date("N", mktime(0,0,0,$dateArr[1],$dateArr[2],$dateArr[0]));
        return $week[$weekday];
       }
}