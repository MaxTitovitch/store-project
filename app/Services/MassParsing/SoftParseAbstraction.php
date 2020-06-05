<?php

namespace App\Services\MassParsing;


class SoftParseAbstraction extends ParseAbstraction
{
    public function parseFormat() : bool {
        return $this->parser->parseProducts('soft');
    }
}