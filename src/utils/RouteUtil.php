<?php
namespace xbase\admin\utils;

use support\Request;
use Webman\Route;
use xbase\admin\utils\MenuUtil;

/**
 * 路由工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class RouteUtil
{
    /**
     * 注册路由
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function regBaseRoute()
    {
        if (!InstallUtil::hasInstall())
        {
            // 注册未安装路由
            self::notInstall();
            return true;
        }
        // 注册XBase路由
        self::registerCore();
        // 注册XBase系统管理文件路由
        self::xbaseAdminView();
        // 注册单应用路由
        self::registerApp();
    }

    /**
     * 注册未安装路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function notInstall()
    {
        Route::any('/install/[{path:.+}]', function (Request $request, $path = '') {
            // 安全检查，避免url里 /../../../password 这样的非法访问
            if (strpos($path, '..') !== false)
            {
                return response('<h1>400 Bad Request</h1>', 400);
            }
            // 文件
            $installPath = dirname(__DIR__);
            $file        = "{$installPath}/resources/install/{$path}";
            if (!is_file($file))
            {
                return response('<h1>404 Not Found</h1>', 404);
            }
            return response()->withFile($file);
        });
    }

    /**
     * 注册XBase核心路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function registerCore()
    {
        // 获取模块配置
        $xbaseModule = config('plugin.xbase.admin.app.xbaseModule');
        // 获取菜单数据
        $path = dirname(__DIR__) . "/resources/menus.json";
        // 获取菜单数据
        $data = MenuUtil::getMenusTable($path);
        // 是否开启控制器后缀
        $suffix = config('app.controller_suffix','');
        // 批量注册路由
        foreach ($data as $menu) {
            // 检测是否注册路由
            if ($menu['is_api'] === '20') {
                // 获取控制器与方法
                list($control, $action) = explode('/', $menu['path']);
                // 注册基础路由
                Route::add(
                    $menu['methods'],
                    "/{$xbaseModule}/{$control}/{$action}",
                    "xbase\admin\controller\\{$control}{$suffix}@{$action}"
                )
                ->middleware([
                    \xbase\admin\middleware\AuthMiddleware::class
                ]);
            }
        }
    }

    /**
     * 注册XBase系统管理文件路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function xbaseAdminView()
    {
        // 注册静态文件路由
        Route::any('/xbase/assets/[{path:.+}]', function (Request $request, $path = '') {
            // 安全检查，避免url里 /../../../password 这样的非法访问
            if (strpos($path, '..') !== false)
            {
                return response('<h1>400 Bad Request</h1>', 400);
            }
            // 文件
            $installPath = dirname(__DIR__);
            $file        = "{$installPath}/resources/xbase/assets/{$path}";
            if (!is_file($file))
            {
                return response('<h1>404 Not Found</h1>', 404);
            }
            return response()->withFile($file);
        });
    }
}