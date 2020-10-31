<?php
class AbAction extends ApiAction{

	/*
	status	message
	050100	成功
	050101	参数解析异常
	050102	资源未找到
	050103	请求方式有误
	050104	服务器异常
	050105	鉴权异常
	*/


	private $accessId='gbdsaqbczsxt'; //分配给外部系统的请求id
	//private $host="http://10.85.241.9:8080/data/push"; //接口地址
	private $host="http://sjgx.palmyou.cn/data/push"; //接口地址


	function __construct()
	{
	
	}


	public function test(){
		import('ORG.Net.Aes');
		$Aes=new Aes();
		$data=array(
			"ProgramName"=>"aaaaa"
		);

		$data='[{"ProgramName":"aaaaa"}]';
		$data=$Aes->encrypt($data);
		
		$curlData=array(
			"appId"=>$this->accessId,
			"resourceId"=>"ab7000000001",
			"requestData"=>$data
		);
		echo json_encode($curlData);
		//echo $Aes->decrypt("BEGDbdgWtWYSak80F42t+UZ54u1+BizqiJ7bTHTjwjTBamnH6q8cnSBTlAGo+NOG");
		$result=$this->curlPost($this->host,json_encode($curlData));
		dump($result);
		
	}

	public function ab1_submit(){
		import('ORG.Net.Aes');
		$Aes=new Aes();
		$ab_id=$_GET['ab_id'];
		$Ab1=M("ab_1");
		$ab1=$Ab1->where("ab_id='$ab_id'")->find();
		$data=array(
			"AlarmDate"=>$ab1['AlarmDate'],
			"Duration"=>$ab1['Duration'],
			"ProgramName"=>$ab1['ProgramName'],
			"CreatTime"=>$ab1['CreatTime'],
			"AlarmHappenTime"=>$ab1['AlarmHappenTime'],
			"AlarmEndTime"=>$ab1['AlarmEndTime'],
			"FaultReason"=>$ab1['FaultReason'],
			"CreatMan"=>$ab1['CreatMan'],
			"ChannelName"=>$ab1['ChannelName'],
			"frequencyValue"=>$ab1['frequencyValue'],
			"IfNewsTime"=>$ab1['IfNewsTime'],
			"DutyMan1"=>$ab1['DutyMan1'],
			"DutyMan2"=>$ab1['DutyMan2'],
			"Description"=>$ab1['Description'],
			"AlarmType"=>$ab1['AlarmType'],
			"SignalType"=>$ab1['SignalType'],
			"AlarmLevel"=>$ab1['AlarmLevel']
		);
		$data="[".json_encode($data)."]";
		$data=$Aes->encrypt($data);
		$curlData=array(
			"appId"=>$this->accessId,
			"resourceId"=>"ab7000000001",
			"requestData"=>$data
		);
		$result=$this->curlPost($this->host,json_encode($curlData));
		if($result->code==200){
			$ab1=$Ab1->where("ab_id='$ab_id'")->setField("Status",1);
			$this->ajaxReturn('','操作成功',1);
		}else{
			dump($result);
			$this->ajaxReturn('',$result->msg,0);
		}
	}

	public function ab2_submit(){
		import('ORG.Net.Aes');
		$Aes=new Aes();
		$ab_id=$_GET['ab_id'];
		$Ab2=M("ab_2");
		$ab2=$Ab2->where("ab_id='$ab_id'")->find();
		$data=array(
			"AlarmDate"=>$ab2['AlarmDate'],
			"MonitorStation"=>$ab2['MonitorStation'],
			"CreatTime"=>$ab2['CreatTime'],
			"CreatMan"=>$ab2['CreatMan'],
			"AlarmHappenTime"=>$ab2['AlarmHappenTime'],
			"frequencyValue"=>$ab2['FrequencyValue'],
			"Description"=>$ab2['Description'],
			"VerifyDepart"=>$ab2['VerifyDepart'],
		);
		$data="[".json_encode($data)."]";
		$data=$Aes->encrypt($data);
		$curlData=array(
			"appId"=>$this->accessId,
			"resourceId"=>"ab7000000002",
			"requestData"=>$data
		);
		$result=$this->curlPost($this->host,json_encode($curlData));
		if($result->code==200){
			$ab2=$Ab2->where("ab_id='$ab_id'")->setField("Status",1);
			$this->ajaxReturn('','操作成功',1);
		}else{
			dump($result);
			$this->ajaxReturn('',$result->msg,0);
		}
	}

	public function ab7_submit(){
		import('ORG.Net.Aes');
		$Aes=new Aes();
		$ab_id=$_GET['ab_id'];
		$Ab7=M("ab_7");
		$ab7=$Ab7->where("ab_id='$ab_id'")->find();
		$data=array(
			"ApplyFor"=>$ab7['ApplyFor'],
			"OPDateTime"=>$ab7['OPDateTime'],
			"DutyPhone"=>$ab7['DutyPhone'],
			"AffectStation"=>$ab7['AffectStation'],
			"DutyMan"=>$ab7['DutyMan'],
			"Emergency"=>$ab7['Emergency'],
			"AffectAbout"=>$ab7['AffectAbout'],
			"AskDate"=>$ab7['AskDate'],
			"OPDetail"=>$ab7['OPDetail'],
			"AffectRange"=>$ab7['AffectRange'],
			"Department"=>$ab7['Department'],
		);
		$data="[".json_encode($data)."]";
		$data=$Aes->encrypt($data);
		$curlData=array(
			"appId"=>$this->accessId,
			"resourceId"=>"ab7000000007",
			"requestData"=>$data
		);

		$result=$this->curlPost($this->host,json_encode($curlData));
		//dump($result);
		if($result->code==200){
			$ab7=$Ab7->where("ab_id='$ab_id'")->setField("Status",1);
			$this->ajaxReturn('','操作成功',1);
		}else{
			dump($result);
			$this->ajaxReturn('',$result->msg,0);
		}
	}


	//心跳接口
	public function heartbeat(){
		$curlData=array(
			"appId"=>$this->accessId,
			"resourceId"=>"heartbeat"
		);
		$result=$this->curlPost($this->host,json_encode($curlData));
		echo $result->code;
		dump($result);
	}




}