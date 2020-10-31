<?php

//计算入流法院，出流法院的审核法院
//入流法院id 出流法院id 当前（申请或审核）法院id
function submitCourt($courtInId,$courtOutId,$submitCourtId){
	//查出入流法院所有父法院
	$courtInParents=getAllParentCourts($courtInId);
	//查询出流法院所有父法院
	$courtOutParents=getAllParentCourts($courtOutId);
	
	//如果申请或审核法院在2个集合内，则该法院就事最终审核
	if(in_array($submitCourtId,$courtInParents)&&in_array($submitCourtId,$courtOutParents)){
		return $submitCourtId;
	//如果不在，取他的父法院
	}else{
		$Court=M('court');
		$res=$Court->where("C_ID=$submitCourtId")->find();
		return $res[C_ManageBy];
	}
}

//查询所有法院的父法院
//返回父法院array
function getAllParentCourts($courtId){
	$x=1;
	$Court=M('court');
	$courts=array();
	$courts[]=$courtId;
	while($x>0){
		$res=$Court->where("C_ID=$courtId")->find();
		$courts[]=$res[C_ManageBy];
		$courtId=$res[C_ManageBy];
		$x=$res[C_ID];
	}
	return $courts;
}

//保存日志
//
function saveLog($type,$P_ID){
		$data[L_Type]=$type;
		$data[L_P_ID]=$P_ID;//任务ID
		$data[L_DateTime]=date("Y-m-d H:i:s",time());//时间
		$data[L_Man]=$_SESSION[M_ID];//操作人
		//根据PID查询表单内容
		$Playlist=M('playlist');
		$playlist=$Playlist->where("P_ID=$P_ID")->find();
		if($playlist){
			$data[L_Detail]=json_encode($playlist);
			$Log=M('log');
			$Log->add($data);
		}else{
			$data[L_Detail]=$P_ID;
			$Log=M('log');
			$Log->add($data);
		}
	}


/**
  +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
  +----------------------------------------------------------
 * @static
 * @access public
  +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
  +----------------------------------------------------------
 * @return string
  +----------------------------------------------------------
 */
function musubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if (function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix)
        return $slice . "…";
    return $slice;
}

//根据秒 格式化时间
function formatTime($seconds){
	$hour=round($seconds/3600,0);
	$min=round(($seconds-$hour*3600)/60,0);
	$sec=$seconds-$hour*3600-$min*60;
	if($hour>0){
		return $hour."小时".$min."分".$sec."秒";
	}else{
		return $min."分".$sec."秒";
	}
}

//页面得到不同语言字段值
function getField($id, $field, $lang, $length) {
    $arrfield = getFieldStr($lang);
    $objCate = M("Detail");
    $arrCate = $objCate->find($id);
    $title = $arrCate[$arrfield[$field]];
    $title = musubstr($title, 0, $length);
    return $title;
}

function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty($time)||($time=='-1')) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date($format, $time);
}

/**
 * 短信发送
 */
function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
}

//文件夹判断是否为空
function my_judge_empty_dir($module)  
{  
	//截取'Common'的位置
	$tpl_pos=strrpos(__FILE__,"Common");
	//得到模块对应模版的路径地址
	$dir=substr(__FILE__,0,$tpl_pos)."Tpl\default\\".$module."\\".MODULE_NAME;
    $handle = opendir($dir);  
    while (($file = readdir($handle)) !== false)  
    {  
        if ($file != "." && $file != "..")  
        {  
            closedir($handle);  
            return false;  
        }  
    }  
    closedir($handle);
    return true;  
}   

//生成短信验证码随机数
function random_verify($number){
	$randStr = str_shuffle('1234567890');
	$rand = substr($randStr,0,$number);
	return $rand;
}

//发送短信验证码
function sendVerify(){
	$User=M('User');
	//查询上一次发送验证码的时间，计算和现在请求发送短信验证码的时间差
	$lastSendTime=$User->where("MACAddress='".$_SESSION['mac']."'")->field('SendVerifyTime,SendPhone')->find();
	$_SESSION['interval']=time()-intval($lastSendTime['SendVerifyTime']);
	if(($lastSendTime['SendVerifyTime']!='')&&($_SESSION['interval']<60)&&($_SESSION['tempPhone']==$lastSendTime['SendPhone'])){
		return false;
	}else{
		//产生随机4位的验证码
		$_SESSION["verifyNumber"]=random_verify(4);
		//保存验证短信发送时间戳,60秒内不允许重复
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField(array('SendVerifyTime','SendPhone'),array(time(),$_SESSION['tempPhone']));
		$_SESSION['interval']=0;
		//手机发送短信
	}
}

//保存电话号码
function savePhone($phone,$changeNum){
	$User=M('User');
	//第一个电话号码字段为空时
	if(!$_SESSION['phone1']){
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo1',$phone);
	}
	//第二个电话号码字段为空时
	else if(!$_SESSION['phone2']){
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo2',$phone);
	}
	//第三个电话号码字段为空时
	else if(!$_SESSION['phone3']){
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo3',$phone);
	}
	//如果保存电话的三个字段都满了，替换修改的那个号码
	else{
		$User->where("MACAddress='".$_SESSION['mac']."'")->setField('PhoneNo'.$changeNum,$phone);
	}
}

//插入一段字符串
function str_insert($str, $i, $substr)
{
	for($j=0; $j<$i; $j++){
		$startstr .= $str[$j];
	}
	for ($j=$i; $j<strlen($str); $j++){
		$laststr .= $str[$j];
	}
	$str = ($startstr . $substr . $laststr);
	return $str;
}


//二维码生成
function QRcode(){
	import('@.ORG.QRcode');
	import('@.ORG.Image');
	$data="http://www.163.com";
	$outfile='./Public/images/QRcode/text.png';
	$water='./Public/images/sitv_water.png';
	// L水平 7%的字码可被修正【官方推荐】
	// M水平 15%的字码可被修正
	// Q水平 25%的字码可被修正
	// H水平 30%的字码可被修正
	$level='M';
	$size=9;
	$margin=1;
	QRcode::png($data, $outfile, $level, $size,$margin);
	 
	$Image = new Image();
	//给图片添加logo水印
	$Image->water($outfile,$water);
}

/**对excel里的日期进行格式转化*/
function GetData($val){
	$jd = GregorianToJD(1, 1, 1970);
	$gregorian = JDToGregorian($jd+intval($val)-25569);
	return $gregorian;/**显示格式为 “月/日/年” */
}

/**
 +----------------------------------------------------------
 * Import Excel | 2013.08.23
 * Author:HongPing <hongping626@qq.com>
 +----------------------------------------------------------
 * @param  $file   upload file $_FILES  文件相对路径
 *                 $column 读取xml表格的列数
 +----------------------------------------------------------
 * @return array   array("error","message")
 +----------------------------------------------------------
 */
function importExcel($file,$Column=17){
	if(!file_exists($file)){
		return array("error"=>0,'message'=>'file not found!');
	}
	require_once './Common/Classes/PHPExcel.php';
	$objReader = new PHPExcel_Reader_Excel2007();
	try{
		$PHPReader = $objReader->load($file);
	}catch(Exception $e){
	}
	if(!isset($PHPReader)) return array("error"=>0,'message'=>'read error!');
	$allWorksheets = $PHPReader->getAllSheets();
	$i = 0;
	foreach($allWorksheets as $objWorksheet){
		$sheetname=$objWorksheet->getTitle();
		$allRow = $objWorksheet->getHighestRow();//how many rows
		$highestColumn = $objWorksheet->getHighestColumn();//how many columns
		$allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$allColumn=$Column;
		$array[$i]["Title"] = $sheetname;
		$array[$i]["Cols"] = $allColumn;
		$array[$i]["Rows"] = $allRow;
		$arr = array();
		$isMergeCell = array();
		foreach ($objWorksheet->getMergeCells() as $cells) {//merge cells
			foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
				$isMergeCell[$cellReference] = true;
			}
		}
		for($currentRow = 1 ;$currentRow<=$allRow;$currentRow++){
			$row = array();
			for($currentColumn=0;$currentColumn<$allColumn;$currentColumn++){
				;
				$cell =$objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
				$afCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn+1);
				$bfCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn-1);
				$col = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
				$address = $col.$currentRow;
				$value = $objWorksheet->getCell($address)->getValue();
				if(substr($value,0,1)=='='){
					return array("error"=>0,'message'=>'can not use the formula!');
					exit;
				}
			 	if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
					$cellstyleformat=$cell->getStyle( $cell->getCoordinate() )->getNumberFormat();
					$formatcode=$cellstyleformat->getFormatCode();
					if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[dy]/i', $formatcode)) {
						$value=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
					}else{
						$value=PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
					}
				} 
				if($isMergeCell[$col.$currentRow]&&$isMergeCell[$afCol.$currentRow]&&!empty($value)){
					$temp = $value;
				}elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$col.($currentRow-1)]&&empty($value)){
					$value=$arr[$currentRow-1][$currentColumn];
				}elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$bfCol.$currentRow]&&empty($value)){
					$value=$temp;
				}
				$row[$currentColumn] = $value;
			}
			$arr[$currentRow] = $row;
		}
		$array[$i]["Content"] = $arr;
		$i++;
	}
	spl_autoload_register(array('Think','autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
	unset($objWorksheet);
	unset($PHPReader);
	unset($PHPExcel);
	//unlink($file);
	return array("error"=>1,"data"=>$array);
}

//
function getXmlToArray($file){
	$res=importExcel($file);
	//删除表格中的空行
	$arr_excel=array();
	foreach($res['data'][0]['Content'] as $key=>$val){
		$res=array_filter($val);//去除数组中的空字符元素
		if($res){
			$arr_excel[$key]=$val;
		}
	}
	return $arr_excel;
}

//随机字符串
function GetRandStr($length){
	$str='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$len=strlen($str)-1;
	$randstr='';
	for($i=0;$i<$length;$i++){
		$num=mt_rand(0,$len);
		$randstr .= $str[$num];
	}
	return $randstr;
}
?>
