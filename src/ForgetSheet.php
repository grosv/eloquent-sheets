<?php

namespace Grosv\EloquentSheets;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ForgetSheet
{
    public function execute($id)
    {
        if (File::isFile(config('sushi.cache-path').'/'.$id.'.sqlite')) {
            File::delete(config('sushi.cache-path').'/'.$id.'.sqlite');
        }
        if (File::isFile(storage_path('framework/cache/'.$id.'.sqlite'))) {
            File::delete(storage_path('framework/cache/'.$id.'.sqlite'));
        }

        $this->triggerCallback($id);

        return response()->noContent();
    }

    protected function triggerCallback($id)
    {
        $class = 'App\\'.Str::studly(str_replace('sushi-', '', $id));
        if (! class_exists($class)) {
            return;
        }
        $model = new $class();

        if (method_exists($model, 'onForget')) {
            $model->onForget();
        }
    }
}
