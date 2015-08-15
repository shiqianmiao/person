<?php
/**
 * @brief banner逻辑处理类
 * @author miaoshiqian
 */
require_once API_PATH . '/impl/BaseImpl.class.php';

class BannerImpl extends BaseImpl {
    
    /**
     * @brief 添加banner
     */
    public static function add($params) {
        $info = Util::getFromArray('info', $params, array());
        $user = Util::getFromArray('user', $params, array());
        if (empty($info) || empty($user)) {
            throw new Exception('给接口banner.add的参数不对');
        }
        $id = Util::getFromArray('id', $info, 0);
        $time = time();
        $info['update_time'] = $time;
        if (empty($id)) {
            //添加
            $info['create_time'] = $time;
            $info['create_user'] = $user['id'];
            $info['status']      = BannerModel::STATUS_NORMAL;
        }
        $model = new BannerModel();
        return $id ? $model->update($info, array(array('id', '=', $id))) : $model->insert($info);
    }

    /**
     * @brief 获取列表
     */
    public static function getList($params) {
        $field   = Util::getFromArray('field', $params, '*');
        $limit   = Util::getFromArray('limit', $params, 0);
        $offset  = Util::getFromArray('offset', $params, 0);
        $order   = Util::getFromArray('order', $params, array('weight' => 'desc', 'id' => 'desc'));
        $filters = Util::getFromArray('filters', $params, '');

        $model = new BannerModel();
        return $model->getAll($field, $filters, $order, $limit, $offset);
    }

    /**
     * @brief 获取数量
     */
    public static function getCount($params) {
        $filters = Util::getFromArray('filters', $params, '');
        $model = new BannerModel();
        return $model->getCount($filters);
    }
}
