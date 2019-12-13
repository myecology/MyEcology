/**
 * Created by Administrator on 2019/2/25.
 */

var isErrorHintShow = false;


function errorHintFadeIn(text,time){
    if (!isErrorHintShow){
        isErrorHintShow = true;
        $(".errorHint").text(text);
        $(".errorHint").fadeIn(500);
        setTimeout(function(){
            $(".errorHint").fadeOut(500);
            isErrorHintShow = false;
        },time);
    }
}


function getQueryString(name)
{
    var reg = new RegExp("(^|\\?|&)"+ name +"=([^&]*)(\\s|&|$)", "i");
    if (reg.test(window.location.href)) {
        var pm = RegExp.$2.replace(/\+/g, " ");
        return pm;
    }
    return "";
}


function GetQueryString2(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
    var context = "";
    if (r != null)
        context = r[2];
    reg = null;
    r = null;
    return context == null || context == "" || context == "undefined" ? "" : context;
}

var str ='<div class="errorHint"></div>';
$('body').append(str);
var getToken = sessionStorage.getItem("token");
var tokenT = GetQueryString2('token');
var urlToken = '';
if (getToken){
    if (getToken != tokenT&&tokenT){
        urlToken = tokenT;
        sessionStorage.setItem("token",tokenT);
    }else{
        urlToken = getToken;
    }
}else{
    urlToken = tokenT;
    sessionStorage.setItem("token",tokenT);
}
//alert(tokenT)
var website = 'http://api.antc.bxguo.net/';
console.log(tokenT)

$.ajaxSetup({
    headers: {
        //"Authorization":'Bearer JvozRC/M3FeoZudbb82/H66iwbvOlxOiTBSet4Bzd2GEuZQJjwRrzmxcl5XkT3BKG8AiIx49VrMNzPijoPhkxxdt85U2oNRd/3Ta+5kHAQQ='
        //"Authorization": 'Bearer /c/E2/wW0oLwNGHzbUm2TK6iwbvOlxOiTBSet4Bzd2GEuZQJjwRrzqhuEwzypHByS8XhNoScU8aC3Pbt83Ipgz4suvcbIZCxk1MvieIJzUs='
        "Authorization": 'Bearer '+urlToken
    }
});