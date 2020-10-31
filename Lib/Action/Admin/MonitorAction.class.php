<?php
class MonitorAction extends ApiAction{

	/*
	status	message
	050100	成功
	050101	参数解析异常
	050102	资源未找到
	050103	请求方式有误
	050104	服务器异常
	050105	鉴权异常
	*/


	private $accessId=123; //分配给外部系统的请求id
	private $timeStamp;  //uninx时间戳
	private $accessKey=""; //私钥
	private $sig; //密文
	private $host_old="http://106.52.50.213:443/microserver-outside/"; //接口地址
	private $host="http://180.163.104.243/api/microserver-outside/"; //接口地址


	function __construct()
	{
		$this->timeStamp=time()."000";
		$this->sig=hash("sha256","accessId=".$this->accessId."&accessKey=".$this->accessKey."&timeStamp=".$this->timeStamp);
	}

	//专项任务内容
	public function specialTaskList(){
		$url=$this->host."api/outside/specialTask/list";
		$curlData=array(
			"accessId"=>$this->accessId,
			"timeStamp"=>$this->timeStamp,
			"sig"=>$this->sig,
			"search"=>json_encode(array())
		);
		//dump($curlData);
		$curlData='{"accessId":123,"timeStamp":"'.$this->timeStamp.'","sig":"28dbd5a9e61b31892d1e74abfd317ffa14bd8b7ee93a9ce5ae8871aaa35729d9","search":{}}';

		$result=$this->curlPost($url,$curlData);
		//dump($result);

		for($i=0;$i<count($result->data);$i++){
			$describeArray[]=$result->data[$i]->describe;
		}

		for($i=0;$i<count($result->data);$i++){
			$idArray[]=$result->data[$i]->id;
		}

		$res=array(
			"code"=>$result->code,
			"message"=>$result->message,
			"data"=>array(
				"describe"=>$describeArray,
				"id"=>$idArray
			)
		);
		//dump($res);
		$this->saveTask($result);
		echo json_encode($res);

	}
	
	//专项任务采集统计
	public function specialTaskNumber(){
		//获取任务id 获取num最小status为0的id
		$Tasklist=M('tasklist');
		$res=$Tasklist->where("status=0")->field("taskId,min(num)")->find();
		if($res['taskId']){
			$taskId=$res['taskId'];
		}else{
			$num=$Tasklist->where("status=1")->max("num");
			$res=$Tasklist->where("num=$num")->find();
			$taskId=$res['taskId'];
		}
		//echo $taskId;
		$Tasklist->where("taskId='$taskId'")->setField("status",1); 

		$url=$this->host."api/outside/specialTaskNumber";
		$curlData=array(
			"accessId"=>$this->accessId,
			"timeStamp"=>$this->timeStamp,
			"sig"=>$this->sig,
			"id"=>$taskId
		);

		$result=$this->curlPost($url,json_encode($curlData));
		//dump($result);

		for($i=0;$i<count($result->data->classProportion);$i++){
			//$describeArray[]=$result->data[$i]->describe;
		}

		

		$res=array(
			"code"=>$result->code,
			"message"=>$result->message,
			"data"=>array(
				"collectorTotal"=>$result->data->data->collectorTotal,
				"classTotal"=>$result->data->data->classTotal,
				"qtsiteProportion"=>34,
				"xmlysiteProportion"=>86,
				//"classProportion"=>array("有声书","历史","体育")
			)
		);
		echo json_encode($res);

	}

	//专项任务智能处理
	public function intelligence(){
		//获取任务id 获取status为1时num最大的id
		$Tasklist=M('tasklist');
		$num=$Tasklist->where("status=1")->max("num");
		$res=$Tasklist->where("num=$num")->find();
		$taskId=$res['taskId'];

		$url=$this->host."api/taskIntelligence/getResults";
		$curlData=array(
			"accessId"=>$this->accessId,
			"timeStamp"=>$this->timeStamp,
			"sig"=>$this->sig,
			"programId"=>$taskId,
			"id"=>$taskId
		);
		$result=$this->curlPost($url,json_encode($curlData));
		//dump($result);

		for($i=0;$i<count($result->data->classifySet);$i++){
			$problemType[]=$result->data->classifySet[$i]->classifyName;
			$problemCount[]=$result->data->classifySet[$i]->classifyAmount;
		}

		for($i=0;$i<count($result->data->xmly->problem->type);$i++){
			$xmlyProblemType[]=$result->data->xmly->problem->type[$i]->problemType;
		}
		
		for($i=0;$i<count($result->data->qt->problem->type);$i++){
			$qtKeyword[]=$result->data->qt->problem->type[$i]->keyWord;
		}

		for($i=0;$i<count($result->data->xmly->problem->type);$i++){
			$xmlyKeyword[]=$result->data->xmly->problem->type[$i]->keyWord;
		}

		$res=array(
			"code"=>$result->code,
			"message"=>$result->message,
			"data"=>array(
				"qt"=>array(
					"problemCount"=>$problemCount,
					"problemType"=>$problemType,
					"keyword"=>$result->data->keyWordsSet,
					"totalCount"=>$result->data->problemNum
				),
				"xmly"=>array(
					"problemCount"=>$result->data->xmly->problem->count,
					"problemType"=>$xmlyProblemType,
					"keyword"=>$xmlyKeyword
				),
				//"totalCount"=>array($result->data->problemNum,$result->data->xmly->count)
			)
		);
		echo json_encode($res);
	}

	//专项任务人工处理
	public function manual(){
		//获取任务id 获取status为1时num最大的id
		$Tasklist=M('tasklist');
		$num=$Tasklist->where("status=1")->max("num");
		$res=$Tasklist->where("num=$num")->find();
		$taskId=$res['taskId'];


		$url=$this->host."api/outside/specialTask/manual";
		$curlData=array(
			"accessId"=>$this->accessId,
			"timeStamp"=>$this->timeStamp,
			"sig"=>$this->sig,
			"id"=>$taskId
		);
		$result=$this->curlPost($url,json_encode($curlData));
		//dump($result);

		for($i=0;$i<count($result->data->qt->problem->type);$i++){
			if($result->data->qt->problem->type[$i]->problemType){
				$qtProblemType=$result->data->qt->problem->type[$i]->problemType;
			}
		}
		for($i=0;$i<count($result->data->xmly->problem->type);$i++){
			if($result->data->xmly->problem->type[$i]->problemType){
				$xmlyProblemType=$result->data->xmly->problem->type[$i]->problemType;
			}
		}
		
		for($i=0;$i<count($result->data->qt->problem->type);$i++){
			if($result->data->qt->problem->type[$i]->keyWord){
				$qtKeyword=$result->data->qt->problem->type[$i]->keyWord;
			}
			
		}

		for($i=0;$i<count($result->data->xmly->problem->type);$i++){
			if($result->data->xmly->problem->type[$i]->keyWord){
				$xmlyKeyword=$result->data->xmly->problem->type[$i]->keyWord;
			}
		}

		//dump($xmlyProblemType);

		$res=array(
			"code"=>$result->code,
			"message"=>$result->message,
			"data"=>array(
				"qt"=>array(
					"problemCount"=>$result->data->qt->problem->count,
					"problemType"=>$qtProblemType,
					"keyword"=>$qtKeyword
				),
				"xmly"=>array(
					"problemCount"=>$result->data->xmly->problem->count,
					"problemType"=>$xmlyProblemType,
					"keyword"=>$xmlyKeyword
				),
				//"totalCount"=>array($result->data->qt->count,$result->data->xmly->count)
			)
		);
		echo json_encode($res);
	}

	//采集量
	public function collection(){
		$url=$this->host_old."api/outside/collection/0";
		$curlData=array(
			"accessId"=>$this->accessId,
			"timeStamp"=>$this->timeStamp,
			"sig"=>$this->sig,
		);

		//echo '{"code":0,"message":"成功","data":[{"相声","访谈","新闻"},{"2000","2000,"2000"},{"20%","50%","30%"}]}';
		/*
		$res=array(
			"code"=>'0',
			"message"=>"成功",
			"data"=>array(
				"className"=>array("相声","访谈","新闻"),
				"total"=>array("2000","4000","3000"),
				"proportion"=>array("20%","50%","30%"),
			)
		);
		*/
		$result=$this->curlPost($url,json_encode($curlData));
		//echo (json_encode($result));
		//dump($result);
		//echo $result->data[0]->className;
		//echo count($result->data);
		//dump($result);
		$classNameArray=array();
		for($i=0;$i<count($result->data->newClass);$i++){
			$classNameArray[]=$result->data->newClass[$i]->className;
		}
		
		$totalArray=array();
		for($i=0;$i<count($result->data->newClass);$i++){
			$totalArray[]=$result->data->newClass[$i]->total;
		}

		$proportion=array();
		for($i=0;$i<count($result->data->newClass);$i++){
			$proportion[]=$result->data->newClass[$i]->proportion;
		}


		$res=array(
			"code"=>$result->code,
			"message"=>$result->message,
			"data"=>array(
				"className"=>$classNameArray,
				"total"=>$totalArray,
				"proportion"=>$proportion,
			)
		);
		echo json_encode($res);

	}

		//采集增量
		public function increment(){
			$url=$this->host_old."api/outside/collection/increment/0";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);
			$qtClassNameArray=array();
			$xmlyClassNameArray=array();
			for($i=0;$i<count($result->data->qt);$i++){
				$qtClassNameArray[]=$result->data->qt[$i]->className;
			}

			//$qtClassNameArray[]="故事";

			for($i=0;$i<count($result->data->xmly);$i++){
				$xmlyClassNameArray[]=$result->data->xmly[$i]->className;
			}

			$qtTotalArray=array();
			$xmlyTotalArray=array();
			for($i=0;$i<count($result->data->qt);$i++){
				$qtTotalArray[]=$result->data->qt[$i]->total;
			}
			//$qtTotalArray[]="200";
			
			for($i=0;$i<count($result->data->xmly);$i++){
				$xmlyTotalArray[]=$result->data->xmly[$i]->total;
			}

			$qtProportion=array();
			$xmlyProportion=array();
			for($i=0;$i<count($result->data->qt);$i++){
				$qtProportion[]=$result->data->qt[$i]->proportion;
			}

			//$qtProportion[]="20%";

			for($i=0;$i<count($result->data->xmly);$i++){
				$xmlyProportion[]=$result->data->xmly[$i]->proportion;
			}

			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"className"=>$qtClassNameArray,
						"total"=>$qtTotalArray,
						"proportion"=>$qtProportion,
						"number"=>count($qtClassNameArray)
					),
					"xmly"=>array(
						"className"=>$xmlyClassNameArray,
						"total"=>$xmlyTotalArray,
						"proportion"=>$xmlyProportion,
						"number"=>count($xmlyClassNameArray)
					)
				)
			);
			
			echo json_encode($res);
	
		}

		//采集总量
		public function collectionTotal(){
			$url=$this->host_old."api/outside/collection/total/0";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->data->qt->category);$i++){
				$qtClassNameArray[]=$result->data->qt->category[$i]->className;
				$qtProportionArray[]=str_replace("%",'',$result->data->qt->category[$i]->proportion);
			}

			for($i=0;$i<count($result->data->xmly->category);$i++){
				$xmlyClassNameArray[]=$result->data->xmly->category[$i]->className;
				$xmlyProportionArray[]=str_replace("%",'',$result->data->xmly->category[$i]->proportion);
			}


			
			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"categoryClassName"=>$qtClassNameArray,
						"categoryProportion"=>$qtProportionArray,
						"number"=>count($qtClassNameArray)
						//"total"=>$result->data->qt->total,
						//"proportion"=>$result->data->qt->proportion,
					),
					"xmly"=>array(
						"categoryClassName"=>$xmlyClassNameArray,
						"categoryProportion"=>$xmlyProportionArray,
						"number"=>count($xmlyClassNameArray)
						//"total"=>$result->data->xmly->total,
						//"proportion"=>$result->data->xmly->proportion,
					),
					"total"=>array($result->data->qt->total,$result->data->xmly->total)
				)
			);
			
			echo json_encode($res);
		}

		//排行榜
		public function rank(){
			$url=$this->host_old."api/outside/rankList/list";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->data->qt);$i++){
				$qtRankArray[]="蜻蜓FM蜻蜓FM_".$result->data->qt[$i]->title;
			}
			for($i=0;$i<count($result->data->xmly);$i++){
				$xmlyRankArray[]="喜马拉雅FM_".$result->data->xmly[$i]->title;
			}
			
			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"rank"=>$qtRankArray,
						"number"=>count($qtRankArray)
					),
					"xmly"=>array(
						"rank"=>$xmlyRankArray,
						"number"=>count($xmlyRankArray)
					)
				)
			);
			
			echo json_encode($res);
	
		}

		//*************************智能处理统计*****************


		//智能处理总量
		public function intelligentTotal(){
			$url=$this->host."api/processingStatistics/getProcessingResults";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
				"range"=>1,
				"processingType"=>0
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->data);$i++){
				if($result->data[$i]->source===0){
					$xmlyArray=array(
						"finishProportion"=>$result->data[$i]->treatedRatio,
						"total"=>$result->data[$i]->treatedAllNum,
						"finish"=>$result->data[$i]->treatedNum,
					);
					$xmlyTotal=$result->data[$i]->treatedAllNum;
				}elseif($result->data[$i]->source===1){
					$qtArray=array(
						"finishProportion"=>$result->data[$i]->treatedRatio,
						"total"=>$result->data[$i]->treatedAllNum,
						"finish"=>$result->data[$i]->treatedNum,
						//"totalProportion"=>$result->data->totalProportion
					);
					$qtTotal=$result->data[$i]->treatedAllNum;
				}
				
			}
			//dump($qtArray);
			$xmlyArray['totalProportion']=round($xmlyTotal/($xmlyTotal+$qtTotal)*100,2)."%";
			$qtArray['totalProportion']=round($qtTotal/($xmlyTotal+$qtTotal)*100,2)."%";
			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>$qtArray,
					"xmly"=>$xmlyArray
				)
			);
			
			echo json_encode($res);
	
		}

		//处理明细
		public function intelligentDetail(){
			$url=$this->host."api/processingStatistics/getProcessingResults";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
				"range"=>1,
				"processingType"=>0
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->qt->detailed);$i++){
				$qtDetailOrderArray[]=$result->qt->detailed[$i]->order;
				$qtDetailTotalArray[]=$result->qt->detailed[$i]->total;
			}

			for($i=0;$i<count($result->xmly->detailed);$i++){
				$xmlyDetailOrderArray[]=$result->xmly->detailed[$i]->order;
				$xmlyDetailTotalArray[]=$result->xmly->detailed[$i]->total;
			}

			for($i=0;$i<count($result->xmly->class);$i++){
				$xmlyClassProportionArray[]=$result->xmly->class[$i]->proportion;
				$xmlyClassTotalArray[]=$result->xmly->class[$i]->total;
				$xmlyClassTypeArray[]=$result->xmly->class[$i]->type;
			}

			for($i=0;$i<count($result->qt->class);$i++){
				$qtClassProportionArray[]=$result->qt->class[$i]->proportion;
				$qtClassTotalArray[]=$result->qt->class[$i]->total;
				$qtClassTypeArray[]=$result->qt->class[$i]->type;
			}

			
			for($i=0;$i<count($result->data);$i++){
				if($result->data[$i]->source===0){
					$xmlyArray=array(
						"detailedOrder"=>$result->data[$i]->treatedRatio,
						"detailedTotal"=>$result->data[$i]->treatedAllNum,
						"classProportion"=>$result->data[$i]->treatedNum,
					);
					$xmlyTotal=$result->data[$i]->treatedAllNum;
				}elseif($result->data[$i]->source===1){
					$qtArray=array(
						"finishProportion"=>$result->data[$i]->treatedRatio,
						"total"=>$result->data[$i]->treatedAllNum,
						"finish"=>$result->data[$i]->treatedNum,
						//"totalProportion"=>$result->data->totalProportion
					);
					$qtTotal=$result->data[$i]->treatedAllNum;
				}
				
			}

			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"detailedOrder"=>$qtDetailOrderArray,
						"detailedTotal"=>$qtDetailTotalArray,
						"classProportion"=>$qtClassProportionArray,
						"classTotal"=>$qtClassTotalArray,
						"classType"=>$qtClassTypeArray
					),
					"xmly"=>array(
						"detailedOrder"=>$xmlyDetailOrderArray,
						"detailedTotal"=>$xmlyDetailTotalArray,
						"classProportion"=>$xmlyClassProportionArray,
						"classTotal"=>$xmlyClassTotalArray,
						"classType"=>$xmlyClassTypeArray
					)
				)
			);
			
			echo json_encode($res);
			
		}

		//AI处理问题节目
		public function intelligentProblem(){
			$url=$this->host."api/outside/intelligentManage/problem/number/0";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
				"range"=>1,
				"processingType"=>0
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->data->qt->problem);$i++){
				$qtProblemTypeArray[]=$result->data->qt->problem[$i]->type;
				$qtProblemKeywordArray[]=$result->data->qt->problem[$i]->keyWord;
			}

			for($i=0;$i<count($result->data->xmly->problem);$i++){
				$xmlyProblemTypeArray[]=$result->data->xmly->problem[$i]->type;
				$xmlyProblemKeywordArray[]=$result->data->xmly->problem[$i]->keyWord;
			}

			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"total"=>$result->data->qt->total,
						"problemType"=>$qtProblemTypeArray,
						"problemKeyword"=>$qtProblemKeywordArray,
					),
					"xmly"=>array(
						"total"=>$result->data->xmly->total,
						"problemType"=>$xmlyProblemTypeArray,
						"problemKeyword"=>$xmlyProblemKeywordArray,
					),
					"total"=>array($result->data->qt->total,$result->data->xmly->total)
				)
			);
			
			echo json_encode($res);

		}


		//***************************人工处理统计************************
		//人工处理总量
		public function manualTotal(){
			$url=$this->host."api/outside/manualManage/total/0";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);
			
			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"checkTotal"=>$result->data->qt->checkTotal,
						"reportReport"=>$result->data->qt->reportReport,
					),
					"xmly"=>array(
						"checkTotal"=>$result->data->xmly->checkTotal,
						"reportReport"=>$result->data->xmly->reportReport,
					)
				)
			);
			
			echo json_encode($res);
		}

		//人工处理明细
		public function manualDetail(){
			$url=$this->host."api/outside/manualManage/number/0";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->data->xmly);$i++){
				$xmlyOrderArray[]=$result->data->xmly[$i]->order;
				$xmlyCheckArray[]=$result->data->xmly[$i]->check;
				$xmlyReportArray[]=$result->data->xmly[$i]->report;
			}

			for($i=0;$i<count($result->data->qt);$i++){
				$qtOrderArray[]=$result->data->qt[$i]->order;
				$qtCheckArray[]=$result->data->qt[$i]->check;
				$qtReportArray[]=$result->data->qt[$i]->report;
			}

			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"order"=>$qtOrderArray,
						"check"=>$qtCheckArray,
						"report"=>$qtReportArray,
					),
					"xmly"=>array(
						"order"=>$xmlyOrderArray,
						"check"=>$xmlyCheckArray,
						"report"=>$xmlyReportArray,
					)
				)
			);
			
			echo json_encode($res);

		}

		//人工处理问题节目
		public function manualProblem(){
			$url=$this->host."api/outside/problemForManual/number/0";
			$curlData=array(
				"accessId"=>$this->accessId,
				"timeStamp"=>$this->timeStamp,
				"sig"=>$this->sig,
			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			//dump($result);

			for($i=0;$i<count($result->data->qt->problem);$i++){
				$qtProblemTypeArray[]=$result->data->qt->problem[$i]->type;
				$qtProblemKeywordArray[]=$result->data->qt->problem[$i]->keyWord;
			}

			for($i=0;$i<count($result->data->xmly->problem);$i++){
				$xmlyProblemTypeArray[]=$result->data->xmly->problem[$i]->type;
				$xmlyProblemKeywordArray[]=$result->data->xmly->problem[$i]->keyWord;
			}

			$res=array(
				"code"=>$result->code,
				"message"=>$result->message,
				"data"=>array(
					"qt"=>array(
						"total"=>$result->data->qt->total,
						"problemType"=>$qtProblemTypeArray,
						"problemKeyword"=>$qtProblemKeywordArray,
					),
					"xmly"=>array(
						"total"=>$result->data->xmly->total,
						"problemType"=>$xmlyProblemTypeArray,
						"problemKeyword"=>$xmlyProblemKeywordArray,
					),
					"total"=>array($result->data->qt->total,$result->data->xmly->total)
				)
			);
			
			echo json_encode($res);
		}


		//广电任务对接
		public function tvTask(){
			//$url=$this->host."api/outside/tvTask/insert";
			//echo $url;
			$url="http://106.52.50.213:443/microserver-outside/api/tvTask/insert";
			$url="http://106.52.50.213:443/microserver-outside/api/tvTask/insert";
			$curlData=array(
				"accessId"=>"202010111515", //分配给外部系统的请求id（未定）
				"timeStamp"=>"1602397553401", //时间戳
				"sig"=>"123456",//密文（未定）
				"column"=>"东方卫视", //频道名称
				"program"=>"上海早晨", //节目名称
				"transferSpeed"=>1, //转写倍速
				"fileSuffix"=>"mp4", //文件后缀
				"tencentTimeStamp"=>"1602397553401", //腾讯接口的timeStamp
				"tencentToken"=>"123456",  //腾讯接口的token,
				"tencentUserId"=>"123456",//腾讯接口的userId,
				"tencentRequestId"=>"123456", //腾讯接口的requestId
				"tencentTaskId"=>"123456", //腾讯接口的taskId
				"level"=>5, //任务优先级
				"startTime"=>"2020-08-28 17:30:00", //节目开始时间

			);
	
			$result=$this->curlPost($url,json_encode($curlData));
			dump($result);
		}

		//保存任务
		public function saveTask($task){
			$Tasklist=M("tasklist");
			$res=$Tasklist->where("status=0")->find();
			if($res){
				return false;
			}else{
				$Tasklist->where("1=1")->delete();
				for($i=0;$i<count($task->data);$i++){
					$data['taskId']=$task->data[$i]->id;
					$data['describe']=$task->data[$i]->describe;
					$data['num']=$i;
					$data['status']=0;
					$Tasklist->add($data);
				}
			}
		}	

}