<?php
class UploadAction extends Action{
    /**
    +--------------------------------
    * 富文本图片上传操作
    +--------------------------------
    * @date: 2019年4月19日 上午11:54:02
    * @author: zt
    * @param: variable
    * @return:
    */
    public function layedit_upload_handler(){
        import('ORG.Net.UploadFile');
        //dump($_FILES);exit();
        //$originName=$_FILES['file']['name']; 单文件上传获取原始文件名
        $originName=$_FILES['file']['name'][0];//多文件上传会多次调用layedit_upload_handler函数，原始文件名
        $name_array=explode('.',$originName);
        $count=count($name_array);
        $format=$name_array[$count-1];
        $size=$_FILES['file']['size'];//原始文件大小 */
        $upload = new UploadFile();// 实例化上传类
        $upload->savePath = C('IMAGE_UPLOAD_PATH'); // 设置附件上传目录
        $ID=time().mt_rand(10000,99999);
        $filename=$ID.".".$format;//文件名
        $upload->saveRule=$ID;
        $upload->uploadReplace=true;
        if(!$upload->upload()) {
            // 上传错误提示错误信息
            $result=array(
                'code'=>1,
                'msg'=>$upload->getErrorMsg()
            );
            echo json_encode($result);
        }else{
            $result=array(
                'code'=>0,
                'msg'=>'success',
                'data'=>array('src'=>C('IMAGE_URL').$filename,'title'=>'')
            );
            echo json_encode($result);
        }
    }
    
}