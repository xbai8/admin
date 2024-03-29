<?php

namespace Hangpu8\Admin\controller;

use Exception;
use Hangpu8\Admin\Base;
use Hangpu8\Admin\crud\Crud;
use Hangpu8\Admin\model\SystemAdmin;
use Hangpu8\Admin\utils\Upload;
use Hangpu8\Admin\utils\Util;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Request;

class SystemAdminController extends Base
{
    // 一键CRUD
    use Crud;

    /**
     * @var SystemAdmin
     */
    protected $model = null;

    // 事件定义
    public $crudEvent = [
        'tableEventBefore'  => 'tableDataCallback',
        'tableEventAfter'   => 'tableDataCheck',
        'delEventBefore'    => 'delBefore',
    ];

    // 表格头部按钮
    public $topButton = [
        [
            'name'      => 'add',
            'title'     => '添加',
            'pageData'  => [
                'path'  => 'hpadmin/SystemAdmin/add',
            ],
            'style'     => []
        ],
    ];

    // 表格列右侧按钮
    public $rightButton = [
        'title'         => '操作选项',
        'extra'         => [
            'template'  => 'rightButtonList',
        ],
        'button'        => [
            [
                'name'      => 'edit',
                'title'     => '编辑',
                'pageData'  => [
                    'path'  => 'hpadmin/SystemAdmin/edit',
                ],
                'style'     => []
            ],
            [
                'name'          => 'del',
                'title'         => '删除',
                'pageData'      => [
                    'path'      => 'hpadmin/SystemAdmin/del',
                    'type'      => 'confirm',
                    'method'    => 'DELETE',
                    'title'     => '温馨提示',
                    'content'   => '是否确认删除该数据？',
                ],
                'style'         => [
                    'type'      => 'danger',
                ]
            ],
        ],
    ];

    // 是否有分页
    public $paginate = true;

    // 验证器
    public $validate = [
        'class'             => \Hangpu8\Admin\validate\SystemAdmin::class,
        'scene'             => [
            'add'           => 'add',
            'edit'          => 'edit',
        ],
    ];

    // 表格列
    public $columns = [
        'id'                => [
            'width'         => 100,
        ],
        'create_at'         => [],
        'role_id'           => [],
        'username'          => [],
        'nickname'          => [],
        'headimg'           => [
            'type'          => 'image'
        ],
        'status'            => [
            'type'          => 'tag',
            'replace'       => '：0禁用，1启用',
            'style'         => [
                [
                    'type'  => 'warning',
                ],
                [
                    'type'  => 'success',
                ],
            ],
            'options'       => ['禁用', '正常'],
        ],
    ];

    // 表单添加视图列
    public $formAddColumns = [
        'username'          => [
            'type'          => 'input',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'password'          => [
            'type'          => 'input',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'nickname'          => [
            'type'          => 'input',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'role_id'           => [
            'type'          => 'input',
            'type'          => 'select',
            'callback'      => [SystemAdminRoleController::class, 'getRoleOptions'],
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'status'            => [
            'type'          => 'radio',
            'value'         => '1',
            'replace'       => '：0禁用，1启用',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
                'options'   => [
                    [
                        'label' => '禁用',
                        'value' => '0'
                    ],
                    [
                        'label' => '正常',
                        'value' => '1'
                    ],
                ],
            ],
        ],
        'headimg'           => [
            'type'          => 'upload',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
                'props'     => [
                    'modalTitle'    => '图片预览',
                    'listType'      => 'picture-card',
                    'uploadType'    => 'image',
                    'limit'         => 1,
                ],
            ],
        ],
    ];

    // 编辑表单数据处理
    public $formEditViewCheck = 'AdminEditViewCheck';
    // 表单修改视图列
    public $formEditColumns = [
        'username'          => [
            'type'          => 'input',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'password'          => [
            'type'          => 'input',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'nickname'          => [
            'type'          => 'input',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'role_id'           => [
            'type'          => 'input',
            'type'          => 'select',
            'callback'      => [SystemAdminRoleController::class, 'getRoleOptions'],
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
            ],
        ],
        'status'            => [
            'type'          => 'radio',
            'value'         => '1',
            'replace'       => '：0禁用，1启用',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
                'options'   => [
                    [
                        'label' => '禁用',
                        'value' => '0'
                    ],
                    [
                        'label' => '正常',
                        'value' => '1'
                    ],
                ],
            ],
        ],
        'headimg'           => [
            'type'          => 'upload',
            'replace'       => '',
            'save'          => true,
            'extra'         => [
                'col'       => [
                    'span'  => 12
                ],
                'props'     => [
                    'modalTitle'    => '图片预览',
                    'listType'      => 'picture-card',
                    'uploadType'    => 'image',
                    'limit'         => 1,
                ],
            ],
        ],
    ];

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new SystemAdmin;
    }

    /**
     * 表格前置事件
     *
     * @return void
     */
    public function tableDataCallback(Request $request)
    {
        $model = $this->model;
        $admin_id = hp_admin_id();
        $where = [
            ['system_admin.id', '<>', $admin_id],
            ['system_admin.pid', '=', $admin_id],
        ];
        $model = $model
            ->join('system_admin_role as role', 'role.id', '=', 'system_admin.role_id')
            ->where($where);

        $model = $model->orderBy('system_admin.id', 'desc');
        // 取部分字段
        $model = $model->select(['system_admin.*', 'role.title as role_id']);

        // 返回构造模型
        return $model;
    }

    /**
     * 表格数据处理后置事件
     *
     * @param array $data
     * @return array
     */
    public static function tableDataCheck(array $data): array
    {
        foreach ($data as $key => $value) {
            if (isset($value['headimg']) && $value['headimg']) {
                $data[$key]['headimg'] = Storage::url($value['headimg']);
            }
        }
        return $data;
    }

    /**
     * 添加数据前置回调
     *
     * @return void
     */
    public function formAddCallback(array $data): array
    {
        $admin_id = hp_admin_id();
        $data['pid'] = $admin_id;

        // 加密哈希值
        $passwordHash = Util::passwordHash($data['password']);
        $data['password'] = $passwordHash;

        // 头像处理
        $headimg = is_array($data['headimg']) ? current($data['headimg']) : $data['headimg'];
        $data['headimg'] = Upload::urlReplace((string) $headimg);

        // 返回处理数据
        return $data;
    }

    /**
     * 修改数据前置回调
     *
     * @param array $data
     * @return array
     */
    public function formEditCallback(array $data): array
    {
        $admin_id = hp_admin_id();
        $data['pid'] = $admin_id;

        if (isset($data['password']) && $data['password']) {
            // 加密哈希值
            $passwordHash = Util::passwordHash($data['password']);
            $data['password'] = $passwordHash;
        } else {
            unset($data['password']);
        }

        // 头像处理
        $headimg = is_array($data['headimg']) ? current($data['headimg']) : $data['headimg'];
        $data['headimg'] = Upload::urlReplace((string) $headimg);

        // 返回处理数据
        return $data;
    }

    /**
     * 编辑表单渲染数据处理
     *
     * @param array $data
     * @return array
     */
    public function AdminEditViewCheck(array $data): array
    {
        // 头像拼接处理
        if ($data['headimg']) {
            $data['headimg'] = Storage::url($data['headimg']);
        }
        return $data;
    }

    /**
     * 删除前置事件
     *
     * @param Request $request
     * @return void
     */
    public function delBefore(Request $request)
    {
        $id = $request->get('id');
        $model = $this->model;
        $where = [
            ['id', '=', $id],
        ];
        $model = $model->where($where)->first();
        if ($model->is_system == '1') {
            throw new Exception('系统管理用户，禁止删除');
        }
        $admin_id = hp_admin_id();
        if ($model->pid != $admin_id) {
            throw new Exception('该管理员非您的旗下');
        }
        return $model;
    }
}
