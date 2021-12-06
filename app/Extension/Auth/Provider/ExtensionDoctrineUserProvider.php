<?php

namespace App\Extension\Auth\Provider;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use packages\domain\model\authentication\Account;

class ExtensionDoctrineUserProvider extends DoctrineUserProvider
{
    public function setHasher(HasherContract $hasher){
        $this->hasher = $hasher;
    }

}
