<?php

namespace App\Http\Resources\Definition;

use App\Http\Resources\Definition\Basic\ResultDefinitionInterface;
use Illuminate\Http\Resources\Json\JsonResource;


class SampleDefinition implements ResultDefinitionInterface
{


    //住所候補リスト
    protected $addresses = [];



    /**
     * @return mixed
     */
    public function getAddresses()
    {
        return $this->addresses;
    }



    /**
     * @param mixed addresses
     */
    public function addAddresses( $addresses): void
    {
        $this->addresses[] = $addresses;
    }

    /**
     * @param mixed addresses
     */
    public function setAddresses(array $addresses): void
    {
        foreach($addresses as $unit){
           $this->addAddresses($unit);
        }
    }



    /**
     * toArray
     * 階層構造の場合、階層部も生成し詰めなおす
     * @param $request
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $val) {
            $result[$key] = is_array($val) ? $this->childConvValue($val) : $this->convValue($val);
        }
        return $result;
    }

    /**
     * 渡された配列の中身を適宜生成し詰めなおしたものをreturn
     * @param $val
     * @return array
     */
    private  function childConvValue($vals){
        $value = array();
        foreach ($vals as $key=> $val){
            $value[$key] = $this->convValue($val);
        }
        return $value;
    }

    /**
     * プロパティの中身にオブジェクト階層があれば生成してreturn
     * @param $val
     * @return mixed
     */
    private function convValue($val){
        return $val instanceof ResultDefinitionInterface ? $val->toArray() : $val;
    }

}
