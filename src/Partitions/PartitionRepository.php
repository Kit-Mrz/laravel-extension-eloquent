<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\DB;
use Mrzkit\LaravelExtensionEloquent\Contracts\ModelContract;
use Mrzkit\LaravelExtensionEloquent\Partitions\Contracts\PartitionRepositoryContract;

abstract class PartitionRepository implements ModelContract, PartitionRepositoryContract
{
    /**
     * @var PartitionModel 模型
     */
    protected $_model;

    /**
     * @desc 设置模型
     * @param PartitionModel $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->_model = $model;

        return $this;
    }

    /**
     * @desc 获取模型
     * @return PartitionModel
     */
    public function getModel() : PartitionModel
    {
        return $this->_model;
    }

    /**
     * @desc 增
     * @param int $partitionFactor 分表因子
     * @param array $data 新增数据
     * @return mixed|PartitionModel
     */
    public function partitionCreate(int $partitionFactor, array $data)
    {
        $model = clone $this->getModel()->partition($partitionFactor);

        $model->fill($data)->save();

        return $model;
    }

    /**
     * @desc 查
     * @param int $partitionFactor 分表因子
     * @param int $page 页码
     * @param int $perPage 每页数
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function partitionRetrieve(int $partitionFactor, int $page = 1, int $perPage = 20)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        $rows = $query->orderByDesc($this->getModel()->getKeyName())->paginate($perPage, ['*'], 'page', $page);

        return $rows;
    }

    /**
     * @desc 改
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return int|mixed
     */
    public function partitionUpdate(int $partitionFactor, int $id, array $data)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        $updated = $query->where($this->getModel()->getKeyName(), $id)->update($data);

        return $updated;
    }

    /**
     * @desc 删
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @return mixed
     */
    public function partitionDelete(int $partitionFactor, int $id)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        $deleted = $query->where($this->getModel()->getKeyName(), $id)->delete();

        return $deleted;
    }

    /**
     * @desc 改(改软删)
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return int|mixed
     */
    public function partitionUpdateWithTrashed(int $partitionFactor, int $id, array $data)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        $updated = $query->where($this->getModel()->getKeyName(), $id)->withTrashed()->update($data);

        return $updated;
    }

    /**
     * @desc 详情
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $fields 查询字段
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function partitionDetail(int $partitionFactor, int $id, array $fields = [])
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->first();

        return $row;
    }

    /**
     * @desc 详情(查软删)
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $fields 查询字段
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function partitionDetailWithTrashed(int $partitionFactor, int $id, array $fields = [])
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->withTrashed()->first();

        return $row;
    }

    /**
     * @desc 分表批量添加
     * @param int $partitionFactor 分表因子
     * @param array $data
     * @return bool
     */
    public function partitionBatchCreate(int $partitionFactor, array $data)
    {
        $model = $this->getModel()->partition($partitionFactor);

        $tableName = $model->getTable();

        $totallyGuarded = $model->totallyGuarded();

        $insertData = [];

        foreach ($data as $index => $item) {
            $tempData = [];
            foreach ($model->overwriteFillableFromArray($item) as $key => $val) {
                if ($model->isFillable($key)) {
                    $tempData[$key] = $val;
                } elseif ($totallyGuarded) {
                    $msg = sprintf('Add [%s] to fillable property to allow mass assignment on [%s].', $index, get_class($this));
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
