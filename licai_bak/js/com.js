// var tokenD = 'SZSphLKPhLPQKOpBkLU3FeN2ajn/tfI1ZeKULzbliQGXjbnVywEDRxkIDPNbULKYd/ohAUhMIxfg/Ap7nQR0ETuUUJ7qDyYaS84d05Io6hE=';
var mainUrl = 'http://api.mfcc_web.marsfarmer.group';
var licaiUrl = 'http://licai_master.marsfarmer.group';

function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}
var tokenD = '';
tokenD = GetQueryString('token');
if (tokenD){
    sessionStorage.setItem("token", tokenD);
}else{
    tokenD = sessionStorage.getItem("token");
}

var addErrorHint = '<div class="errorHint"></div>';
$('body').append(addErrorHint);

if (tokenD == ''||tokenD == null){
    $('body').children().hide();
    errorShow('请重新进入页面',99999999);
}


function errorShow(data,times){
    $(".errorHint").hide().text('');
    $(".errorHint").text(data).fadeIn(500);
    setTimeout(function(){
        $(".errorHint").fadeOut(500);
    },times);
}


