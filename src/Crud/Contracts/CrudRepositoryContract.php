<?php

namespace Mrzkit\LaravelExtensionEloquent\Crud\Contracts;

interface CrudRepositoryContract
{
    /**
     * @desc 增
     * @param array $data
     * @return mixed
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
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * @desc 删
     * @param int $id
     * @return mixed
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
    public function detail(int $id, array $fields = []);

    /**
     * @desc 详情(查软删)
     * @param int $id
     * @param array $fields 查询字段
     * @return mixed
     */
    public function detailWithTrashed(int $id, array $fields = []);

    /**
     * @desc 批量创建
     * @param array $data
     * @return bool
     */
    public function batchCreate(array $data);
}
