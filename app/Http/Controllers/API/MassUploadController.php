<?php

namespace App\Http\Controllers\API;

use App\Facades\MassUpload;
use App\Http\Requests\MassUploadRequest;
use Illuminate\Support\Facades\Storage;

class MassUploadController extends ApiController
{
    public function store(MassUploadRequest $request) {
        $file = $request->file('file');
        $path = Storage::put('data-file', $file);

        $typeParsing  = $request->get('type');
        $nameParts = explode('.', $file->getClientOriginalName());
        $typeFile = array_pop($nameParts);

        $isParsingSuccess = MassUpload::parse($path, $typeFile, $typeParsing);

        Storage::delete($path);

        if($isParsingSuccess == "Success") {
            return $this->sendResponse('Data parsed', 'File is parsed.');
        } else {
            return $this->sendError('Parse error', $isParsingSuccess);
        }
    }



}
