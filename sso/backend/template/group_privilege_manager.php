<br>
<div class="form-inline">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red"><?php echo $this->name; ?></font>权限管理：
    <select name="app">
        <option value="-1">请选权限所在应用</option>
        <?php
        foreach ($this->allApp as $i => $row) {
            echo '<option value="' . $row['app'] . '">' . $row['app'] . ' - ' . $row['name'] . '</option>';
        }
        ?>
    </select>
</div>
<table>
    <tr style="vertical-align: top;">
        <td>
        <div class="well" style="width: 500px; background-color: #FFF; border: 0px;">
            <div class="form-inline">
                该组权限：共<font color="red" name="count2">0</font>项&nbsp;
                <input type="button" class="btn" op="select2" value="全选" />
                <input type="button" class="btn" op="look2" value="收起全部" />
                <input type="button" class="btn" op="remove" value="移除权限" />
            </div>
            <div id="trie2" style="margin-top: 15px;"></div>
        </div></td>
        <td style="vertical-align: top;">
        <div class="well" style="width: 500px;">
            <div class="form-inline">
                所有权限：共<font color="red" name="count">0</font>项&nbsp;
                <input type="button" class="btn" op="select" value="全选" />
                <input type="button" class="btn" op="look" value="收起全部" />
                <input type="button" class="btn" op="add" value="添加权限" />
            </div>
            <div id="trie" style="margin-top: 15px;"></div>
        </div></td>
    </tr>
</table>
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
<script type="text/javascript" src="http://sta.273.cn/app/sso/jstree/_lib/jquery.js"></script>
<script type="text/javascript" src="http://sta.273.cn/app/sso/jstree/jquery.jstree.js"></script>
<script type="text/javascript">
//G.use(["app/backend/js/backend.js", "jquery"]， function(Backend, $) {
    $(function() {
        
        var select_app = $("select[name=app]");
        var trie2 = $("#trie2");
        var btn_select2 = $("input[op=select2]");
        var btn_look2 = $("input[op=look2]");
        var font_count2 = $("font[name=count2]");
        var input_id = $("input[name=id]");
        var trie = $("#trie");
        var btn_select = $("input[op=select]");
        var btn_look = $("input[op=look]");
        var font_count = $("font[name=count]");
        var btn_add = $("input[op=add]");
        var btn_remove = $("input[op=remove]");

        function trieReload() {
            if (select_app.val() == "-1") {
                font_count2.html("0");
                font_count.html("0");
                trie2.html("");
                trie.html("");
                return false;
            }
            $.ajax({
                url : "/sso/privilege/ajaxGetPrivilegeByGroup",
                type : "post",
                dataType : "json",
                data : {
                    "app" : select_app.val(),
                    "id" : input_id.val(),
                },
                success : function(result) {
                    font_count2.html(result.count2);
                    trie2.html(result.trieHtml2);
                    trie2.jstree({
                        "themes" : {
                            "icons" : false
                        },
                        "plugins" : ["themes", "html_data", "checkbox"]
                    }).bind("loaded.jstree", function(e, data) {
                        data.inst.open_all(-1);
                    });
                    
                    font_count.html(result.count);
                    trie.html(result.trieHtml);
                    trie.jstree({
                        "themes" : {
                            "icons" : false
                        },
                        "plugins" : ["themes", "html_data", "checkbox"]
                    }).bind("loaded.jstree", function(e, data) {
                        data.inst.open_all(-1);
                    });
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
            
        }

        select_app.change(function() {
            trieReload();
        });
        
        btn_add.click(function(e) {
            if (select_app.val() == "-1") {
                return false;
            }
            var ids = [];
            trie.jstree("get_checked", null, true).each(function(i, node) {
                if (node.id) {
                    ids.push(node.id);
                }
            });
            if (ids.length == 0) {
                return false;
            }
            $.ajax({
                url : "/sso/privilege/ajaxAddPrivilegeToGroup",
                type : "post",
                dataType : "json",
                data : {
                    "privilege_ids" : JSON.stringify(ids),
                    "group_id" : input_id.val(),
                },
                success : function() {
                    trieReload();
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
        })
        
        btn_remove.click(function(e) {
            if (select_app.val() == "-1") {
                return false;
            }
            var ids = [];
            trie2.jstree("get_checked", null, true).each(function(i, node) {
                if (node.id) {
                    ids.push(node.id);
                }
            });
            if (ids.length == 0) {
                return false;
            }
            $.ajax({
                url : "/sso/privilege/ajaxRemovePrivilegeToGroup",
                type : "post",
                dataType : "json",
                data : {
                    "privilege_ids" : JSON.stringify(ids),
                    "group_id" : input_id.val(),
                },
                success : function() {
                    trieReload();
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
        })

        btn_select2.click(function() {
            if (btn_select2.val() == "全选") {
                trie2.jstree("check_all");
                btn_select2.val("全不选");
            } else {
                trie2.jstree("uncheck_all");
                btn_select2.val("全选");
            }
        })

        btn_look2.click(function() {
            if (btn_look2.val() == "展开全部") {
                trie2.jstree("open_all");
                btn_look2.val("收起全部");
            } else {
                trie2.jstree("close_all");
                btn_look2.val("展开全部");
            }
        })
        
        btn_select.click(function() {
            if (btn_select.val() == "全选") {
                trie.jstree("check_all");
                btn_select.val("全不选");
            } else {
                trie.jstree("uncheck_all");
                btn_select.val("全选");
            }
        })

        btn_look.click(function() {
            if (btn_look.val() == "展开全部") {
                trie.jstree("open_all");
                btn_look.val("收起全部");
            } else {
                trie.jstree("close_all");
                btn_look.val("展开全部");
            }
        })
    });
    
//});
</script>
