<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagramController extends ApiController
{
    public function showLineChart(Request $request, $entity, $param) {

    }

    public function showBarChart(Request $request, $entity, $param) {

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

}
