<?php

declare(strict_types=1);

namespace packages\Domain\Model;

class UserId
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function value(): int
    {
        return $this->id;
    }

}
