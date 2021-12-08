<?php

namespace App\Http\Requests;
use App\Http\Requests\Definition\LoginDefinition;
use Doctrine\Common\Collections\Criteria;
use packages\domain\model\User\UserList;

class LoginRequest extends Basic\AbstractFormRequest
{
    public function __construct(LoginDefinition $definition = null)
    {
        parent::__construct($definition);
    }
    protected function transform(array $attrs): array
    {
        return $attrs;
    }
}
