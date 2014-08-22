<div class="filter form-inline well">
    <form method="get" action="/sso/group/list/">
        组名：
        <input name="name" type="text" class="input-small" value="<?php echo $_GET['name'] ?>"/>
        <input type="submit" class="btn btn-success" value="查询" />
    </form>
</div>
<?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
<script type="text/javascript" charset="utf-8">
    G.use(["app/backend/js/backend.js", "jquery", "widget/dialog/dialog.js"], function(Backend, $, Dialog) {
        Backend.widget("#datagrid-1").ready(function() {
            var datagrid = this;
            datagrid.load();

            Backend.on("delete", function(e) {
                e.preventDefault();
                var url = this.href;
                $.ajax({
                    url : url,
                    type : "GET",
                    dataType : "json",
                    success : function() {
                        datagrid.load();
                    },
                    error : function() {
                        Backend.trigger("alert-error", "服务器超时");
                    }
                })
            });
            
            Backend.on("edit", function (e) {
                e.preventDefault();
                var url = this.href;
                var dialog = new Dialog({
                    title : "编辑",
                    width : 480,
                    height: 300,
                    close : function() {
                        //重新载入列表的数据
                        datagrid.load();
                    }
                });
                dialog.ready(function () {
                    this.open(url);
                });
                window.dialog = dialog;
            });
        });

    }); 
</script>
