<?php

namespace Mrzkit\LaravelExtensionEloquent\Model;

use Mrzkit\LaravelExtensionEloquent\Contracts\ShardFactorContract;
use Mrzkit\LaravelExtensionEloquent\Contracts\ShardingContract;

abstract class ShardModel extends EloquentModel implements ShardingContract, ShardFactorContract
{
    use Positioner;

    /**
     * @var int 最大分表数
     */
    protected $shardMaxCount = 64;

    /**
     * @var \int[][] 分表配置
     */
    protected $shardConfig = [
        [
            'partition' => 8,
            'low'       => 0,
            'high'      => 7,
        ],
        [
            'partition' => 16,
            'low'       => 8,
            'high'      => 15,
        ],
        [
            'partition' => 24,
            'low'       => 16,
            'high'      => 23,
        ],
        [
            'partition' => 32,
            'low'       => 24,
            'high'      => 31,
        ],
        [
            'partition' => 40,
            'low'       => 32,
            'high'      => 39,
        ],
        [
            'partition' => 48,
            'low'       => 40,
            'high'      => 47,
        ],
        [
            'partition' => 56,
            'low'       => 48,
            'high'      => 55,
        ],
        [
            'partition' => 64,
            'low'       => 56,
            'high'      => 63,
        ],
    ];

    /**
     * @desc 获取最大分表数
     * @return int $this->shardMaxCount
     */
    abstract public function getShardMaxCount() : int;

    /**
     * @desc 获取分表配置
     * @return array $this->shardConfig
     */
    abstract public function getShardConfig() : array;

    /**
     * @desc 获取当前分表数
     * @return int
     */
    public function getShardCount() : int
    {
        return count($this->getShardConfig());
    }

    /**
     * @desc 匹配分区因子
     * @return int
     */
    public function getMatchFactor() : int
    {
        $factor = -1;

        if ($this->getShardCount() > 0) {
            if (preg_match('/_(\d+)$/', $this->getTable(), $matches)) {
                if (isset($matches[1])) {
                    $factor = (int) $matches[1];
                    $factor = $factor - 1;
                }
            }
        }

        return $factor;
    }

}
