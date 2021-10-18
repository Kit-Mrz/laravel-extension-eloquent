<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 分表增删改查数据仓库契约
 */
interface PartitionRepositoryContract
{
    /**
     * @desc 增
     * @param int $partitionFactor 分表因子
     * @param array $data 新增数据
     * @return Model
     */
    public function partitionCreate(int $partitionFactor, array $data);

    /**
     * @desc 查
     * @param int $partitionFactor 分表因子
     * @return mixed
     */
    public function partitionRetrieve(int $partitionFactor);

    /**
     * @desc 改
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return bool|null
     */
    public function partitionUpdate(int $partitionFactor, int $id, array $data);

    /**
     * @desc 删
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @return bool|null
     */
    public function partitionDelete(int $partitionFactor, int $id);

    /**
     * @desc 改(改软删)
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $data 更新的数据
     * @return mixed
     */
    public function partitionUpdateWithTrashed(int $partitionFactor, int $id, array $data);

    /**
     * @desc 详情
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 查询前处理
     * @return mixed
     */
    public function partitionDetail(int $partitionFactor, int $id, array $fields = ['id'], Closure $before = null);

    /**
     * @desc 详情(查软删)
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array|string[] $fields 查询字段
     * @param Closure|null $before 查询前处理
     * @return mixed
     */
    public function partitionDetailWithTrashed(int $partitionFactor, int $id, array $fields = ['id'], Closure $before = null);

    /**
     * @desc 快速批量创建
     * @param int $partitionFactor 分表因子
     * @param array $data 数据
     * @return bool
     */
    public function partitionFastBatchCreate(int $partitionFactor, array $data) : bool;

    /**
     * @desc 安全批量创建
     * @param int $partitionFactor 分表因子
     * @param array $data 数据
     * @return array
     */
    public function partitionSafeBatchCreate(int $partitionFactor, array $data) : array;

    /**
     * @desc 关联配置
     * @return array
     */
    public function relationConfig() : array;

    /**
     * @desc 关联解析器
     * @param Builder $query
     * @param array $relations 关联配置
     * @return mixed
     */
    public function relationResolver(Builder $query, array $relations);
}
