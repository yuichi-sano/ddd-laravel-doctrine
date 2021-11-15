<?php
namespace packages\Service;
use packages\Domain\Model\UserId;
use packages\Domain\Model\User;
use packages\Infrastructure\Database\UserRepository;

class UserGetInteractor implements UserGetInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(UserId $userId): User
    {
        $user = $this->userRepository->findUser(new UserId($userId->getId()));

        return $user;


    }
}
