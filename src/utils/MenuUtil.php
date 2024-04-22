<?php
namespace xbase\admin\utils;

class MenuUtil
{
    /**
     * 解析菜单数据
     * @param array $menus
     * @param array $data
     * @param int $pid
     * @param int $id
     * @return array|object
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function parseData(array $menus, array $data = [], int $pid = 0, int $id = 1)
    {
        $defaultState = '10';
        foreach ($menus as $value) {
            // 设置临时处理数据
            $menuData = $value;
            // 设置菜单ID
            $menuData['id'] = $id;
            // 设置父级ID
            $menuData['pid'] = $pid;
            // 设置请求类型
            $menuData['methods'] = isset($value['methods']) ? $value['methods'] : ['GET'];
            // 设置图标
            $menuData['icon'] = isset($value['icon']) ? $value['icon'] : '';
            // 菜单组件
            $menuData['component'] = isset($value['component']) ? $value['component'] : 'none/index';
            // 设置默认附带参数
            $menuData['params'] = isset($value['params']) ? $value['params'] : '';
            // 设置是否系统菜单
            $menuData['is_system'] = isset($value['is_system']) ? $value['is_system'] : $defaultState;
            // 是否默认菜单
            $menuData['is_default'] = isset($value['is_default']) ? $value['is_default'] : $defaultState;
            // 菜单是否显示
            $menuData['show'] = isset($value['is_show']) ? $value['is_show'] : $defaultState;
            // 删除子级菜单
            if (isset($menuData['children'])) {
                unset($menuData['children']);
            }
            // 设置菜单数据
            $data[] = $menuData;
            // 递归处理子级菜单
            if (isset($value['children']) && !empty($value['children'])) {
                $data = array_merge($data, self::parseData($value['children'], [], $id, $id+1));
                $id = end($data)['id'];
            }
            $id++;
        }
        return $data;
    }

    /**
     * 获取原始菜单数据
     * @param string $path
     * @throws \xbase\admin\utils\Exception
     * @return array|object
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getOriginMenus(string $path)
    {
        if (!file_exists($path)) {
            throw new Exception('菜单文件不存在');
        }
        // 获取菜单数据
        $menus = file_get_contents($path);
        if (empty($menus)) {
            throw new Exception('菜单内容为空');
        }
        // 转换菜单数据
        $menus = json_decode($menus, true);
        if (empty($menus)) {
            throw new Exception('菜单数据为空');
        }
        // 返回菜单数据
        return $menus;
    }
    /**
     * 获取表格数组菜单数据
     * @param string $path
     * @return array|object
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenusTable(string $path)
    {
        // 获取原始菜单数据
        $data = self::getOriginMenus($path);
        // 解析菜单数据
        $data = self::parseData($data);
        // 返回菜单数据
        return $data;
    }

    /**
     * 获取树状菜单数据
     * @param string $path
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenuTree(string $path)
    {
    }
}