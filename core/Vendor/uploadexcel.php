
<?php
class uploadExcel
{
public $maxSize;//�ļ���С
public $uploadTpye;//�ļ�����
public $uploadPath;//�ϴ���·��
public $file;
	function __construct()
	{
	$this -> maxSize = MAXFILESIZE;
	$this -> uploadTpye = ALLOWEXTENSION;
	$this -> uploadPath = UPLOADPATH;
	}
	
	function upload($file="file")
	{
	   
		if ($_FILES[$file]['tmp_name'] != "")
			{
			
				//1 ����ļ��Ĵ�С�������� ΪʲôҪ����һ��is_uploaded_file�����أ���������
				if ($_FILES[$file]['size'] > $this -> maxSize)
				{
					die("�ļ���С�������ƣ����ֻ���ϴ�500K���ļ�");
				}
					################################################################
				if (is_uploaded_file($_FILES[$file]['tmp_name']))
				{
				
					$allow = explode(",",$this -> uploadTpye);
					if (!in_array($_FILES[$file]['type'],$allow))
						die("�ļ������ѱ���ֹ��");
					$ext = explode(".",$_FILES[$file]['name']);
					$ext = $ext[count($ext)-1];
					do
					{
						$fileName = time().(microtime() * pow(10,6)).mt_rand(1000,9999).".".$ext;
					}
					while (file_exists(UPEXCEL.$fileName));

					if (move_uploaded_file($_FILES[$file]['tmp_name'],UPEXCEL.$fileName))
					{
						return $fileName;
					}
						
				}
				
		}
	}
}
?>