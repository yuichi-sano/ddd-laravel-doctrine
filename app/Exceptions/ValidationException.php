<?php

namespace App\Exceptions;

class ValidationException extends WebAPIException
{
    /** 例外コード */
    protected $errorCode;
    /** 例外メッセージ */
    protected $errorMessage;

    /**
     * ValidationException constructor.
     * @param string $code
     * @param array $validationErrors
     */
    public function __construct(string $code = 'W_0000000', array $validationErrors = [])
    {
        $this->errorCode = $code;
        $this->errorMessage = __('messages.' . $code);
        parent::__construct($this->errorCode, $validationErrors, parent::HTTP_STATUS_BAD_REQUEST);
    }

    /**
     * 例外内容を成形しJson形式で返します。
     *
     * @return array|Response
     */
    public function render()
    {
        return response()->json(
            [
                'state' => $this->errorCode,
                'message' => $this->errorMessage,
            ],
            parent::HTTP_STATUS_BAD_REQUEST,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
