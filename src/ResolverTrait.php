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
        $relationConfigs = $this->relationConfig();

        $filterConfigs = [];

        $nameSets = [];

        foreach ($relations as $relationName => $relationParam) {
            //
            $name = substr($relationName, 5);

            if ( !isset($relationConfigs[$name])) {
                throw new InvalidArgumentException("没有此关联配置: {$name}");
            }

            if ($relationConfigs[$name] instanceof \Closure) {
                // Old
                $relationConfig = $relationConfigs[$name];

                if (isset($nameSets[$name])) {
                    throw new InvalidArgumentException("关联配置引用重复: {$name}");
                } else {
                    $nameSets[$name] = true;
                }
            } else if (is_array($relationConfigs[$name]) && isset($relationConfigs[$name]['relation']) && isset($relationConfigs[$name]['call'])) {
                // New
                if ($relationConfigs[$name]['call'] instanceof \Closure) {
                    //
                    $relationConfig = $relationConfigs[$name]['call'];

                    $name = $relationConfigs[$name]['relation'];

                    if (isset($nameSets[$name])) {
                        throw new InvalidArgumentException("关联配置引用重复: {$name}");
                    } else {
                        $nameSets[$name] = true;
                    }
                }
            } else {
                throw new InvalidArgumentException("关联配置结构错误: {$name}");
            }

            if (is_bool($relationParam) && $relationParam && $relationConfig instanceof \Closure) {
                //
                $filterConfigs[$name] = $relationConfig([]);
                //
            } else if (is_array($relationParam) && !empty($relationParam) && $relationConfig instanceof \Closure) {
                //
                $filterConfigs[$name] = $relationConfig($relationParam);
                //
            } else {
                throw new InvalidArgumentException('关联参数类型或配置错误:' . gettype($relationParam));
            }
        }

        if (empty( !$filterConfigs)) {
            $query->with($filterConfigs);
        }

        return $query;
    }

    /**
     * @desc 排序配置
     * @return \string[][]
     */
    abstract public function orderConfig(string $orderTable = '') : array;

    /**
     * @desc 排序解析器
     * @param Builder $query 查询构造器
     * @param string $orderKey 选择排序的键
     * @return Builder
     */
    public function orderResolver(Builder $query, string $orderKey = '', string $orderTable = '') : Builder
    {
        $config = $this->orderConfig($orderTable);

        if (isset($config[$orderKey])) {
            $conf = $config[$orderKey];

            if (empty($conf['orderTable'])) {
                $query->orderBy($conf['key'], $conf['value']);
            } else {
                $query->orderBy($conf['orderTable'] . '.' . $conf['key'], $conf['value']);
            }
        }

        return $query;
    }
}
