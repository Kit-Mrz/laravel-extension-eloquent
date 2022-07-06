<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

/**
 * 控制器服务契约
 */
interface ControlServiceContract
{
    /**
     * @desc 列表
     * @param array $params
     * @return mixed
     */
    public function index(array $params);

    /**
     * @desc 保存
     * @param array $params
     * @return mixed
     */
    public function store(array $params);

    /**
     * @desc 信息
     * @param int $id
     * @return mixed
     */
    public function show(int $id);

    /**
     * @desc 更新
     * @param int $id
     * @param array $params
     * @return mixed
     */
    public function update(int $id, array $params);

    /**
     * @desc 删除
     * @param int $id
     * @return mixed
     */
    public function destroy(int $id);

    /**
     * @desc
     * @param array $ids
     * @param array $fields
     * @param array $relations
     * @param \Closure|null $before
     * @return mixed
     */
    //public function many(array $ids, array $fields = ['id'], array $relations = [], \Closure $before = null);
}
