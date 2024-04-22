<?php

/**
 * 获取模块名称
 * @throws \Exception
 * @return mixed
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbModuleName()
{
    $request = request();
    // 设置总后台路由格式
    $path = request()->path();
    $data = array_values(array_filter(explode('/', $path)));
    $moduleName = $data[0] ?? '';
    if (empty($moduleName)) {
        throw new Exception('模块名称不能为空');
    }
    return $moduleName;
}
/**
 * 生成URL地址
 * @param string $url
 * @return string
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
function xbUrl(string $url = '')
{
    $moduleName = xbModuleName();
    $path = "/{$moduleName}/{$url}";
    return $path;
}