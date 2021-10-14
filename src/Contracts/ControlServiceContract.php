<?php

namespace Mrzkit\LaravelExtensionEloquent\Contracts;

/**
 * 控制器服务契约
 */
interface ControlServiceContract
{
    public function index(array $params);

    public function store(array $params);

    public function show(int $id);

    public function update(int $id, array $params);

    public function destroy(int $id);

    public function detail(int $id, array $fields = ['id']);
}
