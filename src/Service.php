<?php

namespace xbase\admin;

use Webman\Bootstrap;

/**
 * 服务启动
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Service implements Bootstrap
{
    /**
     * 启动服务
     * @param mixed $worker
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function start($worker)
    {
        // 是否是命令行环境?
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // monitor进程不执行定时器
        if ($worker->name == 'monitor') {
            return;
        }
        // 引入函数库
        require_once __DIR__ . '/helpers.php';
    }
}