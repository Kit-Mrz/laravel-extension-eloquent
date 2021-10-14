<?php

namespace Mrzkit\LaravelExtensionEloquent\Crud\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * 基本增删改查数据仓库契约
 */
interface CrudRepositoryContract
{
    /**
     * @desc 增
     * @param array $data 新增数据
     * @return Model
     */
    public function create(array $data);

    /**
     * @desc 查
     * @return mixed
     */
    public function retrieve();

    /**
     * @desc 改
     * @param int $id
     * @param array $data
     * @return bool|null
     */
    public function update(int $id, array $data);

    /**
     * @desc 删
     * @param int $id 主键
     * @return bool|null
     */
    public function delete(int $id);

    /**
     * @desc 改(改软删)
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateWithTrashed(int $id, array $data);

    /**
     * @desc 详情
     * @param int $id
     * @param array $fields 查询字段
     * @return mixed
     */
    public function detail(int $id, array $fields = ['id']);

    /**
     * @desc 详情(查软删)
     * @param int $id
     * @param array $fields 查询字段
     * @return mixed
     */
    public function detailWithTrashed(int $id, array $fields = ['id']);

    /**
     * @desc 快速批量创建
     * @param array $data 数据
     * @return bool
     */
    public function fastBatchCreate(array $data) : bool;

    /**
     * @desc 安全批量创建
     * @param array $data 数据
     * @return array
     */
    public function safeBatchCreate(array $data) : array;
}
