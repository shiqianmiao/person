<?php
/**
 * @author 范孝荣<fanxr@273.cn>
 * @since  2013-12-4
 * @copyright Copyright (c) 2003-2013 273 Inc. (http://www.273.cn)
 * @desc 适用于业管后台的分页插件
 */
function pager_helper($params) {
    $pagerHtml = "<div class='pagination pull-right'><ul>";

    if ($params['count'] == 0) {
        return $pagerHtml;
    }
    unset($params['get']['_sys_url_path']);
    unset($params['get']['app']);
    unset($params['get']['p']);
    $getParams = '?';
    foreach ($params['get'] as $key => $value) {
        $getParams .= ($key . '=' . $value . '&');
    }
    $getParams = rtrim($getParams, '&');

    $pagerHtml .= "<li class='disabled'><a href='javascript:;'>1/{$params['last']}页，共{$params['count']}条</a></li>";

    $start = max(1, $params['current'] - 5);
    $pagerHtml .= "<li><a href='" . $params['url'] . $getParams . "&p=1'>首页</a></li>";
    $end = min($params['current'] + 5, $params['last']);
    $linkPage = "";
    for ($i = $start; $i<=$end; ++$i) {
        if ($i != $params['current']) {
            $linkPage .= "<li><a href='" . $params['url'] . $getParams . '&p=' . $i . "'>{$i}</a></li>";
        } else {
            $linkPage .= "<li class='active'><a href='" . $params['url'] . $getParams . '&p=' . $i . "'>{$i}</a></li>";
        }
    }
    $pagerHtml .= $linkPage;

    $pagerHtml .= "<li><a href='" . $params['url'] . $getParams . '&p=' . $params['last'] . "'>末页</a></li>";
    $pagerHtml .= "</ul></div>";
    return $pagerHtml;
}
