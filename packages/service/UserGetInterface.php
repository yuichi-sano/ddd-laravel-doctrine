<?php
namespace packages\Service;
use packages\Domain\Model\UserId;
use packages\Domain\Model\User;
interface UserGetInterface
{
/**
* @param UserId $userId
* @return User
*/
public function execute(UserId $userId): User;
}
