<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

use Closure;

interface RepositoryContract
{
    /**
     * @desc 增
     * @param array $data 数据
     */
    public function create(array $data);

    /**
     * @desc 查
     * @return mixed
     */
    public function retrieve(array $fields = ['*'], array $relations = [], array $paginateParams = [], array $orderConfig = [], Closure $before = null, Closure $after = null);

    /**
     * @desc 改
     * @param int $id
     * @param array $data
     * @return bool|null
     */
    public function update(int $id, array $data) : ?bool;

    /**
     * @desc 删
     * @param int $id 主键
     * @return bool|null
     */
    public function delete(int $id) : ?bool;

    /**
     * @desc 信息
     * @param int $id
     * @param string[] $fields
     * @param string[] $relations
     * @param Closure|null $before
     * @return mixed
     */
    public function info(int $id, array $fields = ['id'], array $relations = ['with_' => true], Closure $before = null);

    /**
     * @desc 多个
     * @param array $ids
     * @param array $fields
     * @param array $relations
     * @param \Closure|null $before
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function many(array $ids, array $fields = ['id'], array $relations = [], Closure $before = null);
}
