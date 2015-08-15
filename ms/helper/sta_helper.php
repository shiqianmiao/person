<?php

/**
 * @desc css,js 静态文件加载helper（版本号）
 * @copyright (c) 2011 273 Inc
 * @author 马鑫 <maxin@273.cn>
 * @since 2013-06-13
 */


function sta_helper($params) {

    return StaticInterface::getStaticFiles($params);
}
