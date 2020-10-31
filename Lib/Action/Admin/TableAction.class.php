<?php
class TableAction extends Action{
    
	/**
	+--------------------------------
	* 获取页面表格数据
	+--------------------------------
	* @date: 2018年8月8日 下午1:39:34
	* @author: zt
	* @param: variable
	* @return:
	*/
    public function getTableData(){
        $tableName=$_GET['tableName'];
        $tableType=$_GET['tableType'];
        $Table=M($tableName);
        $limit=$_GET['limit'];
        $firstRow=($_GET['page']-1)*$limit;
        $count=$Table->count();
        
        //表格类型
        switch($tableType){
            //ab7000_00001
            case "ab_1":
                $condition="1=1";

                if($_GET['createtime']){
                    $start=substr($_GET['createtime'],0,10)." 00:00:00";
                    $end=substr($_GET['createtime'],strlen($_GET['createtime'])-10)." 23:59:59";
                    $condition.=" and (AlarmDate>='$start' and AlarmDate<='$end')";
                }

                $data=$Table
                ->where($condition)
                ->order("AlarmDate desc")
                ->limit($firstRow.','.$limit)->select();
                $count=$Table->where($condition)->count();
                break;

            //ab7000_00002
            case "ab_2":
                $condition="1=1";

                if($_GET['createtime']){
                    $start=substr($_GET['createtime'],0,10)." 00:00:00";
                    $end=substr($_GET['createtime'],strlen($_GET['createtime'])-10)." 23:59:59";
                    $condition.=" and (AlarmDate>='$start' and AlarmDate<='$end')";
                }

                $data=$Table
                ->where($condition)
                ->order("AlarmDate desc")
                ->limit($firstRow.','.$limit)->select();
                $count=$Table->where($condition)->count();
                break;

            //ab7000_00007
            case "ab_7":
                $condition="1=1";

                if($_GET['createtime']){
                    $start=substr($_GET['createtime'],0,10)." 00:00:00";
                    $end=substr($_GET['createtime'],strlen($_GET['createtime'])-10)." 23:59:59";
                    $condition.=" and (OPDateTime>='$start' and OPDateTime<='$end')";
                }

                $data=$Table
                ->where($condition)
                ->order("OPDateTime desc")
                ->limit($firstRow.','.$limit)->select();
                $count=$Table->where($condition)->count();
                break;

           case "schedule":
                 $condition="1=1";
                 $data=$Table
                 ->where($condition)
                 ->order("s_id desc")
                 ->limit($firstRow.','.$limit)->select();
                 foreach($data as $key=>$v){
                    if($v['s_type']=="period"){
                        $data[$key]['start']=$v['s_startDateTime'];
                        $data[$key]['end']=$v['s_endDateTime'];
                    }else{
                        $data[$key]['start']=$v['s_tempStartDateTime'];
                        $data[$key]['end']=$v['s_tempEndDateTime'];
                    }
                 }
                 $count=$Table->where($condition)->count();
                 break;
           
            default:
                $data=$Table->limit($firstRow.','.$limit)->select();
        }
        
        $result=array(
            'code'=>0,
            'msg'=>'',
            'count'=>$count,
            'data'=>$data
        );
        //dump($result);
        echo json_encode($result);
    }
    
    /**
     +--------------------------------
     * 查询稿件审核的领导
     +--------------------------------
     * @date: 2018年8月8日 下午1:39:34
     * @author: zt
     * @param: $document 稿件数组
     * @return: array
     */
    private function getDocumentAuditLeaderList($document){
        //查询稿件审核流程的领导 表t_leaderauditlist
        $Leaderauditlist=M("leaderauditlist");
        foreach($document as $key=>$v){
            $leaders=$Leaderauditlist
            ->where("d_id='$v[d_id]'")
            ->join("left join t_user on t_user.u_id=t_leaderauditlist.l_userID")
            ->select();
            foreach ($leaders as $l){
                $leader_names.="(".$l['u_realname'].")";
            }
            $document[$key]["audit_leader"]=$leader_names;
            unset($leader_names);
        }
        return $document;
    }
}