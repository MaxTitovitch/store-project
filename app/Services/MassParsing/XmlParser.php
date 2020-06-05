<?php

namespace App\Services\MassParsing;


use App\Category;

class XmlParser extends Parser
{

    public function parseProducts(string $type) : bool {
        return true;
    }

    public function parseCategory(string $name, string $parent): Category {
        return null;
    }
}