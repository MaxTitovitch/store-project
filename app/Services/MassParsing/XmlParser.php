<?php

namespace App\Services\MassParsing;

use App\Category;
use App\Characteristic;
use App\CharacteristicValue;
use App\Product;
use App\ProductCharacteristic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;

class XmlParser extends Parser
{

    public function parseProducts(string $type) : bool {
        $xmlData = new SimpleXMLElement($this->file);
        if($type == 'hard') {
            try {
                DB::transaction(function () use ($xmlData) {
                    foreach ($xmlData->category as $xmlCategory) {
                        $this->parseProduct($xmlCategory);
                    }
                });
            } catch (\Exception $ex) {
                return false;
            }
        } else {
            foreach ($xmlData->category as $xmlCategory) {
                try {
                    DB::transaction(function () use ($xmlCategory) {
                        $this->parseProduct($xmlCategory);
                    });
                } catch (\Exception $ex) {
                    $ex->getMessage();
                }
            }
        }
        return true;
    }

    protected function parseProduct(object $xmlCategory) : void {
        $category = $this->parseCategory($xmlCategory->name, $xmlCategory->parent);
        foreach ($xmlCategory->product as $xmlProduct) {
            $product = new Product();
            $product->name = $xmlProduct->name;
            $product->slug = str_slug($xmlProduct->name);
            $product->price = $xmlProduct->price;
            $product->description = $xmlProduct->description;
            $product->category_id = $category->id;
            $product->save();
            $this->parseCharacteristics($xmlProduct->param, $category, $product);
            $this->parsePhotos($xmlProduct->photo, $product->slug);
        }
    }

    private function parseCharacteristics(iterable $xmlCharacteristics, Category $category, Product $product) : void {
        foreach ($xmlCharacteristics as $xmlCharacteristic) {
            $characteristic = Characteristic::findByNameAndCategory($xmlCharacteristic->name, $category);
            if (!$characteristic) {
                $characteristic = new Characteristic();
                $characteristic->name = $xmlCharacteristic->name;
                if ($xmlCharacteristic->value->__toString() == "true" || $xmlCharacteristic->value->__toString() == "false") {
                        $characteristic->type = 'boolean';
                } else if(is_numeric($xmlCharacteristic->value->__toString())) {
                    $characteristic->type = 'number';
                    $characteristic->int_value_start = $xmlCharacteristic->value;
                    $characteristic->int_value_end = $xmlCharacteristic->value;
                } else {
                    $characteristic->type = 'string';
                }
                $characteristic->save();
            }
            $characteristic->categories()->sync($category->id, false);
            $productCharacteristic = new ProductCharacteristic();
            $productCharacteristic->product_id = $product->id;
            $productCharacteristic->characteristic_id = $characteristic->id;
            switch ($characteristic->type) {
                case 'boolean':
                    $productCharacteristic->boolean_value = $xmlCharacteristic->value->__toString() == 'true' ? true : false;
                    break;
                case 'number':
                    $characteristic->updateExtremum($xmlCharacteristic->value);
                    $productCharacteristic->number_value = $xmlCharacteristic->value;
                    break;
                case 'string':
                    $characteristicValue = CharacteristicValue::createOrUpdate($xmlCharacteristic->value, $characteristic);
                    $productCharacteristic->string_value = $characteristicValue->value;
                    break;
            }
            $productCharacteristic->save();
        }
    }

}