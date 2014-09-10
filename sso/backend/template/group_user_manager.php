<table style="margin: 20px;">
    <tr>
        <td><font color="red"><?php echo $this->name; ?></font>一共有<font color="red"><?php echo $this->number; ?></font>位用户</td>
        <td></td>
        <td>
        <div class="form-inline" style="margin-bottom: 10px;">
            查询用户：
            <input type="text" name="name" class="input-small"/>
            <input type="button" name="search" class="btn btn-success" value="确定" />
        </div></td>
    </tr>
    <tr>
        <td>
        <select id="list_a"  multiple="multiple" style="width: 300px; height: 400px;">
            <?php
                foreach ($this->all as $i => $row) {
                    echo '<option value="' . $row['id'] . '">' . $row['id'] . ' - ' . $row['account'] . ' - ' . $row['name'] . '</option>';
                }
            ?>
        </select></td>
        <td>
        <input name="add" type="button" class="btn" style="margin: 10px;" value="<<添加用户" />
        <br>
        <input name="remove" type="button" class="btn" style="margin: 10px;" value="移除用户>>" />
        </td>
        <td>
        <select id="list_b" multiple="multiple" style="width: 300px; height: 400px;">
        </select>
        </td>
    </tr>
    <tr>
        <td><input type="button" name="submit" class="btn btn-success" value="保存" /></td>
        <td></td>
        <td><input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" /></td>
    </tr>
</table>
<script>
    G.use(["app/backend/js/backend.js", "jquery"], function(Backend, $) {
        
        var btn_add = $("input[name=add]");
        var btn_remove = $("input[name=remove]");
        var btn_search = $("input[name=search]");
        var input_id = $("input[name=id]");
        var input_name = $("input[name=name]");
        var input_submit = $("input[name=submit]");
        
        input_submit.click(function() {
            var user_ids = [];
            $("#list_a option").each(function() {
                user_ids.push($(this).val());
            });
            $.ajax({
                url : "/sso/group/ajaxAddUserToGroup",
                type : "post",
                dataType : "json",
                data : {
                    "group_id" : input_id.val(),
                    "user_ids" : JSON.stringify(user_ids)
                },
                success : function(result) {
                    location.reload();
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
        })
        
        
        btn_search.click(function() {
            
            if ($.trim(input_name.val()) == "") {
                return false;
            }

            $.ajax({
                url : "/sso/group/ajaxGetOtherUser",
                type : "post",
                dataType : "json",
                data : {
                    "id" : input_id.val(),
                    "account" : input_name.val()
                },
                success : function(result) {
                    var str = "";
                    $.each(result.all, function(i, row) {
                        str += '<option value="' + row.id + '">' + row.id + ' - ' + row.account + ' - ' + row.name + '</option>';
                    });
                    $("#list_b").html(str);
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
        })

        btn_add.click(function() {
            if ($("#list_b option:selected").length == 0) {
                return;
            }
            $("#list_b option:selected").each(function() {
                $("#list_a").append('<option value="' + $(this).val() + '">' + $(this).text() + '</option>');
            });
        });
        
        btn_remove.click(function() {
            if ($("#list_a option:selected").length == 0) {
                return;
            }
            $("#list_a option:selected").each(function() {
                $(this).remove();
            });
        });
    });

</script>
