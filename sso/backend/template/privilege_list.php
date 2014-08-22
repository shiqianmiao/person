<div class="well" style="width: 920px;">
    <div class="form-inline">
        应用：
        <select name="app">
            <option value="-1">--请选择--</option>
            <?php
            foreach ($this->allApp as $i => $row) {
                echo '<option value="' . $row['app'] . '">' . $row['app'] . ' - ' . $row['name'] . '</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-inline" style="margin-top: 20px;">
        共找到<font color="red" name="count">0</font>项&nbsp;
        <input type="button" class="btn" op="select" value="全选" />
        <input type="button" class="btn" op="look" value="收起全部" />
    </div>
    <div id="trie" style="margin-top: 20px;"></div>
</div>
<script type="text/javascript" src="http://sta.miaosq.com/app/jstree/_lib/jquery.js"></script>
<script type="text/javascript" src="http://sta.miaosq.com/app/jstree/jquery.jstree.js"></script>
<script type="text/javascript">
G.use(["app/backend/js/backend.js"], function (Backend) {
    $(function() {
        var trie = $("#trie");
        var btn_select = $("input[op=select]");
        var btn_look = $("input[op=look]");
        var btn_delete = $("input[op=delete]");
        var select_app = $("select[name=app]");
        var font_count = $("font[name=count]");

        function trieReload() {
            if (select_app.val() == "-1") {
                font_count.html("0");
                trie.html("");
                return false;
            }
            $.ajax({
                url : "/sso/privilege/ajaxList",
                type : "post",
                dataType : "json",
                data : {
                    "app" : select_app.val()
                },
                success : function(result) {
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

        btn_delete.click(function(e) {
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
                url : "/sso/privilege/ajaxDelete/",
                type : "POST",
                dataType : "json",
                data : {
                    "ids" : GJ.jsonEncode(ids),
                    "app" : select_app.val()
                },
                success : function() {
                    trieReload();
                },
                error : function() {
                    Backend.trigger("alert-error", "表单提交失败");
                }
            });
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
});
</script>
