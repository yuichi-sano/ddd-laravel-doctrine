<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\AbstractJsonResource;
use App\Http\Resources\Definition\SampleDefinition;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 * Class AddressStateListResource
 * @package App\Http\Resources
 */
class SampleResource extends AbstractJsonResource
{
    public static function buildResult($result): SampleResource
    {
        $address = new SampleDefinition();
        $address->addAddresses($result);
        return new SampleResource($address);
    }
}
