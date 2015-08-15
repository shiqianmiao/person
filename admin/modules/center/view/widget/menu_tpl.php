<?php if (!empty($this->menu) && is_array($this->menu)) {?>
    <ul class="sidebar-menu">
    <?php foreach($this->menu as $menu) {?>
        <?php if(!empty($menu)){ ?>
        <?php $isCheck = in_array($this->menuUri['first'], $menu['sign']); $class = $isCheck ? 'active' : '';?>
        
            <li <?php echo isset($menu['active']) ? 'class="active"' : (isset($menu['sub_menu']) ? 'class="treeview '.$class.'"' : '') ?>>
                <a style="font-weight: bold;" href="<?php echo $menu['url']?>">
                    <i class="<?php echo $menu['icon'];?>"></i>
                    <span><?php echo $menu['name']?></span>
                    <?php if (isset($menu['sub_menu'])) {?>
                        <i class="fa fa-angle-left pull-right"></i>
                    <?php }?>
                </a>
                
                <?php if(!empty($menu['sub_menu'])){?>
                    <ul class="treeview-menu" <?php if($isCheck){?>style="display:block;"<?php }?>>
                    <?php foreach($menu['sub_menu'] as $subMenu) {?>
                        <li>
                            <?php 
                            if ($subMenu['name'] == 'unsee')
                                continue;
                            
                            if (is_array($subMenu['sign'])) {
                                $isSubCheck = in_array($this->menuUri['uri'], $subMenu['sign']) ? true : false;
                            } else {
                               $isSubCheck = $this->menuUri['uri'] == $subMenu['sign'] ? true : false;
                            }
                            ?>
                            <?php $icon = $isSubCheck ? 'fa fa-check' : 'fa fa-angle-double-right';?>
                            
                            <a href="<?php echo $subMenu['url']?>" <?php if($isSubCheck) {?>style="color:#3c8dbc;font-weight:bold;"<?php }?>>
                                <i class="<?php echo $icon;?>"></i>
                                <?php echo $subMenu['name']?>
                            </a>
                        </li>
                    <?php } ?>
                    </ul>
                <?php }?>
            </li>
        <?php } ?>
    <?php }?>
    </ul>
<?php } ?>