<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $entities = Category::all();
        return $this->sendResponse($entities->toArray(), 'Categories retrieved successfully.');
    }

    public function store(CategoryRequest $request)
    {
        $input = $request->all();
        $entity = Category::create($input);
        return $this->sendResponse($entity->toArray(), 'Category created successfully.');
    }

    public function show($id)
    {
        $entity = Category::find($id);
        if (is_null($entity)) {
            return $this->sendError('Category not found.');
        }
        return $this->sendResponse($entity->toArray(), 'Category retrieved successfully.');
    }

    public function update(CategoryRequest $request, $id)
    {
        $input = $request->all();
        $entity  = Category::find($id);
        $entity->name = $input['name'];
        $entity->save();
        return $this->sendResponse($entity->toArray(), 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $entity  = Category::find($id);
        $entity->delete();
        return $this->sendResponse($entity->toArray(), 'Category deleted successfully.');
    }
}
