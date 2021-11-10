<?php
namespace packages\Service;
use packages\Domain\Model\UserId;

interface UserGetInterface
{
/**
* @param UserId $userId
* @return UserGetResponse
*/
public function execute(UserId $userId);
}
