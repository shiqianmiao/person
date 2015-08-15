<!--main-->
        <div class="span10 main">
<form id="form" action="/mbs_shop/shopManage/submit/" method="post">
            <div class="clearfix">
                <div class="ctitle" style="width:65px;">店铺管理</div>
                <div class="clearfix ui-field">
                    <span class="span1">门店口号：</span>
                    <input class="span6 ml10" type="text" <?php echo $this->form->getField('shop_slogan');?>>
                    
                    <span class="checkinfo"><i class="i-infom"></i>
                        <span id="slogan_tip"></span>
                    </span>
                </div>
                <!--
                <div class="clearfix ui-field">
                    <span class="span1" style="width:65px;">门店寄语：</span>
                    <textarea class="span6 ml10"  <?php echo $this->form->getField('shop_hope');?>></textarea>
                    
                    <span class="checkinfo"><i class="i-infom"></i>
                        <span id="message_tip"></span>
                    </span>
                </div>
                -->
                <div class="clearfix">
                    <span class="span1" style="width:65px;">门店坐标：</span>
                    <a class="ml10" href="/mbs_shop/shopManage/shopMap/">[添加坐标]</a>
                </div>
                <div class="clearfix">
                    <span class="span1" style="width:65px;">服务之星：</span>
                    <a class="ml10" target="_blank" href="/mbs_organization/memberManage/">[设置服务之星]</a>
                </div>
                <div class="clearfix">
                    <span class="span1" style="width:65px;">优质车源：</span>
                    <a class="ml10" target="_blank" href="/mbs_post/shopCarList/">[设置门店优质车源]</a>
                </div>
                <div class="clearfix">
                    <span class="span1">&nbsp;</span>
                    <button class="ml10 btn btn-success" type="submit">确 认</button> <font style="color:red;font-weight:bold;">保存后一个小时内生效</font>
                </div>
            </div>
</form>
        </div>
        <!--/main-->

<script type="text/javascript">
G.use(['validator'], function () {
    G.use(['widget/form/form.js'], function(Form) {
        var form = new Form({
            el : '#form'
        });
    });
});
</script>