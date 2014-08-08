<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>统一账户修改密码</title>
    <script type="text/javascript" charset="utf-8" src="http://sta.273.cn/cgi/ganji_sta.php?file=ganji,js/util/jquery/jquery-1.7.2.js,app/backend/css/bootstrap.css"></script>
</head>
<body>
    <form method="post" action="/account/changePwd/">
        <div class="pull-left" style="margin: 220px; color: red;"></div>
        <table class="table table-bordered" style="width: 400px; margin: 10px;">
            <tr>
                <td><h3>273统一账户修改密码</h3></td>
            </tr>
            <tr>
                <td>
                    <input type="password" class="pull-left" name="oldPasswd" placeholder="旧密码" />
                    <div name="oldPasswd" class="pull-left" style="margin: 5px; color: red;"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="password" class="pull-left" name="newPasswd" placeholder="新密码" />
                    <div name="newPasswd" class="pull-left" style="margin: 5px; color: red;"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="password" class="pull-left" name="notPasswd" placeholder="请再一次输入新密码" />
                    <div name="notPasswd" class="pull-left" style="margin: 5px; color: red;"></div>
                </td>
            </tr>
            <tr>
                <td>
                <input type="submit" class="btn btn-primary pull-left" name="submit" value="修改" />
                <div name="result" class="pull-left" style="margin: 5px; color: red;"><?php echo $this->resultMsg; ?></div></td>
            </tr>
        </table>
    </form>
</body>
</html>
<script>
    GJ.use("jquery", function() {
        var input_oldPasswd = $("input[name=oldPasswd]");
        var input_newPassword = $("input[name=newPasswd]");
        var input_notPpassword = $("input[name=notPasswd]");
        var div_oldPasswd = $("div[name=oldPasswd]");
        var div_newPassword = $("div[name=newPasswd]");
        var div_notPpassword = $("div[name=notPasswd]");
        var div_result = $("div[name=result]");
        var btn_submit = $("input[name=submit]");

        btn_submit.click(function() {
            var checkSubmit = true;
            div_oldPasswd.html("");
            div_newPassword.html("");
            div_notPpassword.html("");
            div_result.html("");
            if ($.trim(input_oldPasswd.val()) == "") {
                div_oldPasswd.html("旧密码不能为空");
                checkSubmit = false;
            }
            if (!checkStrong(input_newPassword.val())) {
                div_newPassword.html("密码必须包含数字和字母");
                checkSubmit = false;
            }
            if (input_newPassword.val().length < 8 || input_newPassword.val().length > 16) {
                div_newPassword.html("新密码要在8～16位之间");
                checkSubmit = false;
            }
            var re = new RegExp(/^[^\s'　']*$/);
            if (!re.test(input_newPassword.val())) {
                div_newPassword.html("新密码不能包含空格");
                checkSubmit = false;
            }
            if ($.trim(input_newPassword.val()) == "") {
                div_newPassword.html("新密码不能为空");
                checkSubmit = false;
            }
            if (input_notPpassword.val() != input_newPassword.val()) {
                div_notPpassword.html("两次输入的密码不一致");
                checkSubmit = false;
            }
            if ($.trim(input_notPpassword.val()) == "") {
                div_notPpassword.html("请再次输入新密码");
                checkSubmit = false;
            }
            return checkSubmit;
        })
        //CharMode函数
        //测试某个字符是属于哪一类.
        function CharMode(iN){
            if (iN>=48 && iN <=57) //数字
                return 1;
            if (iN>=65 && iN <=90) //大写字母
                return 2;
            if (iN>=97 && iN <=122) //小写
                return 4;
            else
                return 8; //特殊字符 
        } 
        //bitTotal函数
        //计算出当前密码当中一共有多少种模式
        function bitTotal(num){
            modes=0;
            if((num & 3 ) != 3 && (num & 5) != 5) {
                return 0;
            }
            for (i=0;i<4;i++){
                //如果字符不同时包括字母和数字,直接返回0
                if (num & 1) {
                    modes++;
                }
                num>>>=1;
            }  
            return modes;
        }
        //checkStrong函数
        //返回密码的强度级别
        function checkStrong(sPW){
            var Modes=0;  
            for (i=0;i<sPW.length;i++){
                //测试每一个字符的类别并统计一共有多少种模式.
                Modes |= CharMode(sPW.charCodeAt(i));
            }
            //返回值为0时,说明需要重置密码
            return bitTotal(Modes) == 0 ? 0 : bitTotal(Modes);
        }
    })
</script>
