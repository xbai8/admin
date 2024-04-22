<?php
namespace xbase\admin\utils;

/**
 * 安装工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class InstallUtil
{
    /**
     * 判断是否已经安装
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function hasInstall()
    {
        if (file_exists(base_path('.env'))) {
            return true;
        }
        return false;
    }
}