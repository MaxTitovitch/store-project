<?php

namespace App\Http\Controllers\API;

use App\Product;
use App\Order;
use App\Http\Requests\ProductOrderRequest;
use Illuminate\Http\Request;

class ProductOrderController extends ApiController
{


    public function update(ProductOrderRequest $request, $id)
    {
        $input = $request->all();
        $order = Order::find($id);
        if(!isset($input['products'])) $input['products'] = [];
        $order->products()->sync($input['products']);
        $response = ['order' => $order, 'products' => $order->products];
        $this->changeOrderTotal($order);
        return $this->sendResponse($response, 'ProductOrder created successfully.');
    }

    public function updateUser(ProductOrderRequest $request, $id)
    {
        $input = $request->all();
        $order = Order::find($id);
        $user = auth('api')->user();
        if($user) {
            if ($order->user_id != $user->id) {
                return $this->sendError('It isn\'t your order', 'Go back!!!.');
            }
        }
        if(!isset($input['products'])) $input['products'] = [];
        $order->products()->sync($input['products']);
        $response = ['order' => $order, 'products' => $order->products];
        $this->changeOrderTotal($order);
        return $this->sendResponse($response, 'ProductOrder created successfully.');
    }

    private function changeOrderTotal($order) {
        $total = 0;
        foreach ($order->products as $product) {
            $productNew = Product::with(['sales'=>function ($query) use ($order) {
                $date = $order->date;
                $query->whereRaw("sales.date_start < '$date' AND sales.date_end > '$date'");
            }])->find($product->id);
            if(count($productNew->sales) > 0) {
                $total += (((int)($productNew->price - ($productNew->price * $productNew->sales[0]->percent / 100)))*100)/100 * $product->pivot->quantity;
            } else {
                $total += $productNew->price * $product->pivot->quantity;
            }
        }
        $order->total = $total;
        $order->save();
    }
}
