<div class="filter form-inline well">
    <form method="get" action="/sso/app/list/">
        代号：
        <input name="app" type="text" class="input-small" value="<?php echo $_GET['app'] ?>"/>
        名称：
        <input name="name" type="text" class="input-small" value="<?php echo $_GET['name'] ?>"/>
        <input type="submit" class="btn btn-success" value="查询" />
    </form>
</div>
<?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
<script type="text/javascript" charset="utf-8">
    G.use(["app/backend/js/backend.js", "jquery"], function(Backend, $) {
        Backend.widget('#datagrid-1').ready(function () {
            Backend.on("edit", function (e) {
                var url = this.href;
                e.preventDefault();
                G.use(["widget/dialog/dialog.js"], function (Dialog) {
                    var dialog = new Dialog({
                        title : "编辑应用",
                        width : 500,
                        height: 280,
                        close : function() {
                            //重新载入列表的数据
                            Backend.widget('#datagrid-1').ready(function () {
                                this.load();
                            });
                        }
                    });
                    dialog.ready(function () {
                        this.open(url);
                    });
                    window.dialog = dialog;
                });
            });
        });
    });
</script>