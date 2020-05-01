<?php

use Grosv\EloquentSheets\ForgetSheet;
use Illuminate\Support\Facades\Route;

Route::get('/eloquent_sheets_forget/{id?}', [ForgetSheet::class, 'execute']);
