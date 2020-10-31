<?php
class cliptypeModel extends Model {
	protected $_validate         =         array(
			array('CT_Name','require','Name can not be empty'), //默认情况下用正则进行验证
			//array('CT_Name','','Repetition of names',0,'unique',1), //默认情况下用正则进行验证
			//array('M_Password','require','密码必须填写！'),
			//array('name','','帐号名称已经存在！',0,’unique’,1), // 在新增的时候验证name字段是否唯一
	);
}