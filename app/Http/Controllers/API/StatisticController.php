<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Product;
use App\View;
use Illuminate\Support\Facades\DB;

class StatisticController  extends ApiController {
    public function index()
    {
        $data = [
            'products' => [
                'name' => 'Товары',
                'link' => '/admin/products',
                'value' =>   Product::all()->count(),
            ],
            'views' => [
                'name' => 'Просмотры',
                'link' => '/admin/schedule?entity=product&param=views',
                'value' => View::where('entity_type', 'product')->get()->count(),
            ],
            'product-orders' => [
                'name' => 'Продано',
                'link' => '/admin/orders',
                'value' =>  DB::table('product_order')->get()->count(),
            ],
        ];
        return $this->sendResponse($data, 'Statistic retrieved successfully.');
    }
}