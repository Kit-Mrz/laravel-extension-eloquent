<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface ResolverContract
{
    /**
     * @desc 关联配置
     * @return array
     */
    public function relationConfig() : array;

    /**
     * @desc 关联解析器
     * @param Builder $query 查询构造器
     * @param array $relations 关联配置
     * @return Builder
     */
    public function relationResolver(Builder $query, array $relations) : Builder;

    /**
     * @desc 排序配置
     * @param string $orderTable 表名
     * @return array
     */
    public function orderConfig(string $orderTable = '') : array;

    /**
     * @desc 排序解析器
     * @param Builder $query 查询构造器
     * @param string $orderKey 选择排序的键
     * @param string $orderTable 表名
     * @return Builder
     */
    public function orderResolver(Builder $query, string $orderKey = '', string $orderTable = '') : Builder;
}
