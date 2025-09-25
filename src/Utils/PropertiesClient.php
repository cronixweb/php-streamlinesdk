<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Models\Property;
use Cronixweb\Streamline\Traits\ModelClient;

class PropertiesClient extends ModelClient
{
    protected string $modelName = Property::class;
    protected string $primaryKey = "unit_id";
    protected string $findOneMethod = "GetPropertyInfo";
    protected string $findAllMethod = "GetPropertyList";

    public function reviews(){
        return new ReviewsClient()
    }

}