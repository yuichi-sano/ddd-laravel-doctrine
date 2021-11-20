<?php

namespace packages\Domain\Basic\Mail;

use Ramsey\Collection\Collection;

class AddressList extends Collection
{
    /**
     * @return Address[]
     */
    public function __construct()
    {
        parent::__construct(Address::class);
    }

    /**
     * @return Address[]
     */
    public function getIterator(): \Traversable
    {
        return parent::getIterator();
    }

}


