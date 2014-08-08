        <select id="<?php echo (!empty($this->provinceName) ? $this->provinceName : 'province'); ?>" name='<?php echo (!empty($this->provinceName) ? $this->provinceName : 'province'); ?>' class="input-small" <?php echo (!empty($this->provinceField) ? $this->provinceField : ''); ?>>
                <option value="" selected="selected">--省份--</option>
            <?php foreach($this->provinceList as $value => $array){ ?>
                <?php foreach ($array as $key=>$name) { ?>
                <option value="<?php echo $name['id'];?>" <?php if ($this->queryInfo['province'] == $name['id']){ echo 'selected';} else if ($this->deptDefault === true && $name['id'] == $this->deptInfo['province']) {echo 'selected';}?>><?php echo $name['name'];?></option>
                <?php } ?>
            <?php } ?>
        </select>
    <?php if ($this->cityNeed) { ?>
        <select id="<?php echo (!empty($this->cityName) ? $this->cityName : 'city'); ?>" name="<?php echo (!empty($this->cityName) ? $this->cityName : 'city'); ?>" class="input-small" <?php echo (!empty($this->cityField) ? $this->cityField : ''); ?>>
                <option value="" selected="selected">--城市--</option>
           <?php foreach($this->cityList as $value=>$array) { ?>
                <option value="<?php echo $array['id']; ?>" <?php if ($this->queryInfo['city'] == $array['id']) { echo 'selected'; } if ($this->deptDefault === true && $array['id'] == $this->deptInfo['city']) {echo 'selected';}?>><?php echo $array['name']; ?></option>
            <?php } ?>
        </select>
    <?php } ?>
    <?php if ($this->deptNeed) { ?>
        <select id="<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>" name="<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>" class="input-middle" <?php echo (!empty($this->deptField) ? $this->deptField : ''); ?>>
                <option value="" selected="selected">--门店--</option>
           <?php foreach($this->deptList as $value=>$array) { ?>
                <option value="<?php echo $array['id']; ?>" <?php if ($this->queryInfo['dept_id'] == $array['id']) { echo 'selected'; } else if ($this->deptDefault === true && $array['id'] == $this->deptInfo['id']) {echo 'selected';}?>><?php echo $array['dept_name']; ?></option>
            <?php } ?>
        </select>
    <?php } ?>
    <?php if ($this->userNeed) { ?>
        <select id="<?php echo (!empty($this->userName) ? $this->userName : 'user_name'); ?>" name="<?php echo (!empty($this->userName) ? $this->userName : 'user_name'); ?>" class="input-small" <?php echo (!empty($this->userField) ? $this->userField : ''); ?>>
                <option value="" selected="selected">--人员--</option>
           <?php foreach($this->userList as $value=>$array) { ?>
                <option value="<?php echo $array['username']; ?>" <?php if ($this->queryInfo['user_name'] == $array['username']) { echo 'selected'; } ?>><?php echo $array['real_name']; ?></option>
            <?php } ?>
        </select>
    <?php } ?>
<script>
G.use(['jquery'], function($) {
            <?php if ($this->cityNeed) { ?>
            $('#<?php echo (!empty($this->provinceName) ? $this->provinceName : 'province'); ?>').change(function(){
                var id = $(this).val();
                $('#<?php echo (!empty($this->cityName) ? $this->cityName : 'city'); ?>') .load('/common/city', 'province=' + id, function(responseTxt, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        $('#<?php echo (!empty($this->cityName) ? $this->cityName : 'city'); ?>').html(responseTxt);
                    }
                    if (statusTxt == "error") {
                        alert("城市数据加载失败！");
                    }
                });
                <?php if ($this->deptNeed) { ?>
                $('#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>').load('/common/city/getDept', 'province=' + id, function(responseTxt, statusTxt, xhr) {
                    if(statusTxt == "success") {
                        $('#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>').html(responseTxt);
                    }
                    if(statusTxt == "error") {
                        alert("门店数据加载失败！");
                    }
                });
                <?php } ?>
                <?php if ($this->userNeed) { ?>
                $("#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>").val('');
                $("#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>").change();
                <?php } ?>
            });
            <?php } ?>
            <?php if ($this->deptNeed) { ?>
            $('#<?php echo (!empty($this->cityName) ? $this->cityName : 'city'); ?>').change(function(){
                var id = $(this).val();
                $('#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>').load('/common/city/getDept', 'city=' + id, function(responseTxt, statusTxt, xhr) {
                    if(statusTxt == "success") {
                        $('#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>').html(responseTxt);
                    }
                    if(statusTxt == "error") {
                        alert("门店数据加载失败！");
                    }
                });
                <?php if ($this->userNeed) { ?>
                $("#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>").val('');
                $("#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>").change();
                <?php } ?>
            });
            <?php } ?>
            <?php if ($this->userNeed) { ?>
            $('#<?php echo (!empty($this->deptName) ? $this->deptName : 'dept_id'); ?>').change(function(){
                var id = $(this).val();
                $('#<?php echo (!empty($this->userName) ? $this->userName : 'user_name'); ?>').load('/common/city/getUser', 'dept_id=' + id + '&departure=<?php echo $this->departure ?>', function(responseTxt, statusTxt, xhr) {
                    if(statusTxt == "success") {
                        $('#<?php echo (!empty($this->userName) ? $this->userName : 'user_name'); ?>').html(responseTxt);
                    }
                    if(statusTxt == "error") {
                        alert("用户数据加载失败！");
                    }
                });
            });
            <?php } ?>
});
</script>
