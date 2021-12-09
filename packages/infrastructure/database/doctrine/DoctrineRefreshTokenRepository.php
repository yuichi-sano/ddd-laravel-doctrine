<?php

declare(strict_types=1);

namespace packages\infrastructure\database\doctrine;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use packages\domain\model\authentication\authorization\AuthenticationRefreshToken;
use packages\domain\model\authentication\authorization\RefreshToken;
use packages\domain\model\User\User;
use packages\domain\model\User\UserId;
use packages\infrastructure\database\RefreshTokenRepository;

class DoctrineRefreshTokenRepository extends EntityRepository implements RefreshTokenRepository
{

    public function save(AuthenticationRefreshToken  $authenticationRefreshToken): void
    {

        $query = $this->createNativeNamedQuery('upsert');
        try {
            $query->setParameters($authenticationRefreshToken->toArray());
            $query->execute();
        } catch (NoResultException $e) {
            throw $e;
        }
    }
}
