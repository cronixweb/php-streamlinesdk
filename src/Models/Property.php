<?php

namespace Cronixweb\Streamline\Models;


use Cronixweb\Streamline\Traits\StreamlineModel;

class Property extends StreamlineModel
{
    public static function parse(array $data): Property
    {
        return new Property();
    }
}