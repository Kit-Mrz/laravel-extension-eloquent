<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

use Closure;

interface BatchContract
{
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

    /**
     * @desc 安全批量更新
     * @param array $data 数据
     * @return array
     */
    public function safeBatchUpdate(array $data, Closure $custom = null) : array;
}
