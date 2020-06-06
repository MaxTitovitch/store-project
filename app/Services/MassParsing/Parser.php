<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 05.06.2020
 * Time: 16:55
 */

namespace App\Services\MassParsing;


use App\Category;
use App\Characteristic;
use App\CharacteristicValue;
use App\Product;
use App\ProductCharacteristic;
use Illuminate\Support\Facades\Storage;

abstract class Parser
{
    protected $file;

    public function __construct($file) {
        $this->file = Storage::get($file);
    }

    abstract public function parseProducts(string $type) : bool;

    abstract protected function parseProduct(object $xmlCategory) : void;

    protected function parseCategory(string $name, ?string $parentName): Category {
        $category = Category::findOrCreate($name);
        if(!$category) {
            $category = new Category();
            $category->name = $name;
            if ($parentName == null) {
                $category->parent_id = null;
            } else {
                $parent = Category::findOrCreate($parentName);
                if ($parent != false) {
                    $category->parent_id = $parent->id;
                } else {
                    $parent = new Category();
                    $parent->name = $parentName;
                    $parent->parent_id = null;
                    $parent->save();
                    $category->parent_id = $parent->id;
                }
            }
        }
        $category->save();
        return $category;
    }


    protected function parsePhotos(iterable $photo, string $slug)
    {
        $id = 1;
        foreach ($photo as $item) {
            try {
                if (gettype($item) != "string") $item = trim($item->__toString());
                var_dump($item, $slug, $id);
                $file = file_get_contents($item);
                var_dump("FILE", $file);
                Storage::put('public/products/' . $slug . '-' . $id . '.png', $file);
                $id++;
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }
}