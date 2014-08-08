<div class="filter form-inline well">
    <form method="get" action="/sso/group/list/">
        组名：
        <input name="name" type="text" class="input-small" value="<?php echo $_GET['name'] ?>"/>
        <input type="submit" class="btn btn-success" value="查询" />
    </form>
</div>
<?php echo $this -> datagrid -> toHTML('datagrid-1'); ?>
<script type="text/javascript" charset="utf-8">
    GJ.use("app/backend/js/backend.js", function() {
        GJ.app.backend.widget("#datagrid-1").ready(function() {
            var datagrid = this;
            datagrid.loadData();

            GJ.app.backend.on("delete", function($el, e) {
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
            
            GJ.app.backend.on("edit", function ($el, e) {
                e.preventDefault();
                GJ.use("js/util/panel/panel.js", function () {
                    GJ.panel({
                        url : $el.attr("href"),
                        title : "",
                        iframe : true,
                        style: "text",
                        width: 420,
                        height: 310,
                        resizeAble: false,
                        onClose: function () {
                            datagrid.loadData();
                        },
                    });
                });
            });
        });

    }); 
</script>
