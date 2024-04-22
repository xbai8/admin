<?php
namespace xbase\view;

trait AdminView
{
    /**
     * 获取表格视图渲染器
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getTableView()
    {
        $builder = new ListBuilder;
        $builder->addActionOptions('操作', [
            'width' => 180
        ]);
        $builder->pageConfig();
        $builder->addTopButton('add', '添加', [
            'type' => 'modal',
            'api' => 'XbAdmin/add',
            'path' => 'XbAdmin/add',
        ], [
            'title' => '添加管理员',
        ], [
            'type' => 'primary'
        ]);
        $builder->addRightButton('edit', '修改', [
            'type' => 'modal',
            'api' => 'XbAdmin/edit',
            'path' => 'XbAdmin/edit',
        ], [
            'title' => '修改管理员信息',
        ], [
            'type' => 'primary',
        ]);
        $builder->addRightButton('del', '删除', [
            'type' => 'confirm',
            'api' => 'XbAdmin/del',
            'method' => 'delete',
        ], [
            'type' => 'error',
            'title' => '温馨提示',
            'content' => '是否确认删除该数据',
        ], [
            'type' => 'danger',
        ]);
        $builder->addColumn('username', '登录账号');
        $builder->addColumn('nickname', '用户昵称');
        $builder->addColumnEle('headimg', '用户头像', [
            'params' => [
                'type' => 'image',
            ],
        ]);
        $builder->addColumn('role.title', '所属角色');
        $builder->addColumn('login_ip', '最近登录IP');
        $builder->addColumn('login_time', '最近登录时间');
        $builder->addColumnEle('status', '当前状态', [
            'width' => 90,
            'params' => [
                'type' => 'tags',
                'options' => StatusEnum::dict(),
                'style' => StatusEnumStyle::labelMap('type', false),
            ],
        ]);
        return $builder;
    }
    protected function getFormView()
    {
    }
}