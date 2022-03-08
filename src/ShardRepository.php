<?php

namespace Mrzkit\LaravelExtensionEloquent;

use Closure;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mrzkit\LaravelExtensionEloquent\Contracts\BatchContract;
use Mrzkit\LaravelExtensionEloquent\Contracts\ModelContract;
use Mrzkit\LaravelExtensionEloquent\Contracts\RepositoryContract;
use Mrzkit\LaravelExtensionEloquent\Contracts\ResolverContract;
use Mrzkit\LaravelExtensionEloquent\Contracts\ShardFactorContract;
use Mrzkit\LaravelExtensionEloquent\Contracts\TrashContract;
use Mrzkit\LaravelExtensionEloquent\Exceptions\CrudException;
use Mrzkit\LaravelExtensionEloquent\Model\ShardModel;

abstract class ShardRepository implements ModelContract, RepositoryContract, BatchContract, ResolverContract, TrashContract, ShardFactorContract
{
    use ResolverTrait;

    /**
     * @var ShardModel 模型
     */
    protected $_model;

    /**
     * @desc
     * @param ShardModel $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->_model = $model;

        return $this;
    }

    /**
     * @desc
     * @return ShardModel
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @desc 设置分表因子给模型
     * @param int $factor
     * @return $this|mixed
     */
    public function setFactor($factor)
    {
        $this->getModel()->setFactor($factor);

        return $this;
    }

    /**
     * @desc 获取模型的分表因子
     * @return int|mixed
     */
    public function getFactor()
    {
        return $this->getModel()->getFactor();
    }

    /**
     * @desc
     * @param array $data
     */
    public function create(array $data)
    {
        $model = clone $this->getModel()->sharding();

        if ( !$model->fill($data)->save()) {
            throw new CrudException('Create data fail.');
        }

        return $model;
    }

    /**
     * @desc 检索
     * @param array|string[] $fields 查询字段
     * @param array $relations 查询关联
     * @param array $paginateParams 分页配置
     * @param array $orderConfig 排序配置
     * @param Closure|null $before 查询前处理
     * @param Closure|null $after 查询后处理
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|mixed
     */
    public function retrieve(array $fields = ['*'], array $relations = [], array $paginateParams = [], array $orderConfig = [], Closure $before = null, Closure $after = null)
    {
        $conf = [
            'perPage'  => (int) ($paginateParams['perPage'] ?? 20),
            'columns'  => $fields,
            'pageName' => (string) ($paginateParams['pageName'] ?? 'page'),
            'page'     => (int) ($paginateParams['page'] ?? 1),
        ];

        $query = $this->getModel()->sharding()->newQuery();

        // 查询前处理
        if ( !is_null($before)) {
            $before($query);
        }

        // 关联解析器
        if ( !empty($relations)) {
            $query = $this->relationResolver($query, $relations);
        }

        // 关联排序解析器
        if ( !empty($orderConfig)) {
            $query = $this->orderResolver($query, $orderConfig['orderKey'] ?? "", $orderConfig['orderTable'] ?? "");
        }

        // 排序和分页
        $rows = $query->select($fields)->paginate($conf['perPage'], ['*'], $conf['pageName'], $conf['page']);

        if ( !is_null($after)) {
            $after($query);
        }

        return $rows;
    }

    /**
     * @desc
     * @param int $id
     * @param array $data
     * @return bool|null
     */
    public function update(int $id, array $data) : ?bool
    {
        $obj = $this->info($id);

        if (is_null($obj)) {
            return null;
        }

        $updated = $obj->update($data);

        if ( !$updated) {
            throw new CrudException('Update data fail.');
        }

        return (bool) $updated;
    }

    /**
     * @desc
     * @param int $id
     * @return bool|null
     */
    public function delete(int $id) : ?bool
    {
        $obj = $this->info($id);

        if (is_null($obj)) {
            return null;
        }

        $deleted = $obj->delete();

        if ( !$deleted) {
            throw new CrudException('Delete data fail.');
        }

        return (bool) $deleted;
    }

    /**
     * @desc
     * @param int $id
     * @param array|string[] $fields
     * @param array $relations
     * @param Closure|null $before
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function info(int $id, array $fields = ['id'], array $relations = [], Closure $before = null)
    {
        $query = $this->getModel()->sharding()->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        // 关联解析器
        if ( !empty($relations)) {
            $query = $this->relationResolver($query, $relations);
        }

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->first();

        return $row;
    }

    /**
     * @desc 恢复垃圾
     * @param int $id
     * @return bool|null
     */
    public function trashRestore(int $id) : ?bool
    {
        $obj = $this->trashInfo($id);

        if (is_null($obj)) {
            return null;
        }

        $restored = $obj->restore();

        if ( !$restored) {
            throw new CrudException('Restore data fail.');
        }

        return (bool) $restored;
    }

    /**
     * @desc 更新垃圾
     * @param int $id
     * @param array $data
     * @return bool|null
     */
    public function trashUpdate(int $id, array $data = []) : ?bool
    {
        $obj = $this->trashInfo($id);

        if (is_null($obj)) {
            return null;
        }

        $updated = $obj->update($data);

        if ( !$updated) {
            throw new CrudException('Update data fail.');
        }

        return (bool) $updated;
    }

    /**
     * @desc 垃圾信息
     * @param int $id
     * @param array|string[] $fields
     * @param array $relations
     * @param Closure|null $before
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function trashInfo(int $id, array $fields = ['id'], array $relations = [], Closure $before = null)
    {
        $query = $this->getModel()->sharding()->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        // 关联解析器
        if ( !empty($relations)) {
            $query = $this->relationResolver($query, $relations);
        }

        $row = $query->select($fields)->where($this->getModel()->getKeyName(), $id)->withTrashed()->first();

        return $row;
    }

    /**
     * @desc 快速批量创建
     * @param array $data
     * @return bool
     */
    public function fastBatchCreate(array $data) : bool
    {
        // 要先在外部设置分表因子
        $model = $this->getModel()->sharding();

        $tableName = $model->getTable();

        $totallyGuarded = $model->totallyGuarded();

        $insertData = [];

        foreach ($data as $index => $dataItem) {
            $tempData = [];
            foreach ($model->overwriteFillableFromArray($dataItem) as $key => $val) {
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
     * @desc 安全批量创建
     * @param array $data
     * @return array
     */
    public function safeBatchCreate(array $data) : array
    {
        DB::beginTransaction();

        try {
            $list = [];

            foreach ($data as $key => $dataItem) {
                if ( !isset($dataItem['_factor'])) {
                    throw new CrudException("Safe batch create fail, not found key _factor.");
                }

                $model = clone $this->getModel()->setFactor($dataItem['factor'])->sharding();

                if ( !$model->fill($dataItem)->save()) {
                    throw new CrudException("Safe batch create fail. index:{$key}");
                }

                $list[] = $model;
            }

            DB::commit();

            return $list;
        } catch (CrudException $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * @desc 安全批量更新
     * @param array $data
     * @param Closure|null $custom
     * @return array
     */
    public function safeBatchUpdate(array $data, Closure $custom = null) : array
    {
        DB::beginTransaction();

        try {
            $list = [];

            foreach ($data as $dataItem) {
                if ( !isset($dataItem['_factor'])) {
                    throw new CrudException("Safe batch update fail, not found key _factor.");
                }

                $cloneModel = clone $this->getModel()->setFactor($dataItem['_factor'])->sharding();

                $newQuery = $cloneModel->newQuery();

                if (is_null($custom)) {
                    if ( !isset($dataItem['_id'])) {
                        throw new CrudException("Safe batch update fail, not found key _id.");
                    }
                    $model = $newQuery->find($dataItem['_id']);
                } else {
                    $model = $custom($dataItem, $newQuery, $cloneModel);
                }

                if (is_null($model)) {
                    throw new CrudException("Safe batch update fail, instance is empty. _id:{$dataItem['_id']}");
                }

                if ( !($model instanceof Model)) {
                    throw new CrudException("Safe batch update fail, instance not instanceof Model. _id:{$dataItem['_id']}");
                }

                if ( !$model->fill($dataItem)->update()) {
                    throw new CrudException("Safe batch update fail. _id:{$dataItem['_id']}");
                }

                $list[] = $model;
            }

            DB::commit();

            return $list;
        } catch (CrudException $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @desc
     * @param array $ids
     * @param array $fields
     * @param array $relations
     * @param \Closure|null $before
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function many(array $ids, array $fields = ['id'], array $relations = [], \Closure $before = null)
    {
        $query = $this->getModel()->sharding()->newQuery();

        if ( !is_null($before)) {
            $before($query);
        }

        if ( !empty($relations)) {
            $query = $this->relationResolver($query, $relations);
        }

        $rows = $query->select($fields)->whereIn($this->getModel()->getKeyName(), $ids)->get();

        return $rows;
    }
}
