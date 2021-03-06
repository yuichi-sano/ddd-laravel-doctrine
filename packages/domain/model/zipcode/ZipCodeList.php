<?php

declare(strict_types=1);
namespace packages\domain\model\zipcode;
use Ramsey\Collection\Collection;

class ZipCodeList extends Collection
{
    /**
     * @return User[]
     */
    public function __construct()
    {
        parent::__construct(ZipCode::class);
    }

    /**
     * @return Task[]
     */
    public function getIterator(): \Traversable
    {
        return parent::getIterator(); // TODO: Change the autogenerated stub
    }

    /**
     * 連想配列にコンバートします
     * @return array
     */
    public function toArray(): array
    {
        $toArray=[];
        foreach ($this->data as $data){
            $toArray[$data->personal] = $data->address;
        }
        return $toArray;
    }
}
