<div class="filter form-inline well">
    <form method="get" action="/sso/user/list/">
        帐号：
        <input name="account" type="text" class="input-small" value="<?php echo $_GET['account'] ?>"/>
        姓名：
        <input name="name" type="text" class="input-small" value="<?php echo $_GET['name'] ?>"/>
        部门：
        <select name="department">
            <option value="-1">--请选择--</option>
            <?php
                foreach ($this->dpt as $k => $v) {
                    echo '<option value="' . $k . '" ' . ($_GET['department'] == $k ? 'selected' : '') . ' >' . $v . '</option>';
                }
            ?>
        </select>
        状态：
        <select name="forbidden" class="input-small">
            <option value="-1">请选择</option>
            <option value="0" <?php echo $_GET['forbidden'] == '0' ? 'selected' : ''; ?> >正常</option>
            <option value="1" <?php echo $_GET['forbidden'] == '1' ? 'selected' : ''; ?> >禁用</option>
        </select>
        <input type="submit" class="btn btn-success" value="查询" />
    </form>
</div>
<?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
<script type="text/javascript" charset="utf-8">
    GJ.use("app/backend/js/backend.js", function() {
        GJ.app.backend.widget("#datagrid-1").ready(function() {
            var datagrid = this;
            datagrid.loadData();

            GJ.app.backend.on("change-status", function($el, e) {
                e.preventDefault();
                $.ajax({
                    url : $el.attr("href"),
                    type : "GET",
                    dataType : "json",
                    success : function() {
                        datagrid.loadData();
                    },
                    error : function() {
                        GJ.app.backend.trigger("alert-error", "服务器超时");
                    }
                })
            });

            GJ.app.backend.on("edit", function($el, e) {
                e.preventDefault();
                GJ.use("js/util/panel/panel.js", function() {
                    GJ.panel({
                        url : $el.attr("href"),
                        title : "",
                        iframe : true,
                        style : "text",
                        width : 420,
                        height : 420,
                        resizeAble : false,
                        onClose : function() {
                            datagrid.loadData();
                        },
                    });
                });
            });
        });
    }); 
</script>