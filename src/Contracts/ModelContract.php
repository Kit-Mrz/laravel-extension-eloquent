<?php
namespace Mrzkit\LaravelExtensionEloquent\Contracts;

/**
 * 模型契约
 */
interface ModelContract
{
    /**
     * @desc 设置模型
     * @param $model
     * @return mixed
     */
    public function setModel($model);

    /**
     * @desc 获取模型
     * @return mixed
     */
    public function getModel();
}
