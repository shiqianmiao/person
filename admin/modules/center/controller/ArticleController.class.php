<?php

/**
 * @brief 帮助中心管理
 * @author chenchaoyang@iyuesao.com
 * @since  2015-01-11
 */
class ArticleController extends BcController {
    public function init() {
    }
    
    /**
     * @brief 帮助中心文章发布页面
     */
    public function defaultAction() {
        $data = array();
        $content = $this->view->fetch('article/add.php', $data);
        echo $content;
    }
    
    /**
     * @brief 帮助中心文章列表页面
     */
    public function ListAction() {
        $data = array();
        $content = $this->view->fetch('article/article_list.php', $data);
        echo $content;
    }
    
    /**
     * @brief 帮助中心文章类别管理页面
     */
    public function CategoryAction() {
        $data = array();
        $content = $this->view->fetch('article/category.php', $data);
        echo $content;
    }
}
