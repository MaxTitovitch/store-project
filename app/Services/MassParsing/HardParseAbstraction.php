<?php

namespace App\Services\MassParsing;


use App\Exceptions\IncorrectException;

class HardParseAbstraction extends ParseAbstraction
{
    public function parseFormat() : bool {
        if(!$this->parser->parseProducts('hard')) {
            $this->throwIncorrectException();
            return false;
        } else {
            return true;
        }
    }

    public function throwIncorrectException()
    {
        throw new IncorrectException('Hard parsing Exception. File data is not correct');
    }
}