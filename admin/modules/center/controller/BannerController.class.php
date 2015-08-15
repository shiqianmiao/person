<?php

/**
 * @brief Banner管理控制器
 * @author 缪石乾<miaoshiqian@anxin365.com>
 */
class BannerController extends BcController {
    /**
     * @brief Banner添加页面
     */
    public function defaultAction() {
        $id = Request::getGET('id');
        $isEdit = !empty($id) ? 1 : 0;
        if ($isEdit) {
            try {
                $bannerInfo = array();
                if (!empty($bannerInfo) && is_array($bannerInfo)) {
                    //banner图片信息
                    $image = array();
                    if (!empty($bannerInfo['path'])) {
                        $photo[] = array(
                            'url'     => QiNiu::formatImageUrl($bannerInfo['path']),
                            'org_url' => QiNiu::formatImageUrl($bannerInfo['path']),
                            'key'     => $bannerInfo['path'] ? $bannerInfo['path'] : '',
                        );
                    }
                    $formData = array(
                        'title1' => Util::getFromArray('title1', $bannerInfo, ''),
                        'brief'  => Util::getFromArray('brief', $bannerInfo, ''),
                        'image'  => $image,
                    );
                }
            } catch (Exception $e) {
                $this->errorAction(array($e->getMessage()));
            }
        }
        $formData['id'] = $id;
        $formObj = new BannerForm($formData);
        $data = array(
            'form'   => $formObj->getForm(),
            'info'   => $formData,
            'isEdit' => $isEdit,
        );
        $content = $this->view->fetch('banner/add.php', $data);
        echo $content;
    }

    /**
     * @brief 添加或编辑banner信息
     */
    public function subBannerAction() {
        $image    = json_decode(Request::getPOST('banner'), true); //banner照片
        $userInfo = $this->userInfo['user'];
        if (empty($userInfo)) {
            $this->errorAction(array('获取登陆用户信息失败！不允许添加！'));
        }

        $formObj  = new BannerForm();
        $form     = $formObj->getForm();
        $validate = $form->validate(); //后端验证
        $postInfo = $form->getValue();
        $id       = Util::getFromArray('id', $postInfo, 0);
        if (!$validate) {
            $data = array(
                'form'   => $form,
                'info'   => $postInfo,
                'isEdit' => !empty($id) ? 1 : 0,
            );
            $content = $this->view->fetch('worker/add.php', $data);
            echo $content;
            exit;
        }
        try {
            $info = array(
                'id'     => Util::getFromArray('id', $postInfo, 0),
                'title1' => Util::getFromArray('title1', $postInfo, ''),
                'brief'  => Util::getFromArray('brief', $postInfo, ''),
                'path'   => !empty($image['0']['key']) ? $image['0']['key'] : '', //图片key
            );
            BannerInterface::add(array('info' => $info, 'user' => $userInfo));
            Response::redirect('/center/banner/list');
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }

    /**
     * @brief banner列表
     */
    public function listAction() {
        $data = array(
            'datagrid'  => $this->getDataGrid(),
            'statusMap' => BannerConfig::$STATUS,
        );
        $content = $this->view->fetch('banner/list.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getDataGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/banner/ajaxGetData/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '图片', 'width:15%');
        $dataGrid->setCol('field2', '标题', 'width: 15%');
        $dataGrid->setCol('field3', '简介', 'width:25%');
        $dataGrid->setCol('field4', '状态', 'width:10%');
        $dataGrid->setCol('field5', '操作', 'width:25%');
        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }

    /**
     * @brief 过滤数据处理
     */
    public function fieldFormat($name, $row) {
        return empty($row[$name]) && $row[$name] != 0 ? '' : $row[$name];
    }

    public function ajaxGetDataAction() {
        $dataGrid = $this->getDataGrid();
        $this->currentPage = (int) Request::getGET('page', 1);
        $allCount = 0;
        try {
            $offset  = ($this->currentPage - 1) * $this->pageSize;
            //构建参数
            $status = Request::getGET('status', 1);
            $filters = array(array('status', '=', $status));

            $params = array(
                'limit'   => $this->pageSize,
                'offset'  => $offset,
                'filters' => $filters,
            );
            $bannerInfo = BannerInterface::getList($params);
            $allCount = BannerInterface::getCount($params);

            $list = array();
            if (!empty($bannerInfo) && is_array($bannerInfo)) {
                $code = $this->userInfo['code'];
                foreach ($bannerInfo as $banner) {
                    $image = !empty($banner['path']) ? QiNiu::formatImageUrl(Util::getFromArray('path', $banner, '')) : '';
                    $row['field1'] = '<img src="'.$image.'" width="120"/>';
                    $row['field2'] = $banner['title1'];
                    $row['field3'] = $banner['brief'];
                    $row['field4'] = isset(BannerConfig::$STATUS[$banner['status']]) ? BannerConfig::$STATUS[$banner['status']] : '';
                    $row['field5'] = '';
                    $list[] = $row;
                }
            }
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }

        $dataGrid->setData($list);
        //设置分页信息
        $maxSize = $allCount > $this->maxSize ? $this->maxSize : $allCount;
        $dataGrid->setPager($maxSize, $this->currentPage, $this->pageSize);
        $data = $dataGrid->getData();
        echo json_encode(array('data' => $data));
    }
}