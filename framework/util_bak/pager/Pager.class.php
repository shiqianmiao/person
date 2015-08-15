<?php
/** 
 * @brief 分页相关方法
 * @author aozhongxu
 */

class Pager {
    
    /**
     * @brief 获得分页信息
     * @param int $count 总条数
     * @param int $current 当前页数
     * @param int $size 每页条数
     * @param int $max_page 最大页数
     * @return array
     *  - count 总条数
     *  - current 当前页数
     *  - size 每页条数
     *  - first 第一页
     *  - last 最后页
     *  - prev 上一页
     *  - next 下一页
     *  - begin 当页第一条信息编号
     *  - end 当页最后一条信息编号
     */
    public static function getPager($count, $current, $size, $max_page = null) {
    
        $count      = (int) $count;
        $current    = (int) $current;
        $size       = (int) $size;
        
        if ($max_page) {
            $max_count = (int)$size * $max_page;
            if ($count > $max_count)
                $count = $max_count;
        }
        
        $first  = 1;
        $last   = ceil($count / $size);
        
        // 当前页数小于最小页
        if ($current < $first) {
            $current    = $first;
        }
        // 当前页数大于最大页
        elseif ($current > $last) {
            $current    = $last;
        }
        
        $prev   = ($current > $first) ? $current - 1 : $first;
        $next   = ($current < $last) ? $current + 1 : $last;
        $begin  = $count ? ($current - 1) * $size + 1 : 0;
        $end    = $count ? $current * $size : 0;
        
        if ($end > $count)  
            $end    = $count;
        
        return array(
            'current'   => $current,
            'count'     => $count,
            'size'      => $size,
            'first'     => $first,
            'last'      => $last,
            'prev'      => $prev,
            'next'      => $next,
            'begin'     => $begin,
            'end'       => $end,
        );
    
    }
    
}
