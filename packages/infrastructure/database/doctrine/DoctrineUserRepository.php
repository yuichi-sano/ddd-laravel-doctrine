<?php

declare(strict_types=1);

namespace packages\Infrastructure\Database\Doctrine;
use Doctrine\ORM\EntityRepository;
use packages\Domain\Model\User;
use packages\Domain\Model\UserId;
use packages\Infrastructure\Database\UserRepository;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{

    public function findUser(UserId $userId): User
    {
        $query = $this->createNativeNamedQuery('dd');
        return $query->getSingleResult();
    }

    public function add(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }
}
