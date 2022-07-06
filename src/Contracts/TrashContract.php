<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

use Closure;

interface TrashContract
{
    /**
     * @desc 垃圾恢复
     * @param int $id
     * @return bool|null
     */
    public function trashRestore(int $id) : ?bool;

    /**
     * @desc 垃圾更新
     * @param int $id
     * @param array $data
     * @return bool|null
     */
    public function trashUpdate(int $id, array $data = []) : ?bool;

    /**
     * @desc 垃圾信息
     * @param int $id
     * @param array|string[] $fields
     * @param array $relations
     * @param Closure|null $before
     * @return mixed
     */
    public function trashInfo(int $id, array $fields = ['id'], array $relations = [], Closure $before = null);
}
