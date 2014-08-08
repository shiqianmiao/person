<table style="margin: 20px;">
    <tr>
        <td> 已分配<font color="red"><?php echo $this->number; ?></font>个组给用户<font color="red"><?php echo $this->account; ?></font></td>
        <td></td>
        <td>
        <div class="form-inline" style="margin-bottom: 10px;">
            查询组：
            <input type="text" name="name" class="input-small"/>
            <input type="button" name="search" class="btn btn-success" value="确定" />
        </div></td>
    </tr>
    <tr>
        <td>
        <select id="list_a"  multiple="multiple" style="width: 300px; height: 400px;">
            <?php
                foreach ($this->all as $i => $row) {
                    echo '<option value="' . $row['id'] . '">' . $row['id'] . ' - ' . $row['name'] . '</option>';
                }
            ?>
        </select></td>
        <td>
        <input name="add" type="button" class="btn" style="margin: 10px;" value="<<添加组" />
        <br>
        <input name="remove" type="button" class="btn" style="margin: 10px;" value="移除组>>" />
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
    GJ.use('jquery', function() {
        
        var btn_add = $("input[name=add]");
        var btn_remove = $("input[name=remove]");
        var btn_search = $("input[name=search]");
        var input_id = $("input[name=id]");
        var input_name = $("input[name=name]");
        var input_submit = $("input[name=submit]");
        
        input_submit.click(function() {
            var group_ids = [];
            $("#list_a option").each(function() {
                group_ids.push($(this).val());
            });
            $.ajax({
                url : "/sso/user/ajaxAddGroupToUser",
                type : "post",
                dataType : "json",
                data : {
                    "user_id" : input_id.val(),
                    "group_ids" : GJ.jsonEncode(group_ids)
                },
                success : function(result) {
                    location.reload();
                },
                error : function() {
                    GJ.app.backend.trigger("alert-error", "表单提交失败");
                }
            });
        })
        
        
        btn_search.click(function() {
            
            if ($.trim(input_name.val()) == "") {
                return false;
            }

            $.ajax({
                url : "/sso/user/ajaxGetOtherGroup",
                type : "POST",
                dataType : "json",
                data : {
                    "id" : input_id.val(),
                    "name" : input_name.val()
                },
                success : function(result) {
                    var str = "";
                    $.each(result.all, function(i, row) {
                        str += '<option value="' + row.id + '">' + row.id + ' - ' + row.name + '</option>';
                    });
                    $("#list_b").html(str);
                },
                error : function() {
                    GJ.app.backend.trigger("alert-error", "表单提交失败");
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