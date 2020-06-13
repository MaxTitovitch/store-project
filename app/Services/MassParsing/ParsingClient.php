<?php
/**
 * Created by PhpStorm.
 * Users: Max
 * Date: 05.06.2020
 * Time: 16:36
 */

namespace App\Services\MassParsing;


use App\Exceptions\IncorrectException;

class ParsingClient
{
    public function parse(string $path, string $typeFile, string $typeParsing) : string {
        $parser = $this->createParser($path, $typeFile);
        $parseAbstraction = $this->createParseAbstraction($parser, $typeParsing);
        try {
            $parseAbstraction->parseFormat();
            return "Success";
        } catch (IncorrectException $exception) {
            return $exception->getMessage();
        }
    }

    private function createParser(string $file, string $typeFile) : Parser {
        if($typeFile == 'json') {
            return new JsonParser ($file);
        } else if($typeFile == 'xml') {
            return new XmlParser ($file);
        }
        return null;
    }

    private function createParseAbstraction(Parser $parser, string $typeParsing) : ParseAbstraction {
        if($typeParsing == 'soft') {
            return new SoftParseAbstraction($parser);
        } else if($typeParsing == 'hard') {
            return new HardParseAbstraction($parser);
        }
        return null;
    }
}