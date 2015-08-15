<?php

require_once dirname(__FILE__) . '/include/ShopPage.class.php';

/**
 * @brief 车源信息管理系统后台默认页
 * @author miaoshiqian
 * @since 2013-8-20
 */

class IndexPage extends ShopPage {
    public function defaultAction() {
        $this->render(array(), 'index.php');
    }
}
