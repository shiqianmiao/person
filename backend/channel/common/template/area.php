<?php if ($this->provinceAreaNeed) { ?>
<select id="province_area" name='province_area' class="input-small">
    <option value="0" selected="selected">省级区域</option>
    <?php foreach($this->provinceAreaList as $key => $name){ ?>
        <option value="<?php echo $name['id'];?>" <?php if ($this->queryInfo['province_area'] == $name['id']){ echo 'selected';} ?>><?php echo $name['dept_name'];?></option>
    <?php } ?>
</select>
<?php } ?>
<?php if ($this->cityAreaNeed) { ?>
    <select id="city_area" name="city_area" class="input-small">
        <option value="0" selected="selected">市级区域</option>
        <?php foreach($this->cityAreaList as $value=>$array) { ?>
            <option value="<?php echo $array['id']; ?>" <?php if ($this->queryInfo['city_area'] == $array['id']) { echo 'selected'; } ?>><?php echo $array['dept_name']; ?></option>
        <?php } ?>
    </select>
<?php } ?>
<?php if ($this->deptNeed) { ?>
    <select id="dept_id" name="dept_id" class="input-large">
        <option value="0" selected="selected">部门</option>
        <?php foreach($this->deptList as $value=>$array) { ?>
            <option value="<?php echo $array['id']; ?>" <?php if ($this->queryInfo['dept_id'] == $array['id']) { echo 'selected'; } ?>><?php echo $array['dept_name']; ?></option>
        <?php } ?>
    </select>
<?php } ?>
<?php if ($this->groupNeed) { ?>
    <select id="group_id" name="group_id" class="input-small">
        <option value="0" selected="selected">业务组</option>
        <?php foreach($this->groupList as $value=>$array) { ?>
            <option value="<?php echo $array['id']; ?>" <?php if ($this->queryInfo['group_id'] == $array['id']) { echo 'selected'; } ?>><?php echo $array['group_name']; ?></option>
        <?php } ?>
    </select>
<?php } ?>
<?php if ($this->userNeed) { ?>
    <select id="user_id" name="user_id" class="input-small">
        <option value="0" selected="selected">人员</option>
        <?php foreach($this->userList as $value=>$array) { ?>
            <option value="<?php echo $array['username']; ?>" <?php if ($this->queryInfo['user_id'] == $array['username']) { echo 'selected'; } ?>><?php echo $array['real_name']; ?></option>
        <?php } ?>
    </select>
<?php } ?>
<script>
    G.use(['jquery'], function($) {
        <?php if ($this->cityAreaNeed) { ?>
        $('#province_area').change(function(){
            var id = $(this).val();
            if (id == 57 || id == 257) {
                $('#group_id').show();
                $('#dept_id').empty();
                $('#dept_id').append('<option value="0" selected="selected">部门</option>');
                $('#group_id').empty();
                $('#group_id').append('<option value="0" selected="selected">业务组</option>');
                $('#user_id').empty();
                $('#user_id').append('<option value="0" selected="selected">人员</option>');
                //如果是重庆与上海没有下属区域中心，直接获取门店信息
                $('#city_area').hide();
                $('#dept_id').load('/common/area/getDept', 'city_area=' + id, function(responseTxt, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        $('#dept_id').html(responseTxt);
                    }
                    if (statusTxt == "error") {
                        alert("门店数据加载失败！");
                    }
                });
            } else {
                $('#group_id').show();
                $('#city_area').show();
                $('#dept_id').empty();
                $('#dept_id').append('<option value="0" selected="selected">部门</option>');
                $('#group_id').empty();
                $('#group_id').append('<option value="0" selected="selected">业务组</option>');
                $('#user_id').empty();
                $('#user_id').append('<option value="0" selected="selected">人员</option>');
                $('#city_area').load('/common/area', 'province_area=' + id, function(responseTxt, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        $('#city_area').html(responseTxt);
                    }
                    if (statusTxt == "error") {
                        alert("市级区域数据加载失败！");
                    }
                });
            }
        });
        <?php } ?>

        <?php if ($this->deptNeed) { ?>
        $('#city_area').change(function(){
            $('#group_id').show();
            var id = $(this).val();
            $('#dept_id').load('/common/area/getDept', 'city_area=' + id, function(responseTxt, statusTxt, xhr) {
                if (statusTxt == "success") {
                    $('#dept_id').html(responseTxt);
                }
                if (statusTxt == "error") {
                    alert("门店数据加载失败！");
                }
            });
        });
        <?php } ?>

        <?php if ($this->groupNeed) { ?>
        $('#dept_id').change(function(){
            $('#group_id').show();
            var id = $(this).val();
            var first_option = '<option value="0" selected>业务组</option>';
            $('#group_id').load('/common/area/getGroup', 'dept_id=' + id, function(responseTxt, statusTxt, xhr) {
                if (statusTxt == "success") {
                    if (responseTxt == '') {
                        $('#group_id').hide();
                        $('#user_id').load('/common/area/getDeptUser', 'dept_id=' + id, function(responseTxt, statusTxt, xhr) {
                            if (statusTxt == "success") {
                                $('#user_id').html(responseTxt);
                            }
                            if (statusTxt == "error") {
                                alert("用户数据加载失败！");
                            }
                        });
                    } else {
//                        alert('baidu');
                        $('#group_id').html(first_option + responseTxt);
                    }
                }
                if (statusTxt == "error") {
                    alert("门店数据加载失败！");
                }
            });
        });
        <?php } ?>

        <?php if ($this->userNeed) { ?>
        $('#group_id').change(function(){
            var id = $(this).val();
            $('#user_id').load('/common/area/getUser', 'group_id=' + id, function(responseTxt, statusTxt, xhr) {
                if (statusTxt == "success") {
                    $('#user_id').html(responseTxt);
                }
                if (statusTxt == "error") {
                    alert("门店数据加载失败！");
                }
            });
        });
        <?php } ?>
    });
</script>
