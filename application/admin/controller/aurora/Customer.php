<?php

namespace app\admin\controller\aurora;

use app\common\controller\Backend;
use app\admin\model\User;
use Exception;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 极光会员信息管理
 *
 * @icon fa fa-circle-o
 */
class Customer extends Backend
{
    /**
     * Customer模型对象
     * @var \app\admin\model\aurora\Customer
     */
    protected $model = null;

    /**
     * 是否是关联查询
     */
    //protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\aurora\Customer;
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /*public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with("user")
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with("user")
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }*/

    /**
     * 用户详情
     */
    public function detail($ids){
        $ids = $this->request->param('ids');

        $row = $this->model->with('user')->find($ids);
        //dump($row);die;
        if (!$row) {
            $this->error(__('No Results were found'));
        }

/*        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }*/

        $row['mobile'] = $row['mobile'] ? $row['mobile'] : '未绑定';


        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
