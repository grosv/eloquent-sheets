<?php

namespace Grosv\EloquentSheets;

use Google_Client;
use Google_Service_Sheets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Revolution\Google\Sheets\Sheets;
use Sushi\Sushi;

class SheetModel extends Model
{
    use Sushi;

    protected $rows = [];
    protected $sheetId;
    protected $spreadsheetId;
    protected $headerRow;
    public $primaryKey = 'id';
    public $cacheName;
    private $cacheDirectory;
    protected $headers;

    public function __construct()
    {
        parent::__construct();
        $this->cacheDirectory = File::isDirectory(config('sushi.cache-path', storage_path('framework/cache'))) ?
            config('sushi.cache-path', storage_path('framework/cache')) :
            File::makeDirectory(config('sushi.cache-path', storage_path('framework/cache')));

        $this->cacheName = $this->getCacheName();
    }

    public function getRows()
    {
        return ! empty($this->rows) ? $this->rows : $this->loadFromSheet();
    }

    public function invalidateCache()
    {
        if (! file_exists($this->cacheDirectory.'/'.config('sushi.cache-prefix', 'sushi').'-'.Str::kebab(str_replace('\\', '', static::class)).'.sqlite')) {
            return;
        }
        unlink($this->cacheDirectory.'/'.config('sushi.cache-prefix', 'sushi').'-'.Str::kebab(str_replace('\\', '', static::class)).'.sqlite');
    }

    public function loadFromSheet(): ?array
    {
        $sheets = new Sheets();
        $client = new Google_Client(config('google'));
        $client->setScopes([Google_Service_Sheets::DRIVE, Google_Service_Sheets::SPREADSHEETS]);
        $service = new \Google_Service_Sheets($client);
        $sheets->setService($service);
        $inferId = 0;

        try {
            $sheet = $sheets->spreadsheet($this->spreadsheetId)->sheetById($this->sheetId)->get();
        } catch (\Exception $e) {
            $this->invalidateCache();
            throw $e;
        }

        $headers = is_array($this->headers) ? collect($this->headers) : collect($sheet->pull($this->headerRow - 1));

        if (! $headers->contains($this->primaryKey)) {
            $headers->push($this->primaryKey);
            $inferId = 1;
        }

        $rows = collect([]);

        $sheet->each(function ($row) use ($headers, $rows, &$inferId) {
            $record = [];

            // append empty cols inside the row to match the number of cols in header
            foreach ($headers as $index => $header) {
                $record[$header] = $row[$index] ?? '';
            }

            if ($inferId) {
                $record[$this->primaryKey] = $inferId++;
            }

            $rows->push($headers->combine($record));
        });

        return $rows->toArray();
    }

    public function getSheetId()
    {
        return $this->sheetId;
    }

    public function getSpreadsheetId()
    {
        return $this->spreadsheetId;
    }

    public function getCacheName()
    {
        return ! is_null($this->getConnection()) ? explode('.', basename($this->getConnection()->getDatabaseName()))[0] : null;
    }
}
