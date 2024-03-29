<?php

namespace Hangpu8\Admin\controller;

use Exception;
use support\Request;
use Hangpu8\Admin\Base;
use Hangpu8\Admin\model\SystemAdmin;
use Hangpu8\Admin\utils\Util;
use Hangpu8\Admin\validate\SystemAdmin as ValidateSystemAdmin;
use Shopwwi\WebmanFilesystem\Facade\Storage;

class PublicsController extends Base
{
    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['site', 'login', 'logout', 'captcha'];

    /**
     * 应用信息
     *
     * @return void
     */
    public function site()
    {
    }

    /**
     * 系统登录
     *
     * @return void
     */
    public function login(Request $request)
    {
        // 获取数据
        $post = $request->post();
        // 数据验证
        hpValidate(ValidateSystemAdmin::class, $post, 'login');

        // 查询数据
        $where['system_admin.username'] = $post['username'];
        $field = [
            'system_admin.*',
            'role.title as level',
            'role.is_system'
        ];
        $adminModel = SystemAdmin::join('system_admin_role as role', 'role.id', '=', 'system_admin.role_id')
            ->where($where)
            ->select($field)
            ->first();
        if (!$adminModel) {
            throw new Exception('登录账号错误');
        }
        // 验证登录密码
        if (!Util::passwordVerify((string) $post['password'], (string)$adminModel->password)) {
            throw new Exception('登录密码错误');
        }
        if ($adminModel->status == 0) {
            throw new Exception('该用户已被冻结');
        }
        $admin = $adminModel->toArray();
        // 处理头像
        if ($admin['headimg']) {
            $admin['headimg'] = Storage::url($admin['headimg']);
        }
        $session = $request->session();
        $session->set('hp_admin', $admin);

        // 更新登录信息
        $ip = $request->getRealIp($safe_mode = true);
        $adminModel->last_login_ip = $ip;
        $adminModel->last_login_time = date('Y-m-d H:i:s');
        $adminModel->save();

        // 返回数据
        return parent::successFul('登录成功', ['token' => $request->sessionId()]);
    }

    /**
     * 退出登录
     *
     * @param Request $request
     * @return void
     */
    public function loginout(Request $request)
    {
        $session = $request->session();
        $session->delete('hp_admin');
        return parent::success('成功退出');
    }
}
