<?php

namespace xbase\admin\controller;

use support\Request;
use xbase\admin\XbController;
use xbase\admin\service\AppService;

class IndexController extends XbController
{
    /**
     * 无需登录方法
     * @var array
     */
    protected $noLogin = [
        'index',
        'site'
    ];

    /**
     * 渲染后台视图
     * @param \think\Request $request
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        // 渲染视图
        return $this->adminView();
    }

    /**
     * 应用入口
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function site(Request $request)
    {
        $config = null;
        // 返回数据
        $data = [
            'web_name' => $config['web_name'] ?? 'XB-Admin',
            'web_title' => '后台登录',
            'web_logo' => $config['web_logo'] ?? '',
            'public_api_login' => xbUrl('Login/login'),
            'public_api_user' => xbUrl('Login/user'),
            'public_api_user_edit_path' => '/XbAdmin/info',
            'public_api_user_edit' => xbUrl('user/info'),
        ];
        $data = AppService::resutl($data);
        return $this->successRes($data);
    }

    /**
     * 获取系统信息
     * @param \think\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function info(Request $request)
    {
        $systemInfo = SystemService::info();
        $data       = [
            'web_name' => $systemInfo['web_name'],
            'web_logo' => $systemInfo['logo'],
            'about_name' => $systemInfo['name'],
            'version_name' => $systemInfo['version_name'],
            'version' => $systemInfo['version'],
        ];
        return $this->successRes($data);
    }

    /**
     * 清除缓存
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function clear(Request $request){}

    /**
     * 锁定屏幕
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function lock(Request $request){}
}
