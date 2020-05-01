<?php

namespace Grosv\EloquentSheets;

use Illuminate\Support\Facades\File;

class ForgetSheet
{
    public function execute($id)
    {
        File::delete(config('sushi.cache-path').'/'.$id.'.sqlite');

        return response()->noContent();
    }
}
