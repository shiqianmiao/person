                            <select class="input-medium select_car_type" data-type="1" data-text='选择品牌' <?php echo $this->form->getField('car_type');?>>
                                <option value="" >选择类型</option>
                                <?php foreach($this->typeName as $value => $name){?>
                                    <option value="<?php echo $value;?>"><?php echo $name;?></option>
                                <?php }?>
                            </select>

                            <select class="input-medium select_car_type" id="car_brand_id" data-type="2" data-text = '选择车系' style="display:none;" <?php echo $this->form->getField('brand_id');?>>
                                <option value="" >选择品牌</option>
                            </select>

                            <select class="input-medium select_car_type" id="car_series_id" data-type="3" data-text = '选择年款' style="display:none;" <?php echo $this->form->getField('series_id');?>>
                                <option value="" >选择车系</option>
                            </select>

                            <?php if($this->yearNeed) { ?>
                            <select class="input-xlarge" id="year_type" style="display:none;"  <?php echo $this->form->getField('year_type');?>>
                                <option value="" >选择年款</option>
                            </select>
                            <?php } ?>
<script>
G.use(['jquery'], function($){
    $('.select_car_type').change(function(){
        var $this = $(this);
        var $val = $this.val();
        var $type = $this.data('type');
        var $text = $this.data('text');
        var $typeId = $('select[name="car_type"]').val();
        if ($val > 0) {
            $.ajax('/mbs_post/pubPost/getChild/?type='+$type+'&type_id='+$typeId+'&id=' + $val, {
                dataType : 'jsonp',
                jsonp : 'jsonCallback'
            }).done(function(data){
                var html = '', item;
                html += '<option value="">'+$text+'</option>';
                for(var i=0,l=data.length; i<l;i++) {
                    item = data[i];
                    html += '<option value="'+item['value']+'">'+item['name']+'</option>';
                }
                if ($type == 3) {
                    $('#year_type').html(html).show();
                } else if ($type == 4) {
                    //上牌城市
                    $('#plate_city').html(html).show();
                } else {
                    $this.next('.select_car_type').html(html).show();
                    $this.next('.select_car_type').nextAll('.select_car_type').hide();
                    $('#year_type').hide();
                }
            });
        } else {
            $this.nextAll('.select_car_type').hide();
            $('#year_type').hide();
        }
    });
});
</script>

