<?php

namespace App\Services\MassParsing;

use App\Category;
use App\Characteristic;
use App\CharacteristicValue;
use App\Product;
use App\ProductCharacteristic;
use Illuminate\Support\Facades\DB;

class JsonParser extends Parser
{
    private $categories = [];

    public function parseProducts(string $type) : bool {
        $jsonData = json_decode($this->file, false, 10);
        if($type == 'hard') {
            try {
                DB::transaction(function () use ($jsonData) {
                    foreach ($jsonData as $jsonCategory) {
                        $this->parseProduct($jsonCategory);
                    }
                });
            } catch (\Exception $ex) {
                return false;
            }
        } else {
            foreach ($jsonData as $jsonCategory) {
                try {
                    DB::transaction(function () use ($jsonCategory) {
                        $this->parseProduct($jsonCategory);
                    });
                } catch (\Exception $ex) {
                    $ex->getMessage();
                }
            }
        }
        return true;
    }

    private function parseProduct(object $jsonCategory) : void {
        $category = $this->parseCategory($jsonCategory->name, $jsonCategory->parent);
        foreach ($jsonCategory->products as $jsonProduct) {
            $product = new Product();
            $product->name = $jsonProduct->name;
            $product->slug = str_slug($jsonProduct->name);
            $product->price = $jsonProduct->price;
            $product->description = $jsonProduct->description;
            $product->category_id = $category->id;
            $product->save();
            $this->parseCharacteristics($jsonProduct->characteristics, $category, $product);
            var_dump($jsonProduct);
            echo ",";
        }
    }

    public function parseCategory(string $name, ?string $parentName): Category {
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

    private function parseCharacteristics(array $jsonCharacteristics, Category $category, Product $product) : void {
        foreach ($jsonCharacteristics as $jsonCharacteristic) {
            $characteristic = Characteristic::findByNameAndCategory($jsonCharacteristic->name, $category);
            if (!$characteristic) {
                $characteristic = new Characteristic();
                $characteristic->name = $jsonCharacteristic->name;
                switch (gettype($jsonCharacteristic->value)) {
                    case 'boolean':
                        $characteristic->type = 'boolean';
                        break;
                    case 'integer': case 'double':
                        $characteristic->type = 'number';
                        $characteristic->int_value_start = $jsonCharacteristic->value;
                        $characteristic->int_value_end = $jsonCharacteristic->value;
                        break;
                    case 'string':
                        $characteristic->type = 'string';
                        break;
                }
                $characteristic->save();
            }
            $characteristic->categories()->sync($category->id, false);
            $productCharacteristic = new ProductCharacteristic();
            $productCharacteristic->product_id = $product->id;
            $productCharacteristic->characteristic_id = $characteristic->id;
            switch ($characteristic->type) {
                case 'boolean':
                    $productCharacteristic->boolean_value = $jsonCharacteristic->value;
                    break;
                case 'number':
                    $characteristic->updateExtremum($jsonCharacteristic->value);
                    $productCharacteristic->number_value = $jsonCharacteristic->value;
                    break;
                case 'string':
                    $characteristicValue = CharacteristicValue::createOrUpdate($jsonCharacteristic->value, $characteristic);
                    $productCharacteristic->string_value = $characteristicValue->value;
                    break;
            }
            $productCharacteristic->save();
        }
    }
}