# Eloquent Models for your Google Sheets
### A package that lets you lay Eloquent on top of a Google Sheet.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/grosv/laravel-passwordless-login.svg?style=flat-square)](https://packagist.org/packages/grosv/laravel-passwordless-login)
[![StyleCI](https://github.styleci.io/repos/243858945/shield?branch=master)](https://github.styleci.io/repos/243858945)
![Build Status](https://app.chipperci.com/projects/8c76f67e-e513-46a3-ad7a-aecb136dfa05/status/master)

### Installation
```shell script
composer require grosv/eloquent-sheets
```

### Configuration
This package relies on [revolution/laravel-google-sheets](https://github.com/kawax/laravel-google-sheets). You must handle the configuration for that package and its dependencies for this package to work. Follow the instructions in their readme (though you can skip the compoer require bit because I do that already in here).

### Usage
Create a model for your Google sheet that extends Grosv\EloquentSheets\SheetModel. Here is the bare bones requirement to make it work:
```php
use Grosv\EloquentSheets\SheetModel;

class YourGoogleSheetsModel extends SheetModel
{
    protected $spreadsheetId = '1HxNqqLtc614UVLoTLEItfvcdcOm3URBEM2Zkr36Z1rE'; // The id of the spreadsheet
    protected $sheetId = '0'; // The id of the sheet within the spreadsheet (gid=xxxxx on the URL)
    protected $headerRow = '1'; // The row containing the names of your columns (eg. id, name, email, phone)
    public $cacheId = '39e93f4d-c20e-4874-88ee-2ddc403bd3de'; // A uuid that is used as a cache key
}
```

### What's Missing
This works as it is but you have to do a great deal of work to set it up. Here are the missing pieces that I'll be adding over the next few days:

1. An artisan command to create your model and interactively populate the required data.

2. A Google Apps Script that you'll be able to use to have your sheet tell your site when its data has changed so that the cache can be forgotten and fresh data pulled from the sheet.

3. Eventually I'd like to add insert and update methods that will let you append rows to your spreadsheet and edit existing rows.

### Acknowledgements
This package wouldn't be possible without [Sushi](https://calebporzio/sushi) by Caleb Porzio. If you're not [sponsoring him on GitHub](https://github.com/sponsors/calebporzio) you should.