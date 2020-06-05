<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 05.06.2020
 * Time: 16:55
 */

namespace App\Services\MassParsing;


use App\Category;
use App\Exceptions\IncorrectException;
use Illuminate\Support\Facades\Storage;

abstract class Parser
{
    protected $file;

    public function __construct($file) {
        $this->file = Storage::get($file);
    }

    abstract public function parseProducts(string $type) : bool;

    abstract public function parseCategory(string $name, string $parent) : Category;

}