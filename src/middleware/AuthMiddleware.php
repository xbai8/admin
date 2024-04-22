<?php
namespace xbase\admin\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 鉴权中间件检测
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $handler): Response
    {
        // 从headers中拿去请求用户token
        $authorization = $request->header('Authorization','');
        // 获取会话ID
        $request->sessionId($authorization);
        // 鉴权前置钩子
        $response = $request->method() == 'OPTIONS' ? response('', 204) : $handler($request);
        // 鉴权后置钩子
        return $response;
    }
}