window.onload=function(){
    layui.laydate.render({
      elem:'#CreateTime'
      ,type: 'datetime'
    });
    var layedit = layui.layedit;
    
    //富文本图片上传
    layedit.set({
   	  uploadImage: {
   	    url: '/index.php/Upload/layedit_upload_handler' //接口url
   	    ,type: 'post' //默认post
   	   /* ,before: function(){
   	    	var loading = layer.load(0, {
                shade: false,
                time: 60*1000
            });
   	     }
   	    ,done: function(){
   	    	layer.close(loading);
   	     }*/
   	  }
   	});
    
    index=layedit.build('LAY_demo1', {
      height:500 //设置编辑器高度
    });
    
    //新建保存
    $("#form1").submit(function(){
    	var content=layedit.getContent(index);
    	if(content.length<1){
    		layer.msg("正文必须填写");
    		return false;
    	}
    	
    	//由于被屏蔽，需转成base64编码传数据
    	content=btoa(encodeURI(content));
    	
    	var data=$("#form1").serializeArray();
      	var arr={"name":"d_content","value":content};
      	data.push(arr);
    	 $.ajax({
 			url: '/index.php/Baoliao/baoliao_save_handler',
 			type: 'post',
 			dataType: 'json',
 			data:data,
 			success: function(msg){
 				if(msg.status==1){
 					//parent.parent.layer.closeAll();
 					parent.parent.layer.msg("保存成功");
 					parent.parent.layui.table.reload("documentTable");
 					//$("#save").css("display","none");
 				}else{
 					layer.msg("操作失败");
 				}
 			},
 			error:function(){
 				layer.msg("发送错误");
 			}
 		});
    	
    	return false;
    });
    
    //编辑保存
    $("#form2").submit(function(){
    	var content=layedit.getContent(index);
    	if(content.length<1){
    		layer.msg("正文必须填写");
    		return false;
    	}
    	
    	//由于被屏蔽，需转成base64编码传数据
    	content=btoa(encodeURI(content));
    	
    	var data=$("#form2").serializeArray();
      	var arr={"name":"d_content","value":content};
      	data.push(arr);
    	 $.ajax({
 			url: '/index.php/Baoliao/baoliao_edit_handler',
 			type: 'post',
 			dataType: 'json',
 			data:data,
 			success: function(msg){
 				if(msg.status==1){
 					layer.msg("操作成功");
 				}else{
 					layer.msg("操作失败");
 				}
 			},
 			error:function(){
 				layer.msg("发送错误");
 			}
 		});
    	
    	return false;
    });
    
	   //附件表格
	    var table=layui.table;
	    table.render({
	    elem: '#attachmentTable'
	    ,url:'/index.php/Table/getTableData'
	    //,even:true
	    ,id:'attachmentTable'
	    ,where:{tableName:"attachment",tableType:"attachment",d_id:d_id}
	    ,height:"full-250"
	    ,limit:5
	    ,limits:[5,10,20]
	    ,cols: [[
	       {type:'checkbox'}
	      ,{field:'a_filename', minWidth:120, title: '附件名称'}
	      ,{field:'a_format', width:120,title: '附件格式'}
	      ,{field:'a_size', width:120, title: '大小', sort: true}
	      ,{field:'action', title: '操作',width:120,align:'center',fixed: 'right',toolbar: '#myAttachmentTable'}
	    ]]
	  });
	    
	  //监听单元格事件
		  table.on('tool(myAttachmentTable)', function(obj){
			var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
		    var data = obj.data;
		    var savename=data.a_savename;
		    var filename=data.a_filename;
		 	var format=data.a_format;
		    if(layEvent === 'check'){
		    	 layer.open({
						type: 2,
						anim:2,
						shadeClose:true,
						title:filename,
						content: '/index.php/Document/document_attachment_show_layer?savename='+savename+"&format="+format,
						area: ['800px', '600px'],
					});
		    }
		  });
	  
	    //添加附件按钮
	  $("#addAttachment").click(function(){
			 layer.open({
					type: 2,
					anim:2,
					title:"添加附件",
					content: '/index.php/Document/document_add_attachment_layer?id='+d_id,
					area: ['800px', '320px'],
					end: function(){
						layui.table.reload("attachmentTable");
					}
				});
	  });
	    
	  //删除附件按钮
	  $("#delAttachment").click(function(){
		  var checkStatus=table.checkStatus('attachmentTable');
		  if(checkStatus.data.length<1){
	   		  layer.msg("至少选择一个附件");
	   		  return false;
	   	  }
		  layer.confirm('确认删除附件？', {icon: 3, title:'提示'}, function(index){
			  $.ajax({
		            async: false,
		            type: "POST",
		            url:'/index.php/Document/document_del_attachment_handler',
		            data:checkStatus,
		            dataType: "json",
		            success: function(msg){
		            		layer.msg(msg.info);
		            		if(msg.status==1){
		            			//刷新attachmentTable
		            			layui.table.reload("attachmentTable");
		            		}
		              },
		            error: function(){
		            	layer.msg("发生错误");
		            }
		        });
	 	 });
	 });
	  
	 //爆料查看层通过审核
	 $("#passBaoliao").click(function(){
		autoSaveDocument();
		 $.ajax({
			type: "POST",
			url:'/index.php/Baoliao/baoliao_pass_audit_handler?d_id='+d_id,
			dataType: "json",
			success: function(msg){
				layer.msg(msg.info);
				layui.table.reload("documentTable");
			},
			error: function(){
				layer.msg("发送错误");
			}
		});
	 });

	  //爆料提交审核按钮
	  $("#submitBaoliao").click(function(){
		autoSaveDocument();
		layer.confirm('确认提交？',{icon: 3, title:'提示'},function(index){
				$.ajax({
					url: '/index.php/Baoliao/baoliao_submit_handler?d_id='+d_id,
					type: 'get',
					dataType: 'json',
					success: function(msg){
						layer.msg("提交成功")
					},
					error:function(){
						layer.msg("发送错误");
					}
			});
		});
	  });
	  
	  
	//新建爆料提交操作
	  $("#createSubmit").click(function(){
		  //autoSaveDocument("new");
		  $.ajax({
	            async: false,
	            type: "POST",
	            url:'/index.php/baoliao/baoliao_checksave_handler?d_id='+d_id,
	            dataType: "json",
	            success: function(msg){
	            		if(msg.status==1){
							$.ajax({
								url: '/index.php/Baoliao/baoliao_submit_handler?d_id='+d_id,
								type: 'get',
								dataType: 'json',
								success: function(msg){
									layer.msg("提交成功")
								},
								error:function(){
									layer.msg("发送错误");
								}
						   });
	            		}else{
	            			layer.msg("稿件未保存！");
	            		}
	              },
	            error: function(){
	            	layer.msg("发生错误");
	            }
	        });
	  });
	  
	//稿件5分钟自动保存
	window.setInterval("intervalSaveDocument()",300000);
	  
	//编辑时自动保存稿件
	  function autoSaveDocument(){
	  	var content=layedit.getContent(index);
	  	if(content.length<1){
	  		//layer.msg("正文必须填写");
	  		return false;
	  	}
	  	
	    //由于被屏蔽，需转成base64编码传数据
    	content=btoa(encodeURI(content));
		var data=$("#form2").serializeArray();
	    var arr={"name":"d_content","value":content};
	    data.push(arr);
	    $.ajax({
			url: '/index.php/Baoliao/baoliao_edit_handler',
			type: 'post',
			dataType: 'json',
			data:data,
			success: function(msg){
				/*if(msg.status==1){
					layer.msg("操作成功");
				}else{
					layer.msg("操作失败");
				}*/
			},
			error:function(){
				//layer.msg("发送错误");
			}
		});
	   
	  }
	  
	
  }

//定时自动保存稿件
function intervalSaveDocument(){
	var layedit = layui.layedit;
 	var content=layedit.getContent(index);
 	if(content.length<1){
 		return false;
 	}
 	
 	var data=$("#form2").serializeArray();
   	var arr={"name":"d_content","value":content};
   	data.push(arr);
 	 $.ajax({
 			url: '/index.php/Document/document_edit_handler',
 			type: 'post',
 			dataType: 'json',
 			data:data,
 			success: function(msg){
 				if(msg.status==1){
 					layer.msg("自动保存成功");
 				}
 			},
 			error:function(){
 			}
 		});
 }
