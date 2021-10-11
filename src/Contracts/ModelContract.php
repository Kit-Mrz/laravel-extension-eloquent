<?php
namespace Mrzkit\LaravelExtensionEloquent\Contracts;

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
