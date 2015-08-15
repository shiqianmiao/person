<?php
/**
 * @brief 简介：帮助中心类
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月15日 下午6:26:27
 */

class HelpController extends BcController {
    /**
     * @brief 当前分页
     */
    public $currentPage = 1;

    public function init() {
    }

    /**
     * @brief 获取并展示帮助中心大类列表
     */
    public function defaultAction() {
        $data = array(
            'datagrid' => $this->getCatGrid(),
            'catType'  => SelfConfig::$HELP_CAT_TYPE,
            'pubType'  => SelfConfig::$HELP_CAT_PUB_TYPE,
        );
        $content = $this->view->fetch('help/index.php', $data);
        echo $content;
    }

    /**
     * @brief 设置表格头
     */
    public function getCatGrid() {
        $dataGrid = new DataGridWidget();
        $dataGrid->setUrl('/center/help/ajaxGetCat/?' . $this->getQueryString());
        $dataGrid->setCol('field1', '大类名称', 'width:10%');
        $dataGrid->setCol('field2', '大类类型', 'width: 10%');
        $dataGrid->setCol('field3', '大类简介', 'width:10%');
        $dataGrid->setCol('field4', '操作', 'width:15%');
        $dataGrid->setFormatCallback(array($this, 'fieldFormat'));
        return $dataGrid;
    }

    /**
     * @brief 过滤数据处理
     */
    public function fieldFormat($name, $row) {
        return empty($row[$name]) && $row[$name] != 0 ? '' : $row[$name];
    }

    public function ajaxGetCatAction() {
        $dataGrid = $this->getCatGrid();
        $this->currentPage = (int)Request::getGET('page', 1);
        $type = (int)Request::getGET('type', SelfConfig::HELP_CAT_TYPE_PARENTAL);
        $allCount = 0;
        try {
            $client = Rpc::client('help.category');
            $offset = ($this->currentPage - 1) * $this->pageSize;
            $params = array(
                'filters' => array(array('type', '=', $type)),
                'limit'   => $this->pageSize,
                'offset'  => $offset,
            );
            $info = $client->getList($params);
            $allCount = $client->getCount(array('filters' => $params['filters']));
            $list = array();
            if (!empty($info) && is_array($info)) {
                foreach ($info as $cat) {
                    $row['field1'] = Util::getFromArray('name', $cat, '');
                    $row['field2'] = isset(SelfConfig::$HELP_CAT_TYPE[$cat['type']]) ? SelfConfig::$HELP_CAT_TYPE[$cat['type']] : '';
                    $row['field3'] = Util::getFromArray('desc', $cat, '');
                    $row['field4'] = '<a href="javascript:;" data-action-type="edit_cat" data-id="' . $cat['id'] . '">编辑</a>';
                    $row['field4'] .= ' | <a target="_blank" href="/center/help/articleList/?cat_id='.$cat['id'].'">进入列表</a>';
                    $list[] = $row;
                }
            }
            $dataGrid->setData($list);
            //设置分页信息
            $maxSize = $allCount > $this->maxSize ? $this->maxSize : $allCount;
            $dataGrid->setPager($maxSize, $this->currentPage, $this->pageSize);
            $data = $dataGrid->getData();
            echo json_encode(array('data' => $data));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
    }
    
    /**
     * @brief 文章列表
     */
    public function articleListAction() {
        $catId = Request::getGET('cat_id');
        $infoList = array();
        try {
            $client = Rpc::client('help.helper');
            $list = $client->getArticleByCat(array('cat_id' => $catId));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
        if (!empty($list) && is_array($list)) {
            foreach ($list as $li) {
                $infoList[] = array(
                    'id'    => Util::getFromArray('id', $li, ''),
                    'title' => Util::getFromArray('title', $li, ''),
                    'brief' => Util::getFromArray('brief', $li, ''),
                    'cover' => !empty($li['cover']) ? QiNiu::formatImageUrl($li['cover'], SelfConfig::$HELP_LIST_COVER) : '',
                );
            }
        }
        $data = array(
            'catId' => $catId,
            'info'  => $infoList,
        );
        $content = $this->view->fetch('help/list.php', $data);
        echo $content;
    }
    
    /**
     * @brief 文章详情
     */
    public function articleDetailAction() {
        $articleId = Request::getGET('id');
        if (empty($articleId)) {
            $this->errorAction(array('文章ID缺失、无法查看！'));
        }
        
        $artInfo = array();
        try {
            $client = Rpc::client('help.helper');
            $info = $client->getArticleById(array('id' => $articleId));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
        
        if (!empty($info) && is_array($info)) {
            $artInfo = array(
                'id'    => Util::getFromArray('id', $info, ''),
                'title' => Util::getFromArray('title', $info, ''),
                'brief' => Util::getFromArray('brief', $info, ''),
                'cont'  => !empty($info['content']) ? htmlspecialchars_decode($info['content']) : '',
            );
        }
        
        $content = $this->view->fetch('help/detail.php', array('artInfo' => $artInfo));
        echo $content;
    }
    
    /**
     * @brief 添加文章、这里有3个发布页模板、需要根据大类id的配置来判断某个大类id进入某个模板
     */
    public function addArticleAction() {
        $catId = Request::getGET('cat_id');
        $tpl = 'add_';
        $client = Rpc::client('help.category');
        $catInfo = $client->getCatById(array('id' => $catId));
        if (empty($catInfo['pub_type'])) {
            $this->errorAction(array("大类ID为{$catId}的大类还没有选择发布页面类型,请先编辑该大类后才能发布内容！"));
        }
        $tpl .= $catInfo['pub_type'];
        $data = array(
            'catId' => $catId,
        );
        
        $content = $this->view->fetch("help/{$tpl}.php", $data);
        echo $content;
    }
    
    /**
     * @brief 编辑文章、这里有3个发布页模板、需要根据大类id的配置来判断某个大类id进入某个模板
     */
    public function editArticleAction() {
        $artId = Request::getGET('id');
        if (empty($artId)) {
            $this->errorAction(array('缺少文章ID'));
        }
        
        try {
            $client = Rpc::client('help.helper');
            $info = $client->getArticleById(array('id' => $artId));
        } catch (Exception $e) {
            $this->errorAction(array($e->getMessage()));
        }
        
        if (empty($info) || !is_array($info)) {
            $this->errorAction(array('该文章不存在'));
        }
        
        //封面图片信息
        $cover = array();
        if (!empty($info['cover'])) {
            $cover[] = array(
                'url'     => QiNiu::formatImageUrl($info['cover'], SelfConfig::$UPLOAD_COVER),
                'org_url' => QiNiu::formatImageUrl($info['cover']),
                'key'     => $info['cover'] ? $info['cover'] : '',
            );
        }//print_r(json_decode($info['food'], true));exit;
        $artInfo = array(
            'id'      => Util::getFromArray('id', $info, 0),
            'cat_id'  => Util::getFromArray('category_id', $info, 0),
            'title'   => Util::getFromArray('title', $info, ''),
            'brief'   => Util::getFromArray('brief', $info, ''),
            'keyword' => Util::getFromArray('keyword', $info, ''),
            'month_b' => Util::getFromArray('fit_month_b', $info, ''),
            'month_e' => Util::getFromArray('fit_month_e', $info, ''),
            'cont'    => Util::getFromArray('content', $info, ''),
            'audio'   => Util::getFromArray('audio_url', $info, ''),
            'cover'   => !empty($cover) ? json_encode($cover) : '',
            'food'    => !empty($info['food']) ? json_decode($info['food'], true) : array(),
        );
        //根据大类id来根据配置里面的情况。来选择发布页类型
        $tpl = 'add_';
        $client = Rpc::client('help.category');
        $catInfo = $client->getCatById(array('id' => $artInfo['cat_id']));
        if (empty($catInfo['pub_type'])) {
            $this->errorAction(array("大类ID为{$artInfo['cat_id']}的大类还没有选择发布页面类型,请先编辑该大类后才能发布内容！"));
        }
        $tpl .= $catInfo['pub_type'];
        $data = array(
            'edit'    => 1,
            'artInfo' => $artInfo,
            'catId'   => $artInfo['cat_id'],
        );
    
        $content = $this->view->fetch("help/{$tpl}.php", $data);
        echo $content;
    }
    
    /**
     * @brief 提交添加文章的表单、和编辑表单的提交
     */
    public function ajaxSubmitArticleAction() {
        $isEdit  = Request::getPOST('is_edit', 0); //是否是编辑文章的提交
        $artId   = Request::getPOST('art_id', 0); //当为编辑的时候、这个字段放文章id
        $food    = Request::getPOST('food');//食材
        $catId   = Request::getPOST('cat_id', 0);//大类id
        $title   = Request::getPOST('title', '');//标题
        $brief   = Request::getPOST('brief', '');//简介
        $keyword = Request::getPOST('keyword', '');//关键字
        $monthB  = Request::getPOST('month_b', 0); //适合月份开始
        $monthE  = Request::getPOST('month_e', 0); //适合月份结束
        $cont    = Request::getPOST('cont', '', true); //内容主题
        $cont    = htmlspecialchars($cont);
        $audio   = Request::getPOST('audio_url', ''); //儿歌url
        $cover   = Request::getPOST('cover', ''); //封面图
        
        //把存储到七牛上面的图片的key保存到数据库中
        if (!empty($cover)) {
            $coverInfo = json_decode($cover, true);
            $cover = !empty($coverInfo['0']['key']) ? $coverInfo['0']['key'] : '';
        }
        
        //给接口整理的文章信息
        $info = array(
            'title'       => $title,
            'fit_month_b' => $monthB,
            'fit_month_e' => $monthE,
            'cover'       => $cover,
            'brief'       => $brief,
            'food'        => $food,
            'content'     => $cont,
            'keyword'     => $keyword,
            'audio_url'   => $audio,
        );
        if (!$isEdit) {//添加才传得参数字段
            $info['category_id'] = $catId;
        } else {
            //编辑才传得参数
            $info['id'] = $artId;
        }
        $params = array(
            'info'    => $info,
            'user_id' => '10', //TODO 这里需要替换为真实用户的id,即为登陆后台的操作人得id
        );
        
        //执行添加
        try {
            $client = Rpc::client('help.helper');
            $isEdit ? $client->updateHelpArticle($params) : $client->addHelpArticle($params);
            $msg = $isEdit ? '更新成功啦！' : '发布成功啦！';
            exit(json_encode(array('errorCode' => 0, 'msg' => $msg)));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief 添加帮助大类的弹层
     */
    public function iframeAddCatAction() {
        $data = array(
            'pubType' => SelfConfig::$HELP_CAT_PUB_TYPE,
            'catType' => SelfConfig::$HELP_CAT_TYPE,
        );
        $content = $this->view->fetch('help/iframe_add_cat.php', $data);
        echo $content;
    }
    
    /**
     * @brief 编辑帮助大类的弹层
     */
    public function iframeEditCatAction() {
        $id = Request::getGET('id');
        if (empty($id) || !is_numeric($id)) {
            $content = $this->view->fetch('widget/iframe_error.php', array('msg' => '缺少参数无法编辑！'));
            echo $content;
            exit;
        }
        
        try {
            $client = Rpc::client('help.category');
            $catInfo = $client->getCatById(array('id' => $id));
            $info = array(
                'name' => Util::getFromArray('name', $catInfo, ''),
                'desc' => Util::getFromArray('desc', $catInfo, ''),
                'id'   => Util::getFromArray('id', $catInfo, 0),
                'type' => Util::getFromArray('type', $catInfo, 0),
                'pub_type' => Util::getFromArray('pub_type', $catInfo, 0),
            );
            $data = array(
                'info' => $info,
                'edit' => 1,
                'catType' => SelfConfig::$HELP_CAT_TYPE,
                'pubType' => SelfConfig::$HELP_CAT_PUB_TYPE,
            );
            $content = $this->view->fetch('help/iframe_add_cat.php', $data);
            echo $content;
        } catch (Exception $e) {
            $content = $this->view->fetch('widget/iframe_error.php', array('msg' => $e->getMessage()));
            echo $content;
            exit;
        }
    }
    
    /**
     * @brief ajax添加或者编辑 帮助中心大类
     * @$edit  如果$edit是1，说明是编辑大类的提交,否则为添加
     */
    public function ajaxSubCatAction() {
        //获取参数并判断有效性
        $id   = Request::getPOST('id', 0);
        $edit = Request::getPOST('edit', 0);
        $name = Request::getPOST('name', '');
        $desc = Request::getPOST('desc', '');
        $type = Request::getPOST('type', 0);
        $pubType = Request::getPOST('pub_type', 0);

        if (empty($type)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '大类类别必须选择！')));
        }
        if (empty($pubType)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '发布页面类型必须选择！')));
        }
        if (empty($name)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '大类名称不能为空！')));
        }
        //编辑的id验证
        if ($edit && !$id) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '丢失大类ID、无法完成编辑！')));
        }
        //判断参数长度
        $nameLen = String::strlen_utf8($name);
        $descLen = !empty($desc) ? String::strlen_utf8($desc) : 0;
        if ($nameLen > 20) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '名称不能超过20个字！')));
        }
        if ($descLen > 40) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '描述不能超过40个字！')));
        }
        //执行添加
        try {
            $client = Rpc::client('help.category');
            $params = array(
                'username' => $this->userInfo['user']['username'],
                'name'     => $name,
                'desc'     => $desc,
                'type'     => $type,
                'pub_type' => $pubType,
            );
            
            $edit ? $client->editCatById(array('info' => $params, 'id' => $id)) : $client->addCategory($params);
            $msg = $edit ? '编辑大类成功啦！' : '添加大类成功啦!';
            exit(json_encode(array('errorCode' => 0, 'msg' => $msg)));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief ajax删除大类信息
     */
    public function ajaxDelCatAction() {
        $id = Request::getPOST('id', 0);
        if (empty($id) || !is_numeric($id)) {
            exit(json_encode(array('errorCode' => 1, 'msg' => '大类ID缺失、无法删除！')));
        }
        
        try {
            $client = Rpc::client('help.category');
            $client->delCatById(array('id' => $id));
            
            exit(json_encode(array('errorCode' => 0, 'msg' => '删除大类成功！')));
        } catch (Exception $e) {
            exit(json_encode(array('errorCode' => 1, 'msg' => $e->getMessage())));
        }
    }
    
    /**
     * @brief 弹层上传儿歌
     */
    public function iframeUpSongAction() {
        $content = $this->view->fetch('help/iframe_up_song.php');
        echo $content;
    }
    
    /**
     * @brief 上传儿歌表单提交
     */
    public function uploadSongAction() {
        $file = $_FILES['upfile']['tmp_name'];
        $size = $_FILES['upfile']['size'];
        $ext = strtolower(strrchr($_FILES['upfile']['name'], '.'));
        
        //2M图片限额
        if (!in_array($ext, SelfConfig::$ALLOW_SONG_EXT)) {
            exit('音频格式不符合');
        }
        if ($size > SelfConfig::$ALLOW_SONG_SIZE) {
            exit('音频不能超出20M');
        }
        if (empty($file)) {
            exit('文件名不存在');
        }
        
        try {
            $result = QiNiu::upLocalFile($file, SelfConfig::$SONG_PRE, '.mp3');
        } catch (Exception $e){
            $err = $e->getMessage();
        }
        $results = array(
            'url' => $result['key'] ? $result['key'] : '',
            'code' => isset($err) ? 500 : 200,
            'msg'  => isset($err) ? $err : '',
        );
        
        if ($results['code'] === 200) {
            $url = QiNiu::formatImageUrl($results['url']);
            $content = $this->view->fetch('help/iframe_up_song.php', array('url' => $url));
            echo $content;
        } else {
            exit($results['msg']);
        }
    }
}