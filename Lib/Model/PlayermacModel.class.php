<?php
class playermacModel extends Model {
	protected $_validate         =         array(
			array('PM_PID','require','ID can not be empty'), //默认情况下用正则进行验证
	        array('PM_Mac','require','Mac can not be empty'),
			array('PM_Mac','','Repetition of mac',0,'unique',1), //默认情况下用正则进行验证
	        array('PM_PID','','Repetition of ID',0,'unique',1), 
			//array('M_Password','require','密码必须填写！'),
			//array('name','','帐号名称已经存在！',0,’unique’,1), // 在新增的时候验证name字段是否唯一
	);
}