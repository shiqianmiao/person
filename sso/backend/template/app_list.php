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
    G.use(["app/backend/js/backend.js"], function(Backend) {
        /*GJ.app.backend.widget("#datagrid-1").ready(function() {
            var datagrid = this;
            datagrid.loadData();
            
            GJ.app.backend.on("edit", function ($el, e) {
                e.preventDefault();
                GJ.use("js/util/panel/panel.js", function () {
                    GJ.panel({
                        url : $el.attr("href"),
                        title : "",
                        iframe : true,
                        style: "text",
                        width: 420,
                        height: 260,
                        resizeAble: false,
                        onClose: function () {
                            datagrid.loadData();
                        },
                    });
                });
            });
        });*/

    }); 
</script>