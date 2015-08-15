
<div class="filter form-inline well">

    <form method="get" action="#">
        门店名称：<input type="text" name="dept_name" class="input-medium" value="<?php echo $this->dept['dept_name'];?>" disabled = "disabled"/>
                <input type="hidden" name="dept_id" value="<?php echo $this->dept['id'];?>" />
                <input type="hidden" id="loc" name="loc" value="<?php echo $this->loc;?>" />
        门店坐标：<input type="text" name="dept_point" class="input-medium" id="point" value="<?php echo $this->dept['shop_point'];?>" readonly="readonly"/>

        <input type="submit" class="btn btn-success" value="保存坐标" data-action-type="submit"/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="javascript:location.reload();" class="btn btn-success">重新定位</a>

        <?php if (!empty($this->dept['shop_point'])) {?>
            <span style="color:red;font-size:14px;">拖动地图中的图标来修改坐标定位。</span>
        <?php } else {?>
            <span style="color:red;font-size:14px;">点击任意地方创建图标，然后拖动定位。</span>
        <?php } ?>

    </form>
</div>
<div class="span10 main" id="allmap" style="width:100%;height:700px;"></div>
<script type="text/javascript">

    G.use(['jquery', 'widget/map/map.js', 'widget/map/bmarker.js'], function($, Map, Marker) {

        var $city = $('#loc'),
            $point = $('#point'),
            city = $city.val(),
            point = $point.val();

        var map = Map.create({
            zoom : 14,
            type : 'dynamic',
            el : '#allmap',
            center : point,
            city : city,
            enableScrollWheelZoom : true
        });


        function setMark(e) {
            var p = e.point.lng + ',' + e.point.lat,
                m = new Marker({
                    point : Map.formatPoint(p),
                    enableDragging : true
                });

            map.map.removeEventListener('click', setMark);
            m.on('dragend', function(e) {
                $point.val(e.point.lng + ',' + e.point.lat);
            }).on('click', openWindow);

            map.addOverlay(m);
        }

        function openWindow() {
           this.openInfoWindow({
                title : '您当前选择的位置',
                content : '如果确定无误的话，点击上方保存按钮，保存门店坐标。',
                width : 100,
                height : 80
            });
        }

        map.ready(function() {
            if (point = Map.formatPoint(point)) {
                var m = new Marker({
                    point : point,
                    enableDragging : true
                });
                m.on('dragend', function(e) {
                    $point.val(e.point.lng + ',' + e.point.lat);
                }).on('click', openWindow);

                map.addOverlay(m);
            } else {
                map.map.addEventListener('click', setMark);
            }
        });
    });


    G.use(['app/backend/js/backend.js', 'jquery'], function (Backend, $) {
        Backend.on("submit", function(e){
            e.preventDefault();
            var $point = $.trim($('input[name="dept_point"]').val());
            var $deptId = $('input[name="dept_id"]').val();

            if ($point == '') {
                alert('坐标不能为空');
            } else {
                $.ajax({
                    type: "GET",
                    url: "/mbs_shop/shopManage/ajaxSavePoint/?shop_point=" + $point + '&dept_id=' + $deptId,
                    dataType: "json",
                    jsonp: 'json',
                    success: function(data){
                        if (data.status == '1') {
                           alert('保存成功');
                        } else if (data.status == '0') {
                           alert(data.message);
                        }
                    }
                });
            }
        });
    });
</script>