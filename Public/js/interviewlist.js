window.onload=function(){
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
 			url: '/index.php/Document/document_save_handler',
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
 			url: '/index.php/Document/document_edit_handler',
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
	  
	  //媒体库稿件库提交审核按钮
	  $("#submitDocument").click(function(){
		  autoSaveDocument();
		  layer.open({
	          title:'审核人选择'
	          ,type: 2
	          ,content: '/index.php/Document/document_choose_audit_person_layer?d_id='+d_id
	          ,shadeClose: false
	          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '400px']
	          ,cancel: function(index, layero){ 
	        	  layui.table.reload("documentTable");
	        	}    
	        });
	  });
	  
	  //媒体部通过审核，带选择领导
	  $("#submitMedia").click(function(){
		  autoSaveDocument();
		  layer.open({
	          title:'审核人选择'
	          ,type: 2
	          ,content: '/index.php/MediaAudit/media_audit_choose_audit_person_layer?d_id='+d_id
	          ,shadeClose: false
	          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '400px']
	          ,cancel: function(index, layero){ 
	        	  layui.table.reload("documentTable");
	        	}    
	        });
	  });
	  
	  //版权通过审核
	  $("#submitCopyright").click(function(){
/*		  layer.confirm('确认通过审核？',{icon: 3, title:'提示'},function(index){
			  autoSaveDocument();
			  $.ajax({
		            type: "POST",
		            url:'/index.php/CopyrightAudit/copyright_audit_submit_handler?d_id='+d_id,
		            dataType: "json",
		            success: function(msg){
		            	parent.layer.closeAll();
		            	layer.msg(msg.info);
		            	layui.table.reload("documentTable");
		            },
		            error: function(){
		            	layer.msg("发送错误");
		            }
		        });
			  layer.close(index);
			});*/
		  autoSaveDocument();
		  if(documentType=='tongfa'){
			layer.open({
				title:'审核人选择'
				,type: 2
				,content: '/index.php/MediaAudit/media_audit_choose_audit_person_layer?d_id='+d_id
				,shadeClose: false
				,area:screen() < 2 ? ['60%', '60%'] : ['600px', '400px']
				,cancel: function(index, layero){ 
					layui.table.reload("documentTable");
				  }    
			  });
		  }else{
			layer.open({
				title:'通过意见'
				,type: 2
				,content: '/index.php/CopyrightAudit/copyright_audit_choose_audit_person_layer?d_id='+d_id
				,shadeClose: false
				,area:screen() < 2 ? ['60%', '60%'] : ['600px', '350px']
				,cancel: function(index, layero){ 
					layui.table.reload("documentTable");
					layer.close(index);
					}    
				});
			}
	  });


	  //编辑配图提交到版权
	  $("#submitEdit").click(function(){
		autoSaveDocument();
		layer.open({
			title:'通过意见'
			,type: 2
			,content: '/index.php/Document/document_choose_audit_person_layer?d_id='+d_id+'&toCopyright='+toCopyright
			,area:screen() < 2 ? ['60%', '60%'] : ['600px', '350px']
			,cancel: function(index, layero){ 
				layui.table.reload("documentTable");
				layer.close(index);
			  }    
		  });
	  });

	  //编辑配图退回稿件
	  $("#failedEdit").click(function(){
		layer.open({
			title:'未通过理由'
			,type: 2
			,content: '/index.php/Document/document_audit_failed_reason_layer?d_id='+d_id
			,shadeClose: false
			,area:screen() < 2 ? ['60%', '60%'] : ['600px', '250px']
			,cancel: function(index, layero){ 
				layui.table.reload("documentTable");
			  }    
		  });
	  });

	  
	  //领导通过审核
	  $("#submitLeader").click(function(){
/*		  layer.confirm('确认通过审核？',{icon: 3, title:'提示'},function(index){
			  autoSaveDocument();
			  $.ajax({
		            type: "POST",
		            url:'/index.php/LeaderAudit/leader_audit_submit_handler?d_id='+d_id,
		            dataType: "json",
		            success: function(msg){
		            	parent.layer.closeAll();
		            	layer.msg(msg.info);
		            	layui.table.reload("documentTable");
		            },
		            error: function(){
		            	layer.msg("发送错误");
		            }
		        });
			  layer.close(index);
			});*/
		  autoSaveDocument();
		  layer.open({
	          title:'通过意见'
	          ,type: 2
	          ,content: '/index.php/LeaderAudit/leader_audit_passed_reason_layer?d_id='+d_id
	          ,shadeClose: false
	          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '250px']
	          ,maxmin: true  
	          ,cancel: function(index, layero){ 
	        	  layui.table.reload("documentTable");
	        	  layer.close(index);
	        	}    
	        });
	  });
	  
	  //退回媒体部
	  $("#failedMedia").click(function(){
		  layer.open({
	          title:'未通过理由'
	          ,type: 2
	          ,content: '/index.php/MediaAudit/media_audit_failed_reason_layer?d_id='+d_id
	          ,shadeClose: false
	          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '250px']
	          ,cancel: function(index, layero){ 
	        	  layui.table.reload("documentTable");
	        	}    
	        });
	  });
	  
	  //退回版权
	  $("#failedCopyright").click(function(){
		  layer.open({
	          title:'未通过理由'
	          ,type: 2
	          ,content: '/index.php/CopyrightAudit/copyright_audit_failed_reason_layer?d_id='+d_id
	          ,shadeClose: false
	          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '250px']
	          ,cancel: function(index, layero){ 
	        	  layui.table.reload("documentTable");
	        	}    
	        });
	  });
	  
	  //退回领导
	  $("#failedLeader").click(function(){
		  layer.open({
	          title:'未通过理由'
	          ,type: 2
	          ,content: '/index.php/LeaderAudit/leader_audit_failed_reason_layer?d_id='+d_id
	          ,shadeClose: false
	          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '250px']
	          ,cancel: function(index, layero){ 
	        	  layui.table.reload("documentTable");
	        	}    
	        });
	  });
	  
	//新建稿件提交操作
	  $("#createSubmit").click(function(){
		  autoSaveDocument("new");
		  $.ajax({
	            async: false,
	            type: "POST",
	            url:'/index.php/Document/document_checksave_handler?d_id='+d_id,
	            dataType: "json",
	            success: function(msg){
	            		if(msg.status==1){
	            			 layer.open({
	            		          title:'审核人选择'
	            		          ,type: 2
	            		          ,content: '/index.php/Document/document_choose_audit_person_layer?d_id='+d_id
	            		          ,shadeClose: false
	            		          ,area:screen() < 2 ? ['60%', '60%'] : ['600px', '400px']
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
	  function autoSaveDocument(type){
	  	var content=layedit.getContent(index);
	  	if(content.length<1){
	  		//layer.msg("正文必须填写");
	  		return false;
	  	}
	  	
	    //由于被屏蔽，需转成base64编码传数据
    	content=btoa(encodeURI(content));
	  	
	  	if(type=="new"){
	  		var data=$("#form1").serializeArray();
	  	}else{
	  		var data=$("#form2").serializeArray();
	  	}
	    var arr={"name":"d_content","value":content};
	    data.push(arr);
	    
	    if(type=="new"){
	    	$.ajax({
	 			url: '/index.php/Document/document_save_handler',
	 			type: 'post',
	 			dataType: 'json',
	 			data:data,
	 			success: function(msg){
	 				if(msg.status==1){
	 					//parent.parent.layer.closeAll();
	 					//parent.parent.layer.msg("保存成功");
	 					//parent.parent.layui.table.reload("documentTable");
	 					//$("#save").css("display","none");
	 				}else{
	 					//layer.msg("操作失败");
	 				}
	 			},
	 			error:function(){
	 				//layer.msg("发送错误");
	 			}
	    	});
	    }else{
	  	 $.ajax({
	  			url: '/index.php/Document/document_edit_handler',
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
