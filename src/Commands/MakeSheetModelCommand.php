<?php


namespace Grosv\EloquentSheets\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeSheetModelCommand extends Command
{
    /** @var string  */
    protected $signature = "make:sheet-model";
    /** @var string */
    private $modelPath;
    /** @var string */
    private $sheetUrl;
    /** @var string */
    private $stub;
    /** @var string */
    private $modelNamespace = null;
    /** @var string */
    private $modelName;
    /** @var string */
    private $spreadsheetId;
    /** @var string */
    private $sheetId;
    /** @var string */
    private $fullyQualfiedModelName;
    /** @var string */
    private $forgetUri;


    public function handle()
    {
        $this->modelPath = $this->ask('Where would you like to create your sheet model?', config('eloquent-sheets.app_path'));
        $this->modelName = $this->ask('What do you want the class name of your new model to be?');
        $this->sheetUrl = $this->ask('Copy and paste the full URL of your Google Sheet from your browser address bar:');
        $this->parseSheetUrl();
        $this->stub = $this->getStub();
        $this->modelPathToNamespace();
        if (is_null($this->modelNamespace)) {
            $this->modelNamespace = $this->ask('We were unable to determine the namespace you want to use for your model. Please provide it:');
        }
        $this->fullyQualfiedModelName = $this->modelNamespace . '\\' . $this->modelName;
        $this->modelPath .= '/' . $this->modelName . '.php';
        $this->calculateForgetUri();
        if (!$this->confirm('Ready to write model '. $this->fullyQualfiedModelName . ' at ' . $this->modelPath . '?')) {
            return;
        }
        File::put($this->modelPath, $this->makeSubstitutions());
        $this->line('Model created! Here is the URI to trigger the clearing of the cache for this model: '. $this->forgetUri);

    }

    protected function getStub()
    {
        return File::get(__DIR__.'/SheetModel.stub');
    }

    protected function makeSubstitutions()
    {
        return str_replace(
            ['SHEET_MODEL_NAMESPACE', 'SHEET_MODEL_NAME', 'SPREADSHEET_ID', 'SHEET_ID'],
            [$this->modelNamespace, $this->modelName, $this->spreadsheetId, $this->sheetId],
            $this->stub);
    }

    protected function modelPathToNamespace(): void
    {
        if ($this->modelPath === app_path()) {
            $this->modelNamespace = 'App';
            return;
        }
        $diff = str_replace(app_path(), '', $this->modelPath);
        if ($diff != $this->modelPath) {
            $studlyArray = [];
            $diffArray = explode('\\', $diff);
            foreach ($diffArray as $part) {
                array_push($studlyArray, Str::studly($part));
            }
            $this->modelNamespace = join('\\', $studlyArray);
            return;
        }
    }

    protected function parseSheetUrl()
    {
        $url = str_replace('https://docs.google.com/spreadsheets/d/', '', $this->sheetUrl);
        $this->sheetId = explode('gid=', $url)[1];
        $this->spreadsheetId = explode('/', $url)[0];
    }

    protected function calculateForgetUri()
    {
        $this->forgetUri = '/eloquent_sheets_forget/sushi-'.Str::kebab(stripslashes($this->fullyQualfiedModelName));
    }

}