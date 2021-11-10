<?php

declare(strict_types=1);

namespace packages\Domain\Model;

class User
{
    private int $id;
    private string $name;
    private Address $address;

    public function __construct(int $id, string $name, Address $address)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    public function hasGrants(): boolean
    {
        return $this->id == 1 ? true:false;
    }
}
