<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CategoryRequest;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{

    public function index(Request $request)
    {
        $entities = $this->filtrateQuery($request->all());
        return $this->sendResponse($entities->toArray(), 'Categories retrieved successfully.');
    }

    public function store(CategoryRequest $request)
    {
        $input = $request->all();
        $entity = Category::create($input);
        return $this->sendResponse($entity->toArray(), 'Category created successfully.');
    }

    public function show(Request $request, $id)
    {
        $entity  = Category::find($id);
        if (is_null($entity)) {
            return $this->sendError('Category not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Category retrieved successfully.');
    }

    private function filtrateQuery($input){
        try {
            $entity = Category::select();
            if(isset($input['trash'])) {
                $entity = $entity->onlyTrashed();
            }
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

    public function update(CategoryRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Category::find($id);
        if (is_null($entity)) {
            return $this->sendError('Category not found.');
        }
        $entity->update($input);
        return $this->sendResponse($entity->toArray(), 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Category::find($id);
        if (is_null($entity)) {
            return $this->sendError('Category not found.');
        }
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Category deleted successfully.');
    }
}
