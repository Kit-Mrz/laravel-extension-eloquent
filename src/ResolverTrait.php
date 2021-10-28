<?php

namespace Mrzkit\LaravelExtensionEloquent;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

trait ResolverTrait
{
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
    public function relationResolver(Builder $query, array $relations) : Builder
    {
        // 读取仓库的关联配置
        $relationConfigs = $this->relationConfig();

        $filterConfigs = [];

        foreach ($relations as $relationName => $relationParam) {
            // 抹掉左边的字符
            $name = substr($relationName, 5);

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

    /**
     * @desc 排序配置
     * @param string $orderTable 表名
     * @return \string[][]
     */
    abstract public function orderConfig(string $orderTable = '') : array;

    /**
     * @desc 排序解析器
     * @param Builder $query 查询构造器
     * @param string $orderKey 选择排序的键
     * @param string $orderTable 表名
     * @return Builder
     */
    public function orderResolver(Builder $query, string $orderKey = '', string $orderTable = '') : Builder
    {
        $oc = $this->orderConfig($orderTable);

        if (isset($oc[$orderKey])) {
            $conf = $oc[$orderKey];

            if (empty($orderTable)) {
                $query->orderBy($conf['key'], $conf['value']);
            } else {
                $query->orderBy($conf['orderTable'] . '.' . $conf['key'], $conf['value']);
            }
        }

        return $query;
    }
}
