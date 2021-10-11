<?php

namespace Mrzkit\LaravelExtensionEloquent\Partitions\Contracts;

interface PartitionRepositoryContract
{
    /**
     * @desc 增
     * @param int $partitionFactor 分表因子
     * @param array $data 新增数据
     * @return mixed
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
     * @return mixed
     */
    public function partitionUpdate(int $partitionFactor, int $id, array $data);

    /**
     * @desc 删
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @return mixed
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
     * @param array $fields 查询字段
     * @return mixed
     */
    public function partitionDetail(int $partitionFactor, int $id, array $fields = []);

    /**
     * @desc 详情(查软删)
     * @param int $partitionFactor 分表因子
     * @param int $id 主键
     * @param array $fields 查询字段
     * @return mixed
     */
    public function partitionDetailWithTrashed(int $partitionFactor, int $id, array $fields = []);

    /**
     * @desc 分表批量添加
     * @param int $partitionFactor 分表因子
     * @param array $data
     * @return bool
     */
    public function partitionBatchCreate(int $partitionFactor, array $data);
}
