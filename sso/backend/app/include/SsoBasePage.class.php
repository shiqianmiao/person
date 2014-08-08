<?php

require_once dirname(__FILE__) . '/../../config/bootstrap.php';
require_once BACKEND . '/widget/DataGridWidget.class.php';
require_once SSO_SITE . '/app/include/SsoData.class.php';

/**
 * @brief Sso项目的基类
 * @author aozhongxu
 */

class SsoBasePage extends BackendPage {
    
    public function getQueryString() {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $queryString = isset($url['query']) ? $url['query'] : '';
        parse_str($queryString,$params);
        return http_build_query($params);
    }
    
}
