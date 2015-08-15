<?php
/**
 * @brief 简介：整个bc后台所有模块都共用的枚举值配置文件
 * @attention 注意：在您修改这里的配置文件时、记得通知各个模块
 * @author 缪石乾<miaoshiqian@iyuesao.com>
 * @date 2015年1月30日 下午2:45:01
 */

class BcEnumerateConfig {
    /**
     * @brief 育儿嫂订单status的枚举值配置
     * @value 配置含义：status的值 => status的含义
     */
    const PARENTAL_ORDER_STATUS_SERVE = 5; //服务中得育儿嫂订单
    public static $ORDER_PARENTAL_STATUS = array(
        '1'  => '匹配育儿嫂中',
        '2'  => '预约面试中',
        '3'  => '签合同中',
        '4'  => '已签合同',
        '5'  => '正式服务中',
        '6'  => '订单结束',
        '-1' => '订单关闭',
    );

    /**
     * @brief 月嫂订单status的枚举值配置
     * @value 配置含义：status的值 => status的含义
     */
    const YUESAO_ORDER_STATUS_SERVE = 5; //服务中得月嫂订单
    public static $ORDER_YUESAO_STATUS = array(
        '1'  => '匹配月嫂中',
        '2'  => '预约面试中',
        '3'  => '签合同中',
        '4'  => '已签合同',
        '5'  => '正式服务中',
        '6'  => '订单结束',
        '-1' => '订单关闭',
    );
    
    /**
     * @brief 订单预约面试状态status的枚举值配置
     */
    const AUDITION_STATUS_DEFAULT = 0; //刚提交的预约面试、还没由客服定过时间地点。
    const AUDITION_STATUS_DOING   = 1; //已经提交了预约面试信息了、正在等待提交面试结果
    const AUDITION_STATUS_PASS    = 2; //预约面试通过
    const AUDITION_STATUS_UNPASS  = 3; //预约面试不通过
    
    /**
     * @brief worker表状态
     */
    const WORKER_STATUS_SIGN = 1; //正常签约状态
    const WORKER_STATUS_UNSIGN = -1; //解约状态
    
    /**
     * @brief 合同状态
     */
    public static $CONTRACT_STATUS = array(
        '1' => '合同已快递',
        '2' => '客户已确认',
        '3' => '合同已回收',
    );
    
    /**
     * @brief 育儿嫂订单定金状态
     */
    public static $ORDER_DEPOSIT_STATUS = array(
        '1' => '定金未支付',
        '2' => '定金已支付1000元',
        //'3' => '余额已支付',
        //'4' => '直接一次付清全款',
    );
    
    /**
     * @brief 订单回访类型配置
     */
    public static $VISIT_TYPE = array(
        '1' => '电话回访',
        '2' => '入户回访',
    );
    
    /**
     * @brief 宝宝类型枚举值。
     */
    public static $BABY_TYPE = array(
        '1' => '单胞胎',
        '2' => '双胞胎',
    );
    
    /**
     * @brief 客户名族枚举值配置
     */
    public static $CUSTOMER_NATION = array(
        '1' => '汉族',
        '2' => '少数名族',
    );
    
    /**
     * @brief 育儿嫂订单里面的薪资范围配置
     */
    public static $SALARY_PARENTAL = array(
        '1' => '3000 ~ 4000',
        '2' => '4000 ~ 6000',
        '3' => '6000以上',
    );
    
    /**
     * @brief 育儿嫂订单中得工龄配置
     */
    public static $WORK_AGE_PARENTAL = array(
        '1' => '3年以内',
        '2' => '3 ~ 5年',
        '3' => '5 ~ 8年',
        '4' => '8年以上',
    );
    
    /**
     * @brief 育儿嫂订单预约面试结果枚举配置
     * @ key:status的状态值。value：结果名称
     */
    public static $AUDITION_RESULT = array(
        '2' => '面试通过',
        '3' => '预约面试不通过',
    );

    /**
     * @brief 订单处罚类型配置
     */
    public static $ORDER_PUNISH_TYPE = array(
        self::PUNISH_NORMAL => '一般违纪',
        self::PUNISH_YELLOW => '黄牌处罚',
        self::PUNISH_RED    => '红牌处罚',
    );
    /**
     * @brief 处罚类型常量配置
     */
    const PUNISH_NORMAL = 1; //一般违纪
    const PUNISH_YELLOW = 2; //黄牌处罚
    const PUNISH_RED    = 3; //红牌处罚

    /**
     * @brief 红黄牌处罚比例
     */
    public static $PUNISH_PERCENT = array(
        self::PUNISH_YELLOW => 0.1,
        self::PUNISH_RED    => 0.2,
    );

    /**
     * @brief 微信下的订单的参考类型配置
     */
    public static $ORDER_WEIXIN_TYPE = array(
        '1' => '育儿嫂订单',
        '2' => '月嫂订单',
    );
    /**
     * @brief 微信下得订单的状态枚举说明配置
     */
    public static $ORDER_WEIXIN_STATUS = array(
        '1'  => '等待下单',
        '2'  => '已经下单',
        '-1' => '已删除',
    );
}
