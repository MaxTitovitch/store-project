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
        return $this->sendResponse($entity->toArray(), 'Ranking updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Ranking::find($id);
        if (is_null($entity)) {
            return $this->sendError('Ranking not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Ranking deleted successfully.');
    }
}
