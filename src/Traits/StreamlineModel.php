<?php

namespace Cronixweb\Streamline\Traits;

abstract class StreamlineModel
{
    public static abstract function parse(array $data): self;
}