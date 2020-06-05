<?php

namespace App\Services\MassParsing;


abstract class ParseAbstraction
{
    protected $parser;


    public function __construct(Parser $parser) {
        $this->parser = $parser;
    }

    abstract public function parseFormat() : bool ;
}