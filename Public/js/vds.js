function screen() {
    //获取当前窗口的宽度
    var width = $(window).width();
    if (width > 1200) {
        return 3;   //大屏幕
    } else if (width > 992) {
        return 2;   //中屏幕
    } else if (width > 768) {
        return 1;   //小屏幕
    } else {
        return 0;   //超小屏幕
    }
}

//视频播放
function videoLayer(e){
	layer.open({
        title:'Video'
        ,type: 2
        ,content: '/index.php/Clips/clips_video_layer?id='+e.id
        ,shadeClose: true
        ,area:screen() < 2 ? ['60%', '60%'] : ['960px', '540px']
        ,maxmin: true
      });
}