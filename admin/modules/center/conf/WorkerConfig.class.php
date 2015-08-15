<?php
/**
 * @author 缪石乾<miaoshiqian@anxin365.com>
 * @brief 简介：服务者配置
 * @date 15/3/19
 * @time 下午4:47
 */
class WorkerConfig {
    /**
     * @brief 性别配置
     */
    const SEX_BOY = 1;
    const SEX_GIRL = 2;
    public static $SEX = array(
        self::SEX_BOY  => '男',
        self::SEX_GIRL => '女',
    );

    /**
     * @brief 服务者服务类型配置
     */
    const WORKER_BABY = 1; //育儿嫂
    const WORKER_MONTH = 2; //月嫂
    public static $WORKER_TYPE = array(
        self::WORKER_BABY  => '育儿嫂',
        self::WORKER_MONTH => '月嫂',
    );

    /**
     * @brief 服务者学历配置
     */
    const PRIMARY = 1; //小学
    const JUNIOR = 2; //初中
    const HIGH = 3; //高中
    const VOCATION = 4; //职业高中
    const JUNIOR_COLLEGE = 5; //大专
    const COLLEGE = 6; //本科
    const GRADUATE = 7; //研究生
    public static $EDUCATION = array(
        self::PRIMARY  => '小学',
        self::JUNIOR => '初中',
        self::HIGH => '高中',
        self::VOCATION => '职高',
        self::JUNIOR_COLLEGE => '大专',
        self::COLLEGE => '本科',
        self::GRADUATE => '研究生',
    );

    /**
     * @brief 服务者照看过的孩子数量配置
     */
    const TAKE_ONE = 1;
    const TAKE_TWO = 2;
    const TAKE_THREE = 3;
    const TAKE_FOUR = 4;
    const TAKE_FIVE = 5;
    public static $TAKE_COUNT = array(
        self::TAKE_ONE  => '0 ~ 10',
        self::TAKE_TWO => '11 ~ 30',
        self::TAKE_THREE => '31 ~ 50',
        self::TAKE_FOUR => '51 ~ 100',
        self::TAKE_FIVE => '100以上',
    );

    /**
     * @brief 服务者手机型号配置
     */
    const ANDROID = 1;
    const IOS = 2;
    const OTHER = 3;
    public static $MOBILE_MODEL = array(
        self::ANDROID => '安卓',
        self::IOS => 'IOS（苹果）',
        self::OTHER => '其他',
    );

    /**
     * @brief 服务者状态配置
     */
    const STATUS_REGISTER = 0; //在册
    const STATUS_SIGN = 1; //已签约
    public static $WORKER_STATUS = array(
        self::STATUS_REGISTER => '在册服务者',
        self::STATUS_SIGN => '已签约服务者',
    );

    /**
     * @brief 服务者各类图片类型配置
     */
    const TYPE_ID_FRONT = 1; //1：身份证正面
    const TYPE_ID_BACK = 2; //2：身份证背面
    const TYPE_WORK_IMG = 3; //3：工作照
    const TYPE_HEALTH_PROVE = 4; //4：体检报告图片
    const TYPE_PROLACTIN = 5; //5：催乳师证
    const TYPE_PARENTAL_PROVE = 6; //6：育儿嫂证
    const TYPE_HITH_PARENTAL_PROVE = 7; //7：高级育儿嫂证
    const TYPE_YUER_PROVE = 8; //8：育婴师证
    const TYPE_CONTRACT_IMG = 9; //9：合同照片
}