<?php

namespace Mrzkit\LaravelExtensionEloquent\Crud;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
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
    public function create(array $data) : Model
    {
        $model = clone $this->getModel();

        if ( !$model->fill($data)->save()) {
            throw new CrudException('Create data fail.');
        }

        return $model;
    }

    /**
     * @desc 查
     * @param int $page 页码
     * @param int $perPage 每页数
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 前置
     * @param Closure|null $after 后置
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function retrieve(int $page = 1, int $perPage = 20, array $fields = ['*'], Closure $before = null, Closure $after = null)
    {
        $query = $this->getModel()->newQuery();

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
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return bool|null
     */
    public function update(int $id, array $data)
    {
        $obj = $this->detail($id);

        if (is_null($obj)) {
            return null;
        }

        $updated = $obj->update($data);

        if ( !$updated) {
            throw new CrudException('Update data fail.');
        }

        return $updated;
    }

    /**
     * @desc 改
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return bool|null
     */
    public function updateWithTrashed(int $id, array $data)
    {
        $obj = $this->detailWithTrashed($id);

        if (is_null($obj)) {
            return null;
        }

        $updated = $obj->update($data);

        if ( !$updated) {
            throw new CrudException('Update data fail..');
        }

        return $updated;
    }

    /**
     * @desc 删
     * @param int $id 主键
     * @return bool|null
     */
    public function delete(int $id)
    {
        $obj = $this->detail($id);

        if (is_null($obj)) {
            return null;
        }

        $deleted = $obj->delete();

        if ( !$deleted) {
            throw new CrudException('Delete data fail.');
        }

        return $deleted;
    }

    /**
     * @desc 详情
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 查询前处理
     * @return \Illuminate\Database\Eloquent\Builder|Model|mixed|object|null
     */
    public function detail(int $id, array $fields = ['id'], Closure $before = null)
    {
        $query = $this->getModel()->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->first();

        return $row;
    }

    /**
     * @desc 详情(查软删)
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 查询前处理
     * @return \Illuminate\Database\Eloquent\Builder|Model|mixed|object|null
     */
    public function detailWithTrashed(int $id, array $fields = ['id'], Closure $before = null)
    {
        $query = $this->getModel()->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->withTrashed()->first();

        return $row;
    }

    /**
     * @desc 快速批量添加
     * @param array $storeData
     * @return bool
     */
    public function fastBatchCreate(array $storeData) : bool
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
     * @param array $storeData
     * @return bool
     */
    public function safeBatchCreate(array $storeData) : array
    {
        $list = [];

        // 事务
        DB::beginTransaction();

        try {
            foreach ($storeData as $data) {
                $model = clone $this->getModel();

                if ( !$model->fill($data)->save()) {
                    $encode = json_encode($data);
                    throw new CrudException('Delete data fail.' . $encode);
                }

                $list[] = $model;
            }

            // 提交
            DB::commit();

            return $list;
        } catch (CrudException $e) {
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

