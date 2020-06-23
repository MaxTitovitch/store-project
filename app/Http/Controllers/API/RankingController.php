<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\RankingRequest;
use App\Ranking;
use Illuminate\Http\Request;

class RankingController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Rankings retrieved successfully.');
    }

    public function store(RankingRequest $request)
    {
        $input = $request->all();
        $entity = Ranking::create($input);        
        $this->changeRankingTotal($entity);
        return $this->sendResponse($entity->toArray(), 'Ranking created successfully.');
    }

    public function storeUser(RankingRequest $request)
    {
        $user = auth('api')->user();
        $input = $request->all();
        $input['user_id'] = $user->id;
        $entity = Ranking::create($input);
        $this->changeRankingTotal($entity);
        return $this->sendResponse($entity->toArray(), 'Ranking created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Ranking::find($id);
        if (is_null($entity)) {
            return $this->sendError('Ranking not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Ranking retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Ranking::select();
//        if(isset($input['trash'])) {
//            $entity = $entity->onlyTrashed();
//        }
            if (isset($input['offset'])) {
                $entity = $entity->offset($input['offset']);
            }
            if (isset($input['limit'])) {
                $entity = $entity->limit($input['limit']);
            }
            if (isset($input['where'])) {
                $entity = $entity->whereRaw($input['where']);
            }
            if (isset($input['order'])) {
                $entity = $entity->orderByRaw($input['order']);
            }
            return $entity->get();
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function update(RankingRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Ranking::find($id);
        if (is_null($entity)) {
            return $this->sendError('Ranking not found.');
        }
        $entity->update($input);
        $this->changeRankingTotal($entity);
        return $this->sendResponse($entity->toArray(), 'Ranking updated successfully.');
    }

    public function updateUser(RankingRequest $request, $id)
    {
        $input = $request->all();
        $user = auth('api')->user();
        $entity  = Ranking::where('user_id', $user->id)->where('id', $id)->first();
        if (is_null($entity)) {
            return $this->sendError('Ranking not found.');
        }
        $entity->update($input);
        $this->changeRankingTotal($entity);
        return $this->sendResponse($entity->toArray(), 'Ranking updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Ranking::find($id);
        if (is_null($entity)) {
            return $this->sendError('Ranking not found.');
        }
        $entity->delete();
        $this->changeRankingTotal($entity);
        return $this->sendResponse($entity->toArray(), 'Ranking deleted successfully.');
    }

    public function destroyUser($id)
    {
        $user = auth('api')->user();
        $entity  = Ranking::where('user_id', $user->id)->where('id', $id)->first();
        if (is_null($entity)) {
            return $this->sendError('Ranking not found.');
        }
        $entity->delete();
        $this->changeRankingTotal($entity);
        return $this->sendResponse($entity->toArray(), 'Ranking deleted successfully.');
    }

    private function changeRankingTotal($rankingFirst) {
        $product = $rankingFirst->product;
        $quantity = 0; $sum = 0;
        foreach ($product->rankings as $ranking) {
            $quantity++;
            $sum += $ranking->point;
        }
        $product->ranking = ((int)(((double)$sum) / $quantity * 100)) / 100;
        $product->save();
    }
}
