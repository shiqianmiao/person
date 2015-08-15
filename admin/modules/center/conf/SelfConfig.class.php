<?php
/**
 * @brief 简介：本模块配置文件
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月19日 下午4:57:06
 */
class SelfConfig {
    /**
     * @brief 老后台的图片系统月嫂头像配置
     * @author 缪石乾<miaoshiqian@iyuesao.com>
     * @date 2015年2月1日 下午12:00:21
     */
    public static $FACE_URL_OPT = array(
        'width'  => 80,
        'height' => 80,
        'mode'   => 'crop',
    );
    
    /**
     * @brief 帮助中心发布文章时、根据大类ID来配置发布页类型
     * @author 缪石乾<miaoshiqian@iyuesao.com>
     * @date 2015年1月27日 下午4:20:47
     * @ key（发布页类型) => array(xx,xx,xx,xx) （大类id数组)
     */
    public static $HELP_PUB_TYPE = array(
        '1' => array(1), //辅食类的发布页 help/add_1.php
        '2' => array(3), //儿歌类的发布页 help/add_2.php
        '3' => array(2, 4, 5, 6), //其余类的发布页 help/add_3.php
    );
    public static $HELP_CAT_PUB_TYPE = array(
        '1' => '辅食类发布页',
        '2' => '儿歌类发布页',
        '3' => '通用发布页',
    );
    
    /**
     * @brief 允许上传儿歌的格式配置
     */
    public static $ALLOW_SONG_EXT = array('.mp3');
    
    /**
     * @brief 允许上传儿歌的最大大小限制、暂时定位20M
     */
    public static $ALLOW_SONG_SIZE = 20971520;
    
    /**
     * @brief 儿歌音频文件在七牛上面的key前缀
     */
    public static $SONG_PRE = 'audio';
    
    /**
     * @brief 上传控件的预览图显示尺寸
     */
    public static $UPLOAD_COVER = array(
            'width'  => 120,
            'height' => 90,
            'mode'   => 1,
    );
    
    /**
     * @brief 帮助中心列表封面图显示尺寸
     */
    public static $HELP_LIST_COVER = array(
        'width'  => 80,
        'height' => 80,
        'mode'   => 1,
    );
    
    /**
     * @brief 档期类型对于的休假时长
     * @key => 档期里面的休假类型。1：整体、2：上午、3：下午
     */
    public static $REST_TYPE_LONG = array(
        '1' => 1,
        '2' => 0.5,
        '3' => 0.5,
    );
    
    /**
     * @brief 婴儿类型、1：单胞胎、2：双胞胎
     */
    public static $BABY_TYPE = array(
        '1' => '单胞胎',
        '2' => '双胞胎',
    );
    
    /**
     * @brief 客户名族配置
     */
    public static $CUSTOMER_NATION = array(
        '1' => '汉族',
        '2' => '少数名族',
    );
    
    /**
     * @brief 育儿嫂薪资范围配置、添加育儿嫂订单页面枚举配置
     */
    public static $PARENTAL_SALARY = array(
        '1' => '3000 ~ 4000',
        '2' => '4000 ~ 6000',
        '3' => '6000以上',
    );
    
    /**
     * @brief 育儿嫂工作年限配置、添加育儿嫂订单页面。工作年限枚举配置
     */
    public static $PARENTAL_WORK_AGE = array(
        '1' => '3年以内',
        '2' => '3~5年',
        '3' => '5~8年',
        '4' => '8年以上',
    );

    /**
     * @brief 帮助中心大类类型枚举配置
     */
    const HELP_CAT_TYPE_PARENTAL = 1;
    const HELP_CAT_TYPE_YUESAO   = 2;
    public static $HELP_CAT_TYPE = array(
        '1' => '育儿嫂大类',
        '2' => '月嫂大类',
    );

    /**
     * @brief 月嫂评分枚举说明
     */
    public static $YUESAO_COMMENT_CAT = array(
        '1' => '专业能力', //对应点评表中的 yuesao_1字段的评分
        '2' => '干活勤快',
        '3' => '安全意识',
        '4' => '注意卫生',
        '5' => '沟通表达',
    );
    /**
     * @brief 育儿嫂评分枚举说明
     */
    public static $PARENTAL_COMMENT_CAT = array(
        '1' => '辅食制作', //对应点评表中的 parental_1字段的评分
        '2' => '亲子互动',
        '3' => '安全意识',
        '4' => '注意卫生',
        '5' => '遵守规则',
    );

    /**
     * @brief 点评的status常量配置
     */
    const COMMENT_STATUS_OK = 1; //设置为正常状态
    const COMMENT_STATUS_DELETE = -1; //设置为删除状态
    const COMMENT_STATUS_SELF = 2; //设置仅客户自己可见状态

    /**
     * @brief 微信订单状态
     */
    const WEIXIN_ORDER_NORMAL = 1; //正常订单
}