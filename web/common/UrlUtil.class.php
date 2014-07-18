<?php
/**
 * 列表页url参数解析与生成
 * @author daiyuancheng <daiyc@273.cn>
 * @date 2013-3-26
 */

require_once API_PATH . '/interface/UriInfoInterface.class.php';
require_once API_PATH . '/interface/VehicleV2Interface.class.php';
require_once API_PATH . '/interface/LocationInterface.class.php';
require_once WEB_V3 . '/common/UrlUtil.class.php';

class UrlUtilV2 {
    public $isOldUrl = false;
    /**
     * 短url替换配置
     * @var array
     */
    private $_replaceConfig = array(
        'type' => array(
            1 => 'ps',
            2 => 'sv',
            3 => 'mb',
            4 => 'lc',
            5 => 'hc',
            6 => 'sc',
            7 => 'pu',
            8 => 'bs',
        )
    );

    /**
     * 参数模式
     * @var array(        //每项表示uri中以'/'分割的字符串的匹配规则
     *         -array(   //主参匹配规则
     *             0 =>  参数名
     *             1 =>  前缀
     *             2 =>  匹配正则式
     *             3 =>  默认值
     *             4 =>  后缀
     *             5 =>  为或null, false表示不参与URL构建
     *         ) |
     *          -array(
     *              array(
     *              )
     *          )
     * )
     */
    public $urlMap = array(

        array('brand',          '', '', null, null, true),
        array('series',         '', '', null, null, true),
        array('model',          'x_', '', null, null, true),
        array('type',           '', 'ps|pu|lc|mb|sc|sv|bs|hc', null, null, true),
        'sub_params'   =>  array(
            array('district',      '',        '',             null, null, true),
            array('price',         'jg',       '\d+',          null, null, true), //价格
            array('min_price',     'i',       '\d+',          null, null, true), //最低价格
            array('max_price',     'a',       '\d+',          null, null, true), //最高价格
            array('displace',      'E',       '\d+',          null, null, true), //排量
            array('min_displace',  'N',       '[\d\.]+',      null, null, true), //排量
            array('max_displace',  'X',       '[\d\.]+',      null, null, true), //排量
            array('kilometer',     'L',       '\d{1,2}',      null, null, true), //表显里程
            array('min_kilometer', 'K',       '\d+',          null, null, true), //表显里程
            array('max_kilometer', 'R',       '\d+',          null, null, true), //表显里程
            array('gearbox',       '',        'sd|zd|sz',     null, null, true), //变速箱
            array('years',         'cl',       '\d+',          null, null, true), //车龄
            array('min_age',       'y',       '[\d]{1,2}',    null, null, true), //车龄
            array('max_age',       'z',       '[\d]{1,2}',    null, null, true), //车龄
            array('color',         'wc',      '[\d]{1,2}',    null, null, true), //颜色
            array('nophoto',       'wp',      '[\d]{1}',      0,    null, true),//是否为无图，默认为有图模式
            array('onsale',        'os',      '[\d]{1}',      0,    null, true),//是否同时显示已售, 0代表同时显示已售,1只显示在售
            array('keywords',      'wk',      '.+',           null, null, true),
            array('order',         'o',       '[ipckt][ad]',  null, null, true),
            array('page',          'p',       '\d+',          1,    null, true),
            //新参数均以配置的编号代替，0表示不限，为99的max表示不限大
            //两/三厢：（车身结构）
            array('structure',     'U',       '\d+',          0,    null, true), //两厢，三厢，旅行车，敞篷车
            //列表/大图
            array('show_pic',      'W',       '[\d]{1}',      0,    null, true),
            //最近几天
            array('recent',        'C',       '\d+',          0,    null, true), //3,7,30
            //排放标准
            array('emission',      'pf',       '[\d]{1,2}',    0,    null, true),
        ),
    );

    /**
     * 设置互斥，以主参数名为主
     * @var array
     */
    public $excludeMap = array(
        'price'       => array(
            'min_price',
            'max_price',
        ),
        'displace'    => array(
            'min_displace',
            'max_displace',
        ),
        'years'       => array(
            'min_age',
            'max_age',
        ),
        'kilometer'       => array(
            'min_kilometer',
            'max_kilometer',
        ),
    );


    /**
     * 解析url得到的参数
     * @var array
     */
    protected $_requestParams = array();

    public function __construct($urlMap = null) {
        if (!empty($urlMap)) {
            $this->urlMap = $urlMap;
        }
    }

    /**
     * 解析当前的url得到满足模式的参数列表
     */
    public function parseUrl($url = null, $domain = array()) {
        //domain
        if (!empty($url)) {
            $urlParams = parse_url($url);
            $host = $urlParams['host'];
        } else {
            $host = $_SERVER['HTTP_HOST'];
            $urlParams = parse_url($_SERVER['REQUEST_URI']);
        }
        if (empty($domain) && preg_match('/^(\w+)\.273\./', $host, $matchs) && !empty($matchs[1])) {
            $this->_requestParams['domain'] = $matchs[1];
        } else {
            $this->_requestParams['domain'] = $domain['location'];
            $this->_requestParams['partner'] = $domain['partner'];
            $this->_requestParams['not_exist_partner'] = $domain['not_exist_partner'];
        }
        $uri = trim($urlParams['path']);
        if (substr($uri, -1) != '/' && preg_match('/(\.html|\.php)$/', $uri) == 0 && empty($url)) {
            $uri .= '/';
            $redirectUrl = $uri . ($urlParams['query'] ? ("?{$urlParams['query']}") : "");
            ResponseUtil::redirectPermently($redirectUrl);
        }
        $uris = explode("/", trim($uri,'/'));
        $uriCount = count($uris);
        do {
            if ($uri == '/car/') {
                if (!empty($urlParams['query'])) {
                    //外站以"/car/"开头的get筛选参数，解析并301跳转到本站规范url
                    $requestParams = $this->parseUrlByGetParams($urlParams['query']);
                    if ($requestParams['brand'] || $requestParams['price'] || $requestParams['min_price'] || $requestParams['max_price']
                        || ($requestParams['domain'] && $requestParams['domain'] != 'www')) {
                        ResponseUtil::redirectPermently($this->createUrl($requestParams));
                    }
                    break;
                } else {
                    //uri为"/car/"无筛选参数，跳出
                    $this->_requestParams['page_type'] = 'sale';
                    break;
                }
            }
            //从uri层数判断是否为旧url
            if ($uriCount > 2 || $uriCount == 2 && $uris[0] == 'car') {
                //新url只有两层uri，大于两层或第一层为car的都是旧url,跳出
                $this->isOldUrl = true;
                break;
            }
            if ($uriCount == 1) {
                $subRet = $this->_parseSubParams($uris[0]);
                if ($subRet === false) {
                    $mainRet = $this->_parseMainParams($uris[0]);
                    if ($mainRet === false) {
                         header('HTTP/1.1 404 Not Found');
                         header("status: 404 Not Found");
                    } else {
                        //能被识别为主参，不是旧url
                        $this->isOldUrl = false;
                    }
                } else {
                    //在有且仅有只定义了自定义价格区间的情况下显示热门价格链接,取代城市链接
                    $showHotPrice = preg_match('/^(I\d+_)?A\d+$/', $uris[0], $matches);
                    if (!empty($showHotPrice)) {
                        $this->_requestParams['show_hot_price'] = 1;
                    }
                }
            }
            else if ($uriCount == 2) {
                $subRet = $this->_parseSubParams($uris[1]);
                if ($subRet === false) {
                    //副参不能完全解析,可能为旧url,跳出
                    break;
                }
                $mainRet = $this->_parseMainParams($uris[0]);
                if ($mainRet === false) {
                    //旧url,跳出
                    break;
                }
            }
            $this->_mergeExclude($this->_requestParams);
            break;
        } while(true);
        if ($this->isOldUrl) {
            $oldUrlUtil = new UrlUtil(null,$url);
            $this->_requestParams = $oldUrlUtil->getRequestParams();
            if (empty($url)) {
                $newUrl = $this->createUrl($this->_requestParams,false);
                ResponseUtil::redirectPermently($newUrl);
            }
        } else {
            //http://www.273.cn/x5/H0005/,过滤掉无效的H0005参数
            if (isset($this->_requestParams['price']) && strlen($this->_requestParams['price']) == 4) {
                $redirect = true;
                unset($this->_requestParams['price']);
            }
            if (isset($this->_requestParams['years']) && strlen($this->_requestParams['years']) == 4) {
                $redirect = true;
                unset($this->_requestParams['years']);
            }
            if ($redirect) {
                $newUrl = $this->createUrl($this->_requestParams,false);
                ResponseUtil::redirectPermently($newUrl);
            }
            // 设置默认参数
            foreach ($this->urlMap['sub_params'] as $val) {
                if (!isset($this->_requestParams[$val[0]]) && isset($val[3])) {
                    $this->_requestParams[$val[0]] = $val[3];
                }
            }
        }
    }

     /**
      * 副参解析
      **/
    private function _parseSubParams($subParamStr) {
        if (empty($subParamStr)) {
            return;
        }
        $subParams = explode('_', $subParamStr);
        $subCount = count($subParams);
        $si = 0;
        foreach ($this->urlMap['sub_params'] as $urlMap) {
            if ($urlMap[0] == 'district') {
                //区域参数单独处理
                $district = LocationInterface::getDistrictByUrl(array('url_path'=>$subParams[$si]));
                if (!empty($district)) {
                    $this->_requestParams['district'] = $subParams[$si];
                    $si++;
                }
                continue;
            }

            if ($si >= $subCount) {
                break;
            }

            $matchStr = "/^" . $urlMap[1] . "(" . $urlMap[2] . ")$/";
            if (isset($urlMap[4])) {
                $matchStr = "/^" . $urlMap[1] . "(" . $urlMap[2] . ")" . $urlMap[4] . "$/";
            }

            if (preg_match($matchStr, $subParams[$si], $mat)) {
                $val = ($mat[1] === NULL || $mat[1] === 'undefined') ? $urlMap[3] : $mat[1];
                $this->_requestParams[$urlMap[0]] = $val;
                $si++;
            }
            //按发布时间排序参数list单独处理
            //else if ($subParams[$si] == 'list') {
            //    $this->_requestParams['order'] = 'td';
            //    $this->_requestParams['onsale'] = 1;
            //    $si++;
            //}
        }
        if ($si < $subCount) {
            //副数不能全部解析，可能为旧url
            $this->isOldUrl = true;
            return false;
        }
        return true;
    }

    /**
     * 主参解析
     **/
    private function _parseMainParams($mainParamStr) {
        if (empty($mainParamStr)) {
            return;
        }
        $mainParams = explode('_', $mainParamStr);
        $mainCount = count($mainParams);
        $mi = 0;
        //第一级只包含一个筛选项时，先判断是不是地区参数
        if ($mainCount == 1) {
            $info = LocationInterface::getDistrictByUrl(array('url_path'=>$mainParams[0]));
            if (!empty($info)) {
                //为地区参数时，识别为旧url
                $this->isOldUrl = true;
                return false;
            }
        } else if ($mainCount == 2 && preg_match('/^x_\d+$/', $mainParamStr)) { // 车型列表页
            $modelId = (int) $mainParams[1];
            if ($modelId > 0) {
                $modelInfo = VehicleV2Interface::getModelById(array(
                    'model_id'   => $modelId,
                ));
                if ($modelInfo && $modelInfo['series_id'] > 0) {
                    $this->_requestParams['model'] = $modelId;
                    $seriesInfo = VehicleV2Interface::getSeriesById(array(
                        'series_id'    => $modelInfo['series_id'],
                    ));
                    if ($seriesInfo && $seriesInfo['brand_id'] > 0) {
                        $brandInfo = VehicleV2Interface::getBrandById(array(
                            'brand_id'    => $seriesInfo['brand_id'],
                        ));
                        if ($brandInfo) {
                            $this->_requestParams['series'] = $seriesInfo['url_path'];
                            $this->_requestParams['brand'] = $brandInfo['url_path'];
                            return true;
                        }
                    }
                }
            }
        }
        $matchType = "/^(ps|pu|lc|mb|sc|sv|bs|hc)$/";
        for ($i = $mainCount-1; $i>=0; $i--) {
            if (preg_match($matchType, $mainParams[$i], $mat)) {
                if (!empty($mat[1])){
                    $this->_requestParams['type'] = $mat[1];
                    $mi++;
                }
            } else if ($series = VehicleV2Interface::getSeriesByUrl(array('url_path'=>$mainParams[$i]))) {
                $this->_requestParams['series'] = $mainParams[$i];
                $mi++;
            } else if ($brand = VehicleV2Interface::getBrandByUrl(array('url_path'=>$mainParams[$i]))) {
                $this->_requestParams['brand'] = $mainParams[$i];
                $mi++;
            } else {
                //包含非法参数，404处理
                header('HTTP/1.1 404 Not Found');
                header("status: 404 Not Found");
            }
        }
        return true;
    }

     /**
     * @desc 解析指定的url得到满足模式的参数列表
     */
    private function _getParamsFromUrl($url) {
        //domain
        $hostInfo = parse_url($url);
        $host = $hostInfo['host'];
        if (preg_match('/^(\w+)\.273\./', $host, $matchs) && !empty($matchs[1])) {
            $this->_requestParams['domain'] = $matchs[1];
        }
        $uri = $hostInfo['path'];

        $uris = explode("/", trim($uri));
        $uriCount = count($uris);

        if ($uriCount > 2) {
            $index = 1;
            $this->_requestParams['page_type'] = 'sale';

            if (in_array($uris[$index], $this->_pageTypes)) {
                $this->_requestParams['page_type'] = $uris[$index];
                $index++;
            }

            //副参解析
            if ($uriCount > 3) { //三层以上才可能有副参，副参一定是倒数第二级
                $subParamStr = $uris[$uriCount - 2];
                $subParams = explode('_', $subParamStr);
                $subCount = count($subParams);
                $si = 0;
                foreach ($this->urlMap['sub_params'] as $urlMap) {
                    if ($si >= $subCount) {
                        break;
                    }

                    $matchStr = "/^" . $urlMap[1] . "(" . $urlMap[2] . ")$/";
                    if (isset($urlMap[4])) {
                        $matchStr = "/^" . $urlMap[1] . "(" . $urlMap[2] . ")" . $urlMap[4] . "$/";
                    }

                    if (preg_match($matchStr, $subParams[$si], $mat)) {
                        $val = ($mat[1] === NULL || $mat[1] === 'undefined') ? $urlMap[3] : $mat[1];
                        $this->_requestParams[$urlMap[0]] = $val;
                        $uris[$uriCount - 2] = false;
                        $si++;
                    }
                }
            }

            $ok = true;
            for ( ; $index <= $uriCount - 2 && $uris[$index]; $index++) {
                if (VEHICLE_VERSION == 'V2') {
                    $info = array();
                } else {
                    $info = UriInfoInterface::getInfoByUrl(array('url_path'=>$uris[$index]));
                }

                if (empty($info)) {
                    if ($index == 1) {
                        $info = LocationInterface::getDistrictByUrl(array('url_path'=>$uris[$index]));
                    }
                    //品牌
                    if (empty($info) && empty($this->_requestParams['brand'])) {
                        if (VEHICLE_VERSION == 'V2') {
                            $info = VehicleV2Interface::getBrandByUrl(array('url_path' => $uris[$index]));
                        } else {
                            $info = VehicleInterface::getBrandByUrl(array('url_path' => $uris[$index]));
                        }
                    }
                    //制造商，为兼容旧URL，不要求制造商必须
                    /* if (empty($info) && empty($this->_requestParams['maker'])) {
                        if (VEHICLE_VERSION == 'V2') {
                            $info = VehicleV2Interface::getMakerByUrl(array('url_path' => $uris[$index]));
                        } else {
                            $info = VehicleInterface::getMakerByUrl(array('url_path' => $uris[$index]));
                        }
                    } */
                    //////VEHICLE_VERSION

                    //车系
                    if (empty($info) && empty($this->_requestParams['series']) && $this->_requestParams['brand']) {
                        if (VEHICLE_VERSION == 'V2') {
                            $info = VehicleV2Interface::getSeriesByUrl(array('url_path' => $uris[$index]));
                        } else {
                            $info = VehicleInterface::getSeriesByUrl(array('url_path' => $uris[$index]));

                        }
                    }
                }

                if ($info) {
                    if (isset($info['city_id'])) {
                        $this->_requestParams['district'] = $uris[$index];
                    } else {
                        if (empty($this->_requestParams['brand'])) {
                            $this->_requestParams['brand'] = $uris[$index];
                        } else {
                            $this->_requestParams['series'] = $uris[$index];
                        }
                    }
                }
            }

            $this->_toShort();
            $this->_mergeExclude($this->_requestParams);
        }

        // 设置默认参数
        foreach ($this->urlMap['sub_params'] as $val) {
            if (!isset($this->_requestParams[$val[0]]) && isset($val[3])) {
                $this->_requestParams[$val[0]] = $val[3];
            }
        }
    }

    public function getRequestParams() {
        //if ($this->_requestParams['pubtime_order'] == 'list') {
        //    $this->_requestParams['order'] = 'td';
        //    $this->_requestParams['onsale'] = 1;
        //}
        return $this->_requestParams;
    }

    /**
     * 生成URL
     * @param array $params    关联数组，参数参考urlMap，使用自定义参数区间时注意将固定区间参数置空（如:使用mix_price不能有price）
     *              - detail  =  true | false    为详情页还是车源图片页
     * @param bool  $edit      是否为修改当前url
     * @return  string  domain参数修改则返回完整url，否则返回uri，修改domain的时候抛弃district参数
     */
    public function createUrl($params, $edit = false) {
        if (!isset($params['page'])) {
            $params['page'] = null;
        }
        if ($edit) {
            $params = array_merge($this->_requestParams, $params);
        }
        if (!empty($params['model']) && (!empty($params['brand']) || !empty($params['series']))) {
            unset($params['brand'], $params['series']);
        }
        if (!empty($params['brand']) && !empty($params['series'])) {
            unset($params['brand']);
        }
        $str = '/';
        $domain = $this->_requestParams['domain'];
        if (!empty($this->_requestParams['partner'])) {
            $domain = $this->_requestParams['partner'] . '.' . $domain;
        }
        if (isset($params['domain']) && $params['domain'] &&
            $params['domain'] !== $this->_requestParams['domain'] && $params['domain'] !== $domain) {
            unset($params['district']);
            $str = 'http://';
            if (!empty($this->_requestParams['partner'])) {
                $str .= "{$this->_requestParams['partner']}.";
            }
            $str .= "{$params['domain']}.273.cn/";
        }
        $this->_mergeExclude($params);
        if ($params) {
            $res = array();
            $subRes = array();
            $mainRes = array();
            foreach ($this->urlMap as $map) {
                if (is_array($map[0])) {
                    foreach ($map as $subMap) {
                        if (isset($subMap[5]) && $subMap[5]) {
                            if (isset($params[$subMap[0]]) && $params[$subMap[0]] &&
                                !(isset($subMap[3]) && $subMap[3] && $subMap[3] == $params[$subMap[0]])) {
                                $subRes[] = $subMap[1] . $params[$subMap[0]];
                            }
                        }
                    }
                } else { //主参
                    if (isset($params[$map[0]]) && $params[$map[0]] &&
                            !(isset($subMap[3]) && $subMap[3] && $subMap[3] == $params[$subMap[0]])) {
                        $mainRes[] = $map[1] . $params[$map[0]];
                    }
                }
            }
            if (empty($mainRes) && empty($subRes)) {
                if (isset($params['car_id']) && $params['car_id']) {
                    $res[] = 'car';
                } else {
                    $res[] = 'car';
                }
            } else {
                 if (!empty($mainRes) && !isset($params['car_id'])) {
                     $res[] = implode('_', $mainRes);
                 }
                 if (!empty($subRes) && !isset($params['car_id'])) {
                     $res[] = implode('_', $subRes);
                 }
            }

            $str .= implode('/', $res) . '/';
            //尾参，详情页或者图片页
            if (isset($params['car_id']) && $params['car_id']) {
                if (!isset($params['detail'])) {
                    $str .= 'pic_';
                }
                $str .= $params['car_id'];
                if (isset($params['pic_id']) && $params['pic_id']) {
                    $str .= '_' . $params['pic_id'];
                }
                $str .= '.html';
            }
        }
        //简单替换原按发布时间排序参数为list
        //$str = str_replace('os1_otd', 'list', $str);
        $delKeyword = isset($params['del_keyword']) ? $params['del_keyword'] : false;
        if (!$delKeyword) {
            if (empty($params['car_id'])) {
                $keyword = RequestUtil::getGET('keywords', '');
                if ($keyword) {
                    $isList = RequestUtil::getGET('is_list', '');
                    if (!$isList) {//首页古来的关键字为Gbk编码
                        $keyword = trim(iconv("GBK", "UTF-8", $keyword));
                        $isList = 1;
                    }
                    $str .= "?keywords={$keyword}";
                    if ($isList) {
                        $str .= "&is_list={$isList}";
                    }
                }
            }
        }
        return $str;
    }

    /**
     * 根据表单参数解析出参数
     * @param  string $str
     * @return array  $requstParams
     */
    public function parseUrlByGetParams($str) {
        $res = array(
            'page_type'  => 'sale',
        );
        parse_str($str, $urlParams);
        //城市信息解析
        if ($urlParams['province']) {
            if ($urlParams['city']) {
                $info = LocationInterface::getCityById(array('city_id' => $urlParams['city']));
            } else {
                $info = LocationInterface::getProvinceById(array('province_id' => $urlParams['province']));
            }
            if ($info && $info['domain']) {
                $res['domain'] = $info['domain'];
            }
        }
        if (empty($res['domain'])) {
            $res['domain'] = 'www';
        }

        //兼容旧的品牌参数
        $urlParams['make'] = strtolower($urlParams['make']);
        if ($urlParams['make']) {
            $brandInfo = VehicleInterface::getBrandByMakeCode(array(
                'make_code' => $urlParams['make'],
            ));
            $res['brand']  = $brandInfo['path'];
            $res['series'] = strtolower($urlParams['family']);
        }
        //解析价格参数
        if ($urlParams['price']) {
            if ($urlParams['price'] == '40-999') {
                $res['price'] = 8;
            } else {
                list($res['min_price'], $res['max_price']) = explode('-', $urlParams['price']);
            }
        }

        //车型库迁移后，合作站搜索功能参数
        if ($urlParams['type_id'] && isset($this->_replaceConfig['type'][$urlParams['type_id']])) {
            $res['type'] = $this->_replaceConfig['type'][$urlParams['type_id']];
        }
        if ($urlParams['brand_id']) {
            $brandInfo = VehicleV2Interface::getBrandById(array(
                    'brand_id' => $urlParams['brand_id'],
            ));
            $res['brand']  = $brandInfo['url_path'];
        }
        if ($urlParams['series_id']) {
            $brandInfo = VehicleV2Interface::getSeriesById(array(
                    'series_id' => $urlParams['series_id'],
            ));
            $res['series']  = $brandInfo['url_path'];
        }

        if (empty($res['min_price']) && $urlParams['min_price']) {
            $res['min_price'] = (int) $urlParams['min_price'];
        }
        if (empty($res['max_price']) && $urlParams['max_price']) {
            $res['max_price'] = (int) $urlParams['max_price'];
        }

        $oldPrice = sprintf('%02d%02d', $res['min_price'], $res['max_price']);
        if (empty($res['price']) && isset($this->_replaceConfig['price'][$oldPrice]) && !is_array($this->_replaceConfig['price'][$oldPrice])) {
            $res['price'] = $this->_replaceConfig['price'][$oldPrice];
            unset($res['min_price']);
            unset($res['max_price']);
        }
        return $res;
    }

    /**
     *参数互斥处理
     *@param unknown_type $params
     */
    protected function _mergeExclude(&$params) {
        foreach ($this->excludeMap as $name => $vals) {
            if (isset($params[$name])) {
                foreach ($vals as $key => $val){
                    if (is_numeric($key)) {
                        if ($val != $name) {
                            unset($params[$val]);
                        }
                    }
                }
            }
        }
    }

}