<?php

namespace backend\responses;

/**
 * Structured HTTP API Response Class
 *
 * @property int $code
 * @property string $msg
 * @property array|object $data
 */
class ApiCode
{
    /**
     *  状态码：成功
     */
    const CODE_SUCCESS = 200;

    /**
     * 状态码：失败
     */
    const CODE_ERROR = 400;

    /**
     * 状态码：未登录
     */
    const CODE_NOT_LOGIN = 401;

    /**
     * 状态码：商城禁用
     */
    const CODE_STORE_DISABLED = 402;
}