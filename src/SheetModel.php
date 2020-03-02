<?php


namespace Grosv\EloquentSheets;

use Google_Client;
use Google_Service_Sheets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Revolution\Google\Sheets\Sheets;
use Sushi\Sushi;

class SheetModel extends Model
{

    use Sushi;

    protected $rows = [];
    protected $sheetId;
    protected $spreadsheetId;
    protected $headerRow;
    private $cacheDirectory;
    public $cacheId;
    public $cacheFile;
    public $cacheCreated = false;
    public $primaryKey = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->cacheDirectory = realpath(config('sushi.cache-path', storage_path('framework/cache')));
        $this->cacheFile = $this->cacheDirectory . '/' . $this->cacheId . '.json';
    }

    public function getRows()
    {
        return File::exists($this->cacheFile) ? json_decode(File::get($this->cacheFile), true) : $this->loadFromSheet();
    }


    public function loadFromSheet(): array
    {
        $sheets = new Sheets();
        $client = new Google_Client(config('google'));
        $client->setScopes([Google_Service_Sheets::DRIVE, Google_Service_Sheets::SPREADSHEETS]);
        $service = new \Google_Service_Sheets($client);
        $sheets->setService($service);

        $sheet = $sheets->spreadsheet($this->spreadsheetId)->sheetById($this->sheetId)->get();

        $headers = collect($sheet->pull($this->headerRow - 1));

        $rows = collect([]);

        $sheet->each(function($row) use ($headers, $rows) {
            $rows->push($headers->combine($row));
        });

        File::put($this->cacheFile, json_encode($rows->toArray()));

        $this->cacheCreated = true;

        return $rows->toArray();

    }
}