<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface ResolverContract
{
    /*
    * return [
    *   'baserUsers' => (function ($where) {
    *        return function (HasOne $query) use ($where) {
    *      };
    *   }),
    * ];
    */
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
     * return [
     *     '-id' => [
     *     'orderTable' => $orderTable,
     *     'key'        => 'id',
     *     'value'      => 'desc',
     *   ],
     *   '+id' => [
     *     'orderTable' => $orderTable,
     *     'key'        => 'id',
     *     'value'      => 'asc',
     *   ],
     * ];
     */
    /**
     * @desc 排序配置
     * @return array
     */
    public function orderConfig() : array;

    /**
     * @desc 排序解析器
     * @param Builder $query 查询构造器
     * @param string $orderKey 选择排序的键
     * @return Builder
     */
    public function orderResolver(Builder $query, string $orderKey = '') : Builder;
}
