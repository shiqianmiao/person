<div id="chart" data-x-field='date' data-widget="chart" data-show-fields="['title1', 'title2']" data-fields='{"title1": "标题1", "title2": "标题2", "title3": "标题3"}' style="height: 300px; margin-bottom: 10px;">
</div>
<div class="filter form-inline well" data-for="#datagrid-1">
    <label for="date">date:</label><input type="text" class="input-small" data-min-date='2012-10-11' data-widget="datepicker" name="date" value="" id="date">
    <label for="date2">date:</label><input type="text" class="input-small" data-min-date='2012-10-11' data-widget="datepicker" name="date2" value="" id="date2">
    <a href="#" class="btn btn-success pull-right" id="add-item-btn"><i class="icon-plus icon-white"></i>增加条目</a>
</div>
<?php echo $this->datagrid->toHTML('datagrid-1'); ?>


<script type="text/javascript" charset="utf-8">
GJ.use('app/backend/js/backend.js', function () {
    GJ.app.backend.widget('#datagrid-1').ready(function () {
        var datagrid = this;
        GJ.app.backend.widget('#chart').ready(function () {
            var chart = this;
            datagrid.on('data-update', function (data) {
                chart.update(data.rows);
            });
            datagrid.loadData();
        });
    });
});
</script>