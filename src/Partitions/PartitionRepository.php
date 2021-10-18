<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
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

        if ( !$model->fill($data)->save()) {
            throw new PartitionException('Create data fail.');
        }

        return $model;
    }

    /**
     * @desc 查
     * @param int $partitionFactor 分表因子
     * @param int $page 页码
     * @param int $perPage 每页数
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 前置
     * @param Closure|null $after 后置
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function partitionRetrieve(int $partitionFactor, int $page = 1, int $perPage = 20, array $fields = ['*'], Closure $before = null, Closure $after = null)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        $rows = $query->orderByDesc($this->getModel()->getKeyName())->paginate($perPage, $fields, 'page', $page);

        if ( !is_null($after)) {
            $after($query);
        }

        return $rows;
    }

    /**
     * @desc 改
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return bool|null
     */
    public function partitionUpdate(int $partitionFactor, int $id, array $data)
    {
        $obj = $this->partitionDetail($partitionFactor, $id);

        if (is_null($obj)) {
            return null;
        }

        $updated = $obj->update($data);

        if ( !$updated) {
            throw new PartitionException('Update data fail.');
        }

        return $updated;
    }

    /**
     * @desc 删
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @return bool|null
     */
    public function partitionDelete(int $partitionFactor, int $id)
    {
        $obj = $this->partitionDetail($partitionFactor, $id);

        if (is_null($obj)) {
            return null;
        }

        $deleted = $obj->delete();

        if ( !$deleted) {
            throw new PartitionException('Delete data fail.');
        }

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
        $obj = $this->partitionDetailWithTrashed($partitionFactor, $id);

        if (is_null($obj)) {
            return null;
        }

        $updated = $obj->update($data);

        if ( !$updated) {
            throw new PartitionException('Update data fail.');
        }

        return $updated;
    }

    /**
     * @desc 详情
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 查询前处理
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function partitionDetail(int $partitionFactor, int $id, array $fields = ['id'], Closure $before = null)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->first();

        return $row;
    }

    /**
     * @desc 详情(查软删)
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 查询前处理
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function partitionDetailWithTrashed(int $partitionFactor, int $id, array $fields = ['id'], Closure $before = null)
    {
        $query = $this->getModel()->partition($partitionFactor)->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->withTrashed()->first();

        return $row;
    }

    /**
     * @desc 快速批量添加
     * @param int $partitionFactor 分表因子
     * @param array $data 数据
     * @return bool
     */
    public function partitionFastBatchCreate(int $partitionFactor, array $data) : bool
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

    /**
     * @desc 安全批量添加
     * @param int $partitionFactor 分表因子
     * @param array $storeData 数据
     * @return array
     */
    public function partitionSafeBatchCreate(int $partitionFactor, array $storeData) : array
    {
        $list = [];

        // 事务
        DB::beginTransaction();

        try {
            foreach ($storeData as $data) {
                $model = clone $this->getModel()->partition($partitionFactor);

                if ( !$model->fill($data)->save()) {
                    $encode = json_encode($data);
                    throw new PartitionException('Delete data fail.' . $encode);
                }

                $list[] = $model;
            }

            // 提交
            DB::commit();

            return $list;
        } catch (PartitionException $e) {
            // 回滚
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * @desc 关联配置
     * @return array
     */
    abstract public function relationConfig() : array;

    /**
     * @desc 关联解析器
     * @param Builder $query
     * @param array $relations 关联配置
     * @return Builder
     */
    public function relationResolver(Builder $query, array $relations)
    {
        // 读取仓库的关联配置
        $relationConfigs = $this->relationConfig();

        $filterConfigs = [];

        foreach ($relations as $relationName => $relationParam) {
            // 抹掉左边的字符
            $name = ltrim($relationName, 'with_');

            // 检测是否已经配置
            if ( !isset($relationConfigs[$name])) {
                throw new InvalidArgumentException("没有此关联配置: {$name}");
            }

            if (is_bool($relationParam)) {
                if ($relationParam && isset($relationConfigs[$name])) {
                    // 获取配置
                    $filterConfigs[$name] = $relationConfigs[$name]([]);
                }
            } else if (is_array($relationParam)) {
                if ( !empty($relationParam)) {
                    // 获取配置
                    $filterConfigs[$name] = $relationConfigs[$name](
                        $relationParam
                    );
                }
            } else {
                throw new InvalidArgumentException('关联参数只支持布尔类型和数组类型:' . gettype($relationParam));
            }
        }

        if (empty( !$filterConfigs)) {
            $query->with($filterConfigs);
        }

        return $query;
    }
}
