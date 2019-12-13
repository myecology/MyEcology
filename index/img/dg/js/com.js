/**
 * Created by Administrator on 2019/3/20.
 */
$(window).scroll(function(){
    if($(window).scrollTop()>70){
        $(".header").css({
            backgroundColor:'rgba(0,0,0,.5)'
        })
    }else{
        $(".header").css({
            backgroundColor:'rgba(255,255,255,.1)'
        })
    }
});

if($(window).scrollTop()>70){
    $(".header").css({
        backgroundColor:'rgba(0,0,0,.5)'
    })
}else{
    $(".header").css({
        backgroundColor:'rgba(255,255,255,.1)'
    })
}

var topBanner = '<img src="img/bg_01.jpg" class="bgT" alt="">'
    +'<div class="topMain wp pa">'
    +'<div class="pa topN">'
    +'<p class="p1">抖观 - 会议不止于直播</p>'
    +'<p class="p2">打造会议爆款短视频</p>'
    +'<div class="addShou">立即联系</div>'
    +'</div>'
    +'<div class="y pa">'
    +'<div class="phone phoneRun"><img src="img/rightPhone.png" style="opacity:0;width:100%;"  alt=""></div>'
    //+'<img src="img/rightPhone.png" class="phone" onclick="ff()" alt="">'

    +'<img src="img/quan.png" class="quan" alt="">'
    +'</div>'
    +'</div>';

$(".top").append(topBanner);

var headerAdd = '<div class="hed wp">'
    +'<img src="img/logo.png" class="z" alt="">'
    +'<div class="y nav">'
    +'<a href="index.html" class="z a1">首页</a>'
    +'<a href="cpjs.html" class="z a2">产品介绍</a>'
    +'<a href="xxfw.html" class="z a2">线下服务</a>'
    +'<a href="case.html" class="z a2">精彩案例</a>'
    +'<a href="tc.html" class="z a2">套餐介绍</a>'
    +'</div>'
    +'</div>';
$(".header").append(headerAdd);

$('body').fadeIn(500);