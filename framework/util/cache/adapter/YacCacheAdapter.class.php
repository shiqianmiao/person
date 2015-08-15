<?PHP
/**
 * @brief 简介：yac缓存应用
 * @author 陈朝阳<chenchaoyang@iyuesao.com>
 * @date 15/3/19
 * @copyright Copyright (c) 2015 安心客 Inc. (http://www.anxin365.cn)
 */

class YacCacheAdapter {
    private $yac = null;
    /**
     * 写缓存
     * @param  string $key      缓存的key
     * @param  string $value    缓存的value
     * @param  int    $duration 缓存时间，以秒为单位
     * @return bool   如果成功缓存则返回true，否则返回false.
     */
    public function write($key, $value, $duration = 0) {
        if (!class_exists('Yac')) return false;
        if (empty($this->yac)) {
            $this->yac = new Yac();
        }
        $key = CacheNamespace::encodeKey($key);
        return $this->yac->set($key, $value, $duration);
    }

    /**
     * 读缓存
     * @param  string $key 缓存的key
     * @return mixed  返回key对应的value，如果结果不存在、过期或者在获取的过程中发生错误，将会返回false.
     */
    public function read($key) {
        if (!class_exists('Yac')) return false;
        if (empty($this->yac)) {
            $this->yac = new Yac();
        }
        $key = CacheNamespace::encodeKey($key);
        return $this->yac->get($key);
    }

    /**
     * 删除缓存
     * @param  string $key 缓存的key
     * @return bool   如果成功删除,返回true;如果key对应的value不存在或者不能删除，则返回false.
     */
    public function delete($key) {
        if (!class_exists('Yac')) return false;
        if (empty($this->yac)) {
            $this->yac = new Yac();
        }
        $key = CacheNamespace::encodeKey($key);
        return $this->yac->delete($key);
    }
}

