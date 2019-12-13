/**
 * Created by Administrator on 2019/2/25.
 */

//var isErrorHintShow = false;
//
//
//function errorHintFadeIn(text,time){
//    if (!isErrorHintShow){
//        isErrorHintShow = true;
//        $(".errorHint").text(text);
//        $(".errorHint").fadeIn(500);
//        setTimeout(function(){
//            $(".errorHint").fadeOut(500);
//            isErrorHintShow = false;
//        },time);
//    }
//}
//
//var str ='<div class="errorHint"></div>';
//$('body').append(str);

var footerStr = '<a href="index.html" class="z">'
    +'<img src="img/footerIcon1_1.png" class="f_1"  alt="">'
    +'<img src="img/footerIcon1_2.png" class="f_2"  alt="">'
    +'<p>首页</p>'
    +'</a>'
    +'<a href="quotation.html" class="z">'
    +'<img src="img/footerIcon2_1.png" class="f_1"  alt="">'
    +'<img src="img/footerIcon2_2.png" class="f_2"  alt="">'
    +'<p>行情</p>'
    +'</a>'
    +'<a href="index.html" class="z">'
    +'<img src="img/footerIcon3_1.png" class="f_1"  alt="">'
    +'<img src="img/footerIcon3_2.png" class="f_2"  alt="">'
    +'<p>交易</p>'
    +'</a>'
    +'<a href="index.html" class="z">'
    +'<img src="img/footerIcon4_1.png" class="f_1"  alt="">'
    +'<img src="img/footerIcon4_2.png" class="f_2"  alt="">'
    +'<p>资产</p>'
    +'</a>';

$(".footer").append(footerStr);
/*是否登录*/
function isOnline(){
    window.location.href ='login.html';
}

//底部菜单亮起

function footerShow(e){
    $(".footer a").eq(e).addClass('nowPage');
}