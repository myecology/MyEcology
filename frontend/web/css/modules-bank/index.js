$(function () {
    var psw = "", counts = 0;
    $(".subBtn").on("click", function () {
        $(".keywords").show();
        $(".shade").show();

    });
    //关闭键盘
    $(".close").on("click", function () {
        $(".keywords").hide();
        $(".shade").hide();
        $(".inpPsw span b").css("display", "none");
        $(".pswInp").val("");
        psw = "";
        counts = 0;
    })
    //删除密码
    $('.delete').on('touchstart', function (e) {
        $(this).addClass("on")
        counts--;
        psw = psw.substring(0, psw.length - 1)
        $(".pswInp").val(psw * 1);
        $(".inpPsw span").eq(counts).find("b").css("display", "none");
    });
    $('.delete').on('touchend', function (e) {
        $(this).removeClass("on")
    });
    //输入密码
    $('.num').on('touchstart', function (e) {
        $(this).addClass("on")
        var num = $(this).attr("num");
        if (counts <= 5) {
            counts++;
            for (var i = 0; i <= counts - 1; i++) {
                $(".inpPsw span").eq(i).find("b").css("display", "block");
            }
            psw += num;
        }
    });
    $('.num').on('touchend', function (e) {
        $(this).removeClass("on")
        $(".pswInp").val(psw * 1);
        //输入6位密码发送请求
        if (counts == 6) {
            console.log($(".pswInp").val());
            //输入密码后请求接口 遮罩层加载
            var index = layer.load(1, {
                shade: [0.5, '#000']
            });
            //关闭加载层
            layer.close(index)
            //				if(){
            //密码错误
            layer.msg('<i class="iconfont" style="font-size:0.36rem;">&#xe68d; &nbsp;</i>密码错误，请重新输入');
            //				}else{
            //密码正确显示加载层 申请中、提现成功、提现失败、处理中
            //					var cash_apply = '<div class="applying"></div><p>申请中</p>';
            //					var cash_ok = '<div class="cash_ok"></div><p>提现成功</p>';
            //					var cash_no = '<div class="cash_no"></div><p>提现失败</p>';
            //					var cash_load = '<div class="cash_loading"></div><p>处理中</p>';
            //					//交易密码自定义键盘重置、隐藏
            //					psw="";
            //					counts = 0;
            //					$(".keywords").hide().find(".pswInp").val("");
            //					$(".keywords .inpPsw").find("b").hide();
            //					//显示加载 添加到页面上
            //					$(".loading").show().html(cash_ok);
            //				}
        }
    });
});