<?php
namespace packages\Service;
use packages\Domain\Model\UserId;
use packages\Infrastructure\Database\UserRepository;

class UserGetInteractor implements UserGetInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(UserId $userId): UserGetOutputData
    {
        $user = $this->userRepository->findUser(new UserId($userId->getId()));

        return new UserGetOutputData(
            $user->getId(),
            $user->getName(),
            $user->getAddress()->street(),
            $user->getAddress()->postalCode(),
            $user->getAddress()->city(),
            $user->getAddress()->country(),
        );


    }
}
