<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\DateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagramController extends ApiController
{
    public function showLineChart(Request $request, $entity, $id, $param) {
        $input = $request->all();
        $input['quantity'] = empty($input['quantity']) ? 5 : $input['quantity'];
        if(!$this->isCorrectParams($entity, $param)) {
            return $this->sendError('Show error', 'Param isn\'t corrected.');
        }
        switch ($entity) {
            case 'product':
                if($param == 'orders') {
                    $data = DB::table('products')
                        ->join('product_order', 'products.id', '=', 'product_order.product_id')
                        ->join('orders', 'orders.id', '=', 'product_order.order_id')
                        ->select(DB::raw("products.name, orders.date"))
                        ->whereRaw('\''. $input['date_start'] . '\' < orders.date AND \'' . $input['date_end'] . '\' > orders.date')
                        ->where("products.id", $id)->get()->toArray();
                } else {
                    $entityId = $param == 'views' ? 'entity_id' : "{$entity}_id";
                    $data = DB::table('products')
                        ->join($param, "products.id", '=', "$param.$entityId")
                        ->select(DB::raw("products.name, DATE({$param}.created_at) as date"));
                    if($param == 'views') {
                        $data = $data->where("$param.entity_type", $entity);
                    }
                    $data = $data->whereRaw('\''. $input['date_start'] . '\' < ' . $param . '.created_at AND \'' . $input['date_end'] . '\' > ' . $param . '.created_at')
                        ->where("products.id", $id)->get()->toArray();
                }
                break;
            case 'post':
                $data = DB::table('posts')
                    ->join($param, "posts.id", '=', "$param.entity_id")
                    ->select(DB::raw("posts.name, DATE({$param}.created_at) as date"))
                    ->where("$param.entity_type", $entity)
                    ->whereRaw('\''. $input['date_start'] . '\' < ' . $param . '.created_at AND \'' . $input['date_end'] . '\' > ' . $param . '.created_at')
                    ->where('posts.id', $id)->get()->toArray();
                break;
            case 'user':
                $data = DB::table('users')
                    ->join('orders', 'users.id', '=', 'orders.user_id')
                    ->join('product_order', 'orders.id', '=', 'product_order.order_id')
                    ->select(DB::raw("users.last_name as name, orders.date"))
                    ->whereRaw('\''. $input['date_start'] . '\' < orders.date AND \'' . $input['date_end'] . '\' > orders.date')
                    ->where('users.id', $id)->get()->toArray();
                break;
            case 'sale-category':
                $data = DB::table('sale_categories')
                    ->join('sales', 'sale_categories.id', '=', 'sales.sale_category_id')
                    ->join('products', 'products.id', '=', 'sales.product_id')
                    ->join('product_order', 'products.id', '=', 'product_order.product_id')
                    ->join('orders', 'orders.id', '=', 'product_order.order_id')
                    ->select(DB::raw("`sale_categories`.name, orders.date"))
                    ->whereRaw('sales.date_start < orders.date AND sales.date_end > orders.date')
                    ->where('sale_categories.id', $id)->get()->toArray();
                break;
            default:
                return $this->sendError('Show error', 'Params aren\'t corrected.');
        }
        return $this->sendResponse($this->transformLineData($data, $input['date_start'], $input['date_end']), 'Data has got successfully.');
    }

    public function showBarChart(DateRequest $request, $entity, $param) {
        $input = $request->all();
        if(!$this->isCorrectParams($entity, $param)) {
            return $this->sendError('Show error', 'Param isn\'t corrected.');
        }
        switch ($entity) {
            case 'product':
                if($param == 'orders') {
                    $data = DB::table('products')
                        ->join('product_order', 'products.id', '=', 'product_order.product_id')
                        ->join('orders', 'orders.id', '=', 'product_order.order_id')
                        ->select(DB::raw("products.name, count(*) as count"))
                        ->whereRaw('\''. $input['date_start'] . '\' < orders.date AND \'' . $input['date_end'] . '\' > orders.date')
                        ->groupBy("products.id")
                        ->orderByRaw("`count` desc")->limit($input["quantity"])->get()->toArray();
                } else {
                    $entityId = $param == 'views' ? 'entity_id' : "{$entity}_id";
                    $data = DB::table('products')
                        ->join($param, "products.id", '=', "$param.$entityId")
                        ->select(DB::raw("products.name, count(*) as count"));
                    if($param == 'views') {
                        $data = $data->where("$param.entity_type", $entity);
                    }
                    $data = $data->whereRaw('\''. $input['date_start'] . '\' < products.created_at AND \'' . $input['date_end'] . '\' > products.created_at')
                        ->groupBy("products.id")
                        ->orderByRaw("`count` desc")->limit($input["quantity"])->get()->toArray();
                }
                break;
            case 'post':
                $data = DB::table('posts')
                    ->join($param, "posts.id", '=', "$param.entity_id")
                    ->select(DB::raw("posts.name, count(*) as count"))
                    ->where("$param.entity_type", $entity)
                    ->whereRaw('\''. $input['date_start'] . '\' < ' . $param . '.created_at AND \'' . $input['date_end'] . '\' > ' . $param . '.created_at')
                    ->groupBy("posts.id")
                    ->orderByRaw("`count` desc")->limit($input["quantity"])->get()->toArray();
                break;
            case 'user':
                $data = DB::table('users')
                    ->join('orders', 'users.id', '=', 'orders.user_id')
                    ->join('product_order', 'orders.id', '=', 'product_order.order_id')
                    ->select(DB::raw("users.last_name, count(*) as count"))
                    ->whereRaw('\''. $input['date_start'] . '\' < orders.date AND \'' . $input['date_end'] . '\' > orders.date')
                    ->groupBy("users.id")
                    ->orderByRaw("`count` desc")->limit($input["quantity"])->get()->toArray();
                break;
            case 'sale-category':
                $data = DB::table('sale_categories')
                    ->join('sales', 'sale_categories.id', '=', 'sales.sale_category_id')
                    ->join('products', 'products.id', '=', 'sales.product_id')
                    ->join('product_order', 'products.id', '=', 'product_order.product_id')
                    ->join('orders', 'orders.id', '=', 'product_order.order_id')
                    ->select(DB::raw("`sale_categories`.name, count(*) as count"))
                    ->whereRaw('sales.date_start < orders.date AND sales.date_end > orders.date')
                    ->groupBy("sale_categories.id")
                    ->orderByRaw("`count` desc")->limit($input["quantity"])->get()->toArray();
                break;
            default:
                return $this->sendError('Show error', 'Params aren\'t corrected.');
        }
        return $this->sendResponse($data, 'Data has got successfully.');
    }

    public function showPieChart($param) {
        switch ($param) {
            case 'sex':
                $data = DB::table('orders')
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw("count(*) as count, users.sex"))
                    ->groupBy("users.sex")->get()->toArray();
                break;
            case 'city':
                $data = DB::table('orders')
                    ->join('addresses', 'addresses.id', '=', 'orders.address_id')
                    ->select(DB::raw("count(*) as count, addresses.city"))
                    ->groupBy("addresses.city")->get()->toArray();
                break;
            case 'age':
                $data = DB::table('orders')
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw("((YEAR(CURRENT_DATE)-YEAR(users.date_of_birth))-(RIGHT(CURRENT_DATE,5)<RIGHT(users.date_of_birth,5))) as year, "
                        . "COUNT(((YEAR(CURRENT_DATE)-YEAR(users.date_of_birth))-(RIGHT(CURRENT_DATE,5)<RIGHT(users.date_of_birth,5)))) as count"))
                    ->groupBy(DB::raw('(YEAR(CURRENT_DATE)-YEAR(users.date_of_birth))-(RIGHT(CURRENT_DATE,5)<RIGHT(users.date_of_birth,5))'))
                    ->get()->toArray();
                $data = $this->convertAgeData($data);
                break;
            default:
                return $this->sendError('Show error', 'Param isn\'t corrected.');
        }
        return $this->sendResponse($data, 'Data has got successfully.');
    }

    private function convertAgeData($data) {
        $newData = [
            ['year' => '<= 20', 'count' => 0],
            ['year' => '21-30', 'count' => 0],
            ['year' => '31-45', 'count' => 0],
            ['year' => '=> 46', 'count' => 0]
        ];
        foreach ($data as $item) {
            if ($item->year <= 20){
                $newData[0]['count'] += $item->count;
            } elseif($item->year > 20 && $item->year <= 30) {
                $newData[1]['count'] += $item->count;
            } elseif($item->year > 30 && $item->year <= 45) {
                $newData[2]['count'] += $item->count;
            } else {
                $newData[3]['count'] += $item->count;
            }
        }
        return $this->removeEmptyData($newData);
    }

    private function removeEmptyData($data) {
        $newData = [];
        foreach ($data as $item) {
            if($item['count'] != 0) {
                $newData[] = ['year' => $item['year'], 'count' => $item['count']];
            }
        }
        return $newData;
    }

    private function isCorrectParams($entity, $param)
    {
        if(!in_array($entity, ['product', 'post', 'user', 'sale-category'])) {
            return false;
        } else if($entity == 'product' && !in_array($param, ['views', 'orders', 'rankings'])) {
            return false;
        } else if($entity == 'post' && !in_array($param, ['views', 'likes']) ) {
            return false;
        } else if ($entity == 'user' && $param != 'orders') {
            return false;
        } else if($entity == 'sale-category' && $param != 'orders') {
            return false;
        }
        return true;
    }

    private function transformLineData($data, $dateStart, $dateEnd)
    {
        if ($data == null) {
            return $data;
        }
        $newData = ['entity' => '', 'dates' => [] ]; $id = 0;
        $newData['dates'][$dateStart ] = $id;
        foreach ($data as $item) {
            $newData['dates'][$item->date] =  ++$id;
            $newData['entity'] = $item->name;
        }
        $newData['dates'][$dateEnd ] = $id;
        return $newData;
    }

}
