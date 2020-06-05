<?php

namespace App\Services\MassParsing;

use App\Category;
use App\Product;
use Illuminate\Support\Facades\DB;

class JsonParser extends Parser
{
    private $categories = [];

    public function parseProducts(string $type) : bool {
        $jsonData = json_decode($this->file, false, 10);
        foreach ($jsonData as $jsonCategory) {
            try {
                DB::transaction(function () use ($jsonCategory) {
                    $category = $this->parseCategory($jsonCategory->name, $jsonCategory->parent);
                    foreach ($jsonCategory->products as $jsonProduct) {
                        $product = new Product();
                        $product->name = $jsonProduct->name;
                        $product->slug = str_slug($jsonProduct->name);
                        $product->price = $jsonProduct->price;
                        $product->description = $jsonProduct->description;
                        $product->category_id = $category->id;
                        $product->save();
                        var_dump($jsonProduct);
                        echo ",";
                    }
                });
            } catch (\Exception $ex) {
                if($type == 'hard') return false;
            }
        }
        return true;
    }

    private function parseCategories() : void {

    }

    public function parseCategory(string $name, string $parentName): Category {
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
//                    $parent->save();
                    $category->parent_id = $parent->id;
                }
            }
        }
//        $category->save();
        return $category;
    }
}