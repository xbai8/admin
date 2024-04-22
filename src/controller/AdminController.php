<?php
namespace xbase\controller;

use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\enum\StatusEnum;
use app\common\enum\StatusEnumStyle;
use app\common\model\AdminRole;
use app\common\service\UploadService;
use app\common\validate\AdminValidate;
use hg\apidoc\annotation as Apidoc;
use app\common\model\Admin;
use support\Request;
use xbase\view\table\AdminView;
use xbase\XbController;

/**
 * 账号管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminController extends XbController
{
    use AdminView;

    /**
     * 初始化方法
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function initialize()
    {
        parent::initialize();
    }
    
    /**
     * 管理员-表格
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = $this->getTableView();
        $data    = $builder->create();
        return $this->successRes($data);
    }
    
    /**
     * 管理员列表
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $admin_id = $request->userId;
        $where    = [
            'pid'   => $admin_id,
        ];
        $data = Admin::with(['role'])
            ->where($where)
            ->paginate()
            ->toArray();
        return $this->successRes($data);
    }
    
    /**
     * 添加管理员
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        $admin_id = $request->user['id'];
        if ($request->method() == 'POST') {
            $post = $request->post();
            $post['pid']    = $admin_id;

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'add');
            
            // 验证是否已存在
            $where = [
                'username'      => $post['username']
            ];
            if (Admin::where($where)->count()) {
                return $this->fail('该登录账号已存在');
            }
            $model = new Admin;
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $view = $this->formView()
        ->setMethod('POST')
        ->create();
        return $this->successRes($view);
    }

    /**
     * 修改管理员
     * @Apidoc\Method ("GET,PUT")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $admin_id = $request->userId;
        $id = $request->get('id');
        $where    = [
            'id'    => $id,
            'pid'   => $admin_id,
        ];
        $model = Admin::where($where)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'edit');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $view = $this->formView()
        ->setMethod('PUT')
        ->setData($model)
        ->create();
        return $this->successRes($view);
    }

    /**
     * 删除管理员
     * @Apidoc\Method ("DELETE")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        if (!Admin::where('id',$id)->delete()) {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 修改管理员资料
     * @Apidoc\Method ("GET,PUT")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function info(Request $request)
    {
        $adminId = $request->userId;
        $model = Admin::where('id', $adminId)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT') {
            $post = $request->post();

            // 数据验证
            xbValidate(AdminValidate::class, $post, 'editSelf');

            // 空密码，不修改
            if (empty($post['password'])) {
                unset($post['password']);
            }
            if (!$model->save($post)) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data = $model->toArray();
        $data['avatar'] = UploadService::url($data['avatar']);
        $builder = new FormBuilder;
        $builder->addRow('username', 'input', '登录账号', '', [
            'col'           => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col'           => 12,
            'placeholder'   => '不填写，则不修改密码',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'col'           => 12,
        ]);
        $builder->addRow('avatar', 'uploadify', '用户头像', '', [
            'col'           => 12,
            'props'         => [
                'format'    => ['jpg', 'png', 'gif']
            ],
        ]);
        $builder->setMethod('PUT');
        $builder->setFormData($data);
        $view = $builder->create();
        return $this->successRes($view);
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $adminId = request()->userId;
        $builder = new FormBuilder;
        $builder->addRow('role_id', 'select', '所属角色', '', [
            'col'           => 12,
            'options'       => AdminRole::selectOptions((int)$adminId)
        ]);
        $builder->addRow('status', 'radio', '用户状态', '10', [
            'col'           => 12,
            'options'       => StatusEnum::options()
        ]);
        $builder->addRow('username', 'input', '登录账号', '', [
            'col'           => 12,
        ]);
        $builder->addRow('password', 'input', '登录密码', '', [
            'col'           => 12,
            'placeholder'   => '不填写，则不修改密码',
        ]);
        $builder->addRow('nickname', 'input', '用户昵称', '', [
            'col'           => 12,
        ]);
        $builder->addRow('avatar', 'uploadify', '用户头像', '', [
            'col'           => 12,
            'props'         => [
                'format'    => ['jpg', 'png', 'gif']
            ],
        ]);
        return $builder;
    }
}
