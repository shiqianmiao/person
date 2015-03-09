<?php

require_once WEB_V3 . '/common/WebBasePage.class.php' ;

/**
 *
 * @author    miaoshiqian
 * @since     2014-7-7
 * @desc      
 */
class BlogPage extends WebBasePage{
    /**
     *@desc blog Page
     */
    function defaultAction() {

        $staHtml .= '';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));

        $this->render(array(
            'staHtml' => $staHtml,
        ), 'blog.php');

    }
}

