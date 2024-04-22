<?php
namespace xbase\admin;

use xbase\admin\utils\JsonUtil;

class XbController
{
    // 引入JsonUtil
    use JsonUtil;

    /**
     * 构造方法
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 初始化方法
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function initialize()
    {
    }
    
    /**
     * 渲染系统视图
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function adminView()
    {
        $path = str_replace("\\",'/',__DIR__) . '/resources/xbase/index.html';
        if (!file_exists($path)) {
            throw new \Exception('未找到系统视图资源文件');
        }
        $content = file_get_contents($path);
        if (!$content) {
            throw new \Exception('系统视图资源文件内容为空');
        }
        return (string)$content;
    }
}