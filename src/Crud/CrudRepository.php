<?php

namespace Mrzkit\LaravelExtensionEloquent\Crud;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mrzkit\LaravelExtensionEloquent\Contracts\ModelContract;
use Mrzkit\LaravelExtensionEloquent\Crud\Contracts\CrudRepositoryContract;

abstract class CrudRepository implements ModelContract, CrudRepositoryContract
{
    /**
     * @var CrudModel 模型
     */
    protected $_model;

    /**
     * @desc 获取模型
     * @return CrudModel
     */
    public function getModel() : CrudModel
    {
        return $this->_model;
    }

    /**
     * @desc 设置模型
     * @param CrudModel $model
     */
    public function setModel($model)
    {
        $this->_model = $model;

        return $this;
    }

    /**
     * @desc 增
     * @param array $data 新增数据
     * @return Model
     */
    public function create(array $data)
    {
        $model = clone $this->getModel();

        $model->fill($data)->save();

        return $model;
    }

    /**
     * @desc 查
     * @param null $page 页码
     * @param int $perPage 每页数
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function retrieve(int $page = 1, int $perPage = 20)
    {
        $query = $this->getModel()->newQuery();

        $rows = $query->orderByDesc($this->getModel()->getKeyName())->paginate($perPage, ['*'], 'page', $page);

        return $rows;
    }

    /**
     * @desc 改
     * @param int $id 主键
     * @param array $data
     * @return int|mixed 更新的数据
     */
    public function update(int $id, array $data)
    {
        $query = $this->getModel()->newQuery();

        $updated = $query->where($this->getModel()->getKeyName(), $id)->update($data);

        return $updated;
    }

    /**
     * @desc 改
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return mixed
     */
    public function updateWithTrashed(int $id, array $data)
    {
        $query = $this->getModel()->newQuery();

        $updated = $query->where($this->getModel()->getKeyName(), $id)->withTrashed()->update($data);

        return $updated;
    }

    /**
     * @desc 删
     * @param int $id 主键
     * @return mixed
     */
    public function delete(int $id)
    {
        $query = $this->getModel()->newQuery();

        $num = $query->where($this->getModel()->getKeyName(), $id)->delete();

        return $num;
    }

    /**
     * @desc 详情
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @return \Illuminate\Database\Eloquent\Builder|Model|mixed|object|null
     */
    public function detail(int $id, array $fields = ['id'])
    {
        $query = $this->getModel()->newQuery();

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->first();

        return $row;
    }

    /**
     * @desc 详情(查软删)
     * @param int $id 主键
     * @param array $fields 查询字段
     * @return \Illuminate\Database\Eloquent\Builder|Model|mixed|object|null
     */
    public function detailWithTrashed(int $id, array $fields = [])
    {
        $query = $this->getModel()->newQuery();

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->withTrashed()->first();

        return $row;
    }

    /**
     * @desc 批量添加
     * @param array $storeData
     * @return bool
     */
    public function batchCreate(array $storeData)
    {
        $model = $this->getModel();

        $tableName = $model->getTable();

        $totallyGuarded = $model->totallyGuarded();

        $insertData = [];

        foreach ($storeData as $index => $item) {
            $tempData = [];
            foreach ($model->overwriteFillableFromArray($item) as $key => $val) {
                if ($model->isFillable($key)) {
                    $tempData[$key] = $val;
                } elseif ($totallyGuarded) {
                    $msg = sprintf( 'Add [%s] to fillable property to allow mass assignment on [%s].', $index, get_class($this));
                    throw new MassAssignmentException($msg);
                }
            }

            if ( !empty($tempData)) {
                $insertData[] = $tempData;
            }
        }

        $inserted = false;

        if ( !empty($insertData)) {
            $inserted = DB::table($tableName)->insert($insertData);
        }

        return $inserted;
    }

}

