// var tokenD = 'SZSphLKPhLPQKOpBkLU3FeN2ajn/tfI1ZeKULzbliQGXjbnVywEDRxkIDPNbULKYd/ohAUhMIxfg/Ap7nQR0ETuUUJ7qDyYaS84d05Io6hE=';
var mainUrl = 'http://api.mfcc_web.marsfarmer.group';
var licaiUrl = 'http://licai_master.marsfarmer.group';
var tokenD = '';

function GetQueryString(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}

tokenD = GetQueryString('Token');
if (tokenD){
    sessionStorage.setItem("token", tokenD);
}else{
    tokenD = sessionStorage.getItem("token");
}

