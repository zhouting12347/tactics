<?php
$con = mysql_connect("172.168.1.209","hotel","hotel");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("STRHOTEL", $con);

set_time_limit(0);
while(true){
	sleep(1);
	//��ѯһ��Ҫ���͵Ķ��� IsSend=0
	$res=mysql_query("SELECT * FROM U_Messages where IsSend=0 limit 1");
	if($res){
		while($row = mysql_fetch_array($res)){
			$msg=$row['Message'];
			$id=$row['Id'];
		}

	/*----------���Ͷ���-------------*/

		//���ŷ��ͳɹ� IsSend��Ϊ1
		if(1){
			$sql="UPDATE U_Messages SET IsSend=1 where Id=".$id;
			mysql_query($sql);
		}
	}
	sleep(1);
}
?>