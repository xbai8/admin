<?php
namespace xbase\controller;

use app\admin\validate\PageValidate;
use app\common\builder\FormBuilder;
use app\common\builder\ListBuilder;
use app\common\builder\table\RowEditTrait;
use app\common\enum\StatusEnum;
use app\common\model\AdminRule;
use app\common\model\ContentPage;
use hg\apidoc\annotation as Apidoc;
use think\Request;
use xbase\XbController;

/**
 * 单页管理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PageController extends XbController
{
    use RowEditTrait;

    /**
     * 模型
     * @var ContentPage
     */
    protected $model;

    /**
     * 初始化
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function initialize()
    {
        parent::initialize();
        $this->model = new ContentPage;
    }

    /**
     * 单页列表-表格
     * @Apidoc\Method ("GET")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function indexTable(Request $request)
    {
        $builder = new ListBuilder;
        $data    = $builder
            ->addActionOptions('操作', [
                'width' => 180
            ])
            ->pageConfig()
            ->editConfig()
            ->addTopButton('add', '添加', [
                'api' => 'Page/add',
                'path' => '/Page/add',
            ], [], [
                'type' => 'primary'
            ])
            ->addRightButton('edit', '修改', [
                'api' => 'Page/edit',
                'path' => '/Page/edit',
                'aliasParams' => [
                    'name' => 'name'
                ],
            ], [], [
                'type' => 'primary',
            ])
            ->addRightButton('del', '删除', [
                'type' => 'confirm',
                'api' => 'Page/del',
                'method' => 'delete',
            ], [
                'type' => 'error',
                'title' => '温馨提示',
                'content' => '是否确认删除该数据',
            ], [
                'type' => 'danger',
            ])
            ->addColumn('create_at', '创建时间', [
                'width' => 150
            ])
            ->addColumn('title', '标题名称')
            ->addColumn('name', '标签名称', [
                'width' => 280
            ])
            ->addColumnEle('link', 'H5链接', [
                'width' => 160,
                'params' => [
                    'type' => 'link',
                    'props' => [
                        'copy' => true,
                        'text' => '点击打开'
                    ]
                ]
            ])
            ->addColumnEle('state', '状态', [
                'width' => 100,
                'params' => [
                    'type' => 'switch',
                    'api' => xbUrl('Page/rowEdit'),
                    'unchecked' => StatusEnum::getActiveEnum('STATUS_NO', 'text'),
                    'checked' => StatusEnum::getActiveEnum('STATUS_YES', 'text'),
                ],
            ])
            ->addColumnEle('is_menu', '是否加入菜单', [
                'width' => 150,
                'params' => [
                    'type' => 'switch',
                    'api' => xbUrl('Page/menuEdit'),
                    'unchecked' => [
                        'text' => '未加入菜单',
                        'value' => '10'
                    ],
                    'checked' => [
                        'text' => '已加入菜单',
                        'value' => '20'
                    ],
                ],
            ])
            ->create();
        return $this->successRes($data);
    }

    /**
     * 单页菜单处理
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function menuEdit(Request $request)
    {
        $id     = $request->post('id');
        $model  = $this->model;
        $model  = $model->where('id', $id)->find();
        $where  = [
            ['path', '=', 'Page/edit'],
            ['auth_params', '=', "name={$model['name']}"],
        ];
        $isMenu = AdminRule::where($where)->count();
        if ($isMenu)
        {
            event('DelMenu', [
                'where' => [
                    ['path', '=', 'Page/edit'],
                    ['auth_params', '=', "name={$model['name']}"],
                ],
            ]);
        }
        else
        {
            event('CreateMenu', [
                'title' => $model['title'],
                'parent' => 'Content/group',
                'component' => 'form/index',
                'path' => 'Page/edit',
                'sort' => '100',
                'params' => "name={$model['name']}",
            ]);
        }
        return $this->success('操作成功');
    }

    /**
     * 单页列表
     * @Apidoc\Method ("GET")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $model = $this->model;
        $data  = $model
            ->order('id', 'desc')
            ->paginate()
            ->each(function ($item) {
                $where           = [
                    ['path', '=', 'Page/edit'],
                    ['auth_params', '=', "name={$item['name']}"],
                ];
                $isMenu          = AdminRule::where($where)->count();
                $item['is_menu'] = $isMenu ? '20' : '10';
            })
            ->toArray();
        return $this->successRes($data);
    }

    /**
     * 添加单页
     * @Apidoc\Method ("GET,POST")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function add(Request $request)
    {
        if ($request->method() == 'POST')
        {
            $post = $request->post();

            // 数据验证
            xbValidate(PageValidate::class, $post, 'add');

            $model = $this->model;
            if (!$model->save($post))
            {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $builder = $this->formView();
        $builder->setMethod('POST');
        $view = $builder->create();
        return $this->successRes($view);
    }

    /**
     * 修改单页
     * @Apidoc\Method ("GET,PUT")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function edit(Request $request)
    {
        $name  = $request->get('name');
        $where = [
            'name' => $name,
        ];
        $model = $this->model;
        $model = $model->where($where)->find();
        if (!$model)
        {
            return $this->fail('该数据不存在');
        }
        if ($request->method() == 'PUT')
        {
            $post = $request->post();

            // 数据验证
            xbValidate(PageValidate::class, $post, 'edit');

            if (!$model->save($post))
            {
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
     * 删除单页
     * @Apidoc\Method ("DELETE")
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function del(Request $request)
    {
        $id    = $request->post('id');
        $model = $this->model;
        $model = $model->where('id', $id)->find();
        if (!$model)
        {
            return $this->fail('该数据不存在');
        }
        // 删除
        if (!$model->delete())
        {
            return $this->fail('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function formView()
    {
        $name       = request()->get('name', '');
        $disabled = false;
        if ($name)
        {
            $disabled = true;
        }
        $builder = new FormBuilder;
        $builder->addRow('title', 'input', '标题名称', '', [
            'col' => 8,
        ]);
        $builder->addRow('menu_title', 'input', '菜单标题', '', [
            'col' => 8,
        ]);
        $builder->addRow('name', 'input', '标签名称（创建后不可修改）', '', [
            'col' => 8,
            'disabled' => $disabled,
        ]);
        $builder->addRow('content', 'wangEditor', '文章内容');
        return $builder;
    }
}
