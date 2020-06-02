<?php

namespace App\Http\Controllers\API;

use App\Product;
use App\Order;
use App\Http\Requests\ProductOrderRequest;
use Illuminate\Http\Request;

class ProductOrderController extends ApiController
{

    public function index(ProductOrderRequest $request)
    {
        $input = $request->all();
        $product = Product::find($input['product_id']);
        $order = Order::find($input['order_id']);
        $response = ['product' => $product, 'order' => $order];
        return $this->sendResponse($response, 'ProductOrders retrieved successfully.');
    }

    public function store(ProductOrderRequest $request)
    {
        $input = $request->all();
        $product = Product::find($input['product_id']);
        $order = Order::find($input['order_id']);
        if (!$this->isAtach($product, $order)) {
            $product->orders()->attach($order);
        }
        $response = ['product' => $product, 'order' => $order];
        return $this->sendResponse($response, 'ProductOrder created successfully.');
    }

    private function isAtach($product, $orderChecked)
    {
        if ($product->orders != null) {
            foreach ($product->orders as $order) {
                if ($orderChecked->id == $order->id) {
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

    public function destroy(ProductOrderRequest $request, $id)
    {
        $input = $request->all();
        $product = Product::find($input['product_id']);
        $order = Order::find($input['order_id']);
        $product->orders()->detach($order);
        $response = ['product' => $product, 'order' => $order];
        return $this->sendResponse($response, 'ProductOrder deleted successfully.');
    }
}
