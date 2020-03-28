<?php

namespace Tests\Models;

use Grosv\EloquentSheets\SheetModel;

class DefineHeadersModel extends SheetModel
{
    protected $spreadsheetId = '1HxNqqLtc614UVLoTLEItfvcdcOm3URBEM2Zkr36Z1rE';
    protected $sheetId = '282825363';
    protected $headerRow = '1';
    protected $headers = ['id', 'name', 'email'];
}
