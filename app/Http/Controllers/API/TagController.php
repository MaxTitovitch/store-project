<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use Illuminate\Http\Request;
use App\Tag;

class TagController extends ApiController
{

    public function index()
    {
        $products = Tag::all();
        return $this->sendResponse($products->toArray(), 'Tags retrieved successfully.');
    }


    public function create()
    {
        //
    }


    public function store(TagRequest $request)
    {
        $input = $request->all();
        $tag = Tag::create($input);
        return $this->sendResponse($tag->toArray(), 'Tag created successfully.');
    }


    public function show($id)
    {
        $tag = Tag::find($id);
        if (is_null($tag)) {
            return $this->sendError('Tag not found.');
        }
        return $this->sendResponse($tag->toArray(), 'Tag retrieved successfully.');
    }


    public function edit($id)
    {
    }


    public function update(TagRequest $request, $id)
    {
        $input = $request->all();
        $tag  = Tag::find($id);
        $tag->name = $input['name'];
        $tag->save();
        return $this->sendResponse($tag->toArray(), 'Tag updated successfully.');
    }

    public function destroy($id)
    {
        $tag  = Tag::find($id);
        $tag->delete();
        return $this->sendResponse($tag->toArray(), 'Tag deleted successfully.');
    }
}
