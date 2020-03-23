# Eloquent Models for your Google Sheets
### A package that lets you lay Eloquent on top of a Google Sheet.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/grosv/eloquent-sheets.svg?style=flat-square)](https://packagist.org/packages/grosv/eloquent-sheets)
[![StyleCI](https://github.styleci.io/repos/244386505/shield?branch=master)](https://github.styleci.io/repos/244386505)
![Build Status](https://app.chipperci.com/projects/65d88e3d-ecf4-4ced-afd3-e5ba3593ce21/status/master)

This package provides an Eloquent model that sits on top of a Google Sheet. In order for it to work, there are two things your sheet needs to have. One is a heading row that holds the name of your columns. This defaults to row 1 (the top row) but it can be any row in the sheet. The other is a primary key column. Eloquent assumes that your primary key column is named id. If it's not, set it in your model like you would normally.

When you use this package, an initial invocation of the model will read the sheet and store each row as a record in a table inside a file-based sqlite database. Subsequent invocations of the model use that sqlite database so changes to the spreadsheet won't be reflected in the database. However, there are two ways that you can invalidate the sqlite cache and cause it to be recreated:

1. You can call the invalidateCache() method on the model with something like like `YourGoogleSheetModel::first()->invalidateCache()` 

2. A macro that you can attach to your Google sheet. The macro listens for edits within the sheet and when one happens, it sends a request to a route provided by this package that deletes the sqlite database forcing a fresh load the next time the model is used. 

### Installation
```shell script
composer require grosv/eloquent-sheets
```

### Configuration
This package relies on [revolution/laravel-google-sheets](https://github.com/kawax/laravel-google-sheets). You must handle the configuration for that package and its dependencies for this package to work. Follow the instructions in their readme (though you can skip the composer require bit because I do that already in here).

### Usage
Consider the following Google Sheet. We want to lay an Eloquent model on top of it. 

<img width="691" alt="Screen Shot 2020-03-03 at 3 02 15 PM" src="https://user-images.githubusercontent.com/1053395/75819454-5440d500-5d60-11ea-8f5b-0e4e04a39326.png">

```shell script
php artisan make:sheet-model
```
**Step 1 - Enter the full path to the directory where you want to create the model file (defaults to app_path()):** 

<img width="824" alt="Screen Shot 2020-03-11 at 4 48 48 PM" src="https://user-images.githubusercontent.com/1053395/76467708-373b8000-63b8-11ea-8cfc-e8f7ee084559.png">

---

**Step 2 - Enter the name you want to use for your model class:**

<img width="536" alt="Screen Shot 2020-03-03 at 3 11 10 PM" src="https://user-images.githubusercontent.com/1053395/75820030-3de74900-5d61-11ea-9cf2-84d2c6977cbb.png">

---

**Step 3 - Paste the edit url of your Google Sheet from the browser address bar:**

<img width="816" alt="Screen Shot 2020-03-03 at 3 13 33 PM" src="https://user-images.githubusercontent.com/1053395/75820229-99b1d200-5d61-11ea-9d18-37372b108976.png">

---

**Step 4 - Confirm that the path and full classname look right:**

<img width="1120" alt="Screen Shot 2020-03-03 at 3 14 47 PM" src="https://user-images.githubusercontent.com/1053395/75820325-c4038f80-5d61-11ea-987d-edac8e820caf.png">

---

**Step 5 - And you will receive the template of a macro that you can attach to your sheet that will tell your site that the sheet has changed so a new cache has to be built.**

<img width="972" alt="Screen Shot 2020-03-03 at 3 20 58 PM" src="https://user-images.githubusercontent.com/1053395/75820796-a256d800-5d62-11ea-8cff-2b5c0d42efb3.png">

---

You can use something like this to enable the macro generated on your sheet as an [installable trigger](https://developers.google.com/apps-script/guides/triggers/installable):

```javascript
function createSpreadsheetOpenTrigger() {
  var ss = SpreadsheetApp.getActive();
  ScriptApp.newTrigger('onEdit')
      .forSpreadsheet(ss)
      .onEdit()
      .create();
}
```

### The Resulting Model Class

```php
use Grosv\EloquentSheets\SheetModel;

class YourGoogleSheetsModel extends SheetModel
{
    protected $spreadsheetId = '1HxNqqLtc614UVLoTLEItfvcdcOm3URBEM2Zkr36Z1rE'; // The id of the spreadsheet
    protected $sheetId = '0'; // The id of the sheet within the spreadsheet (gid=xxxxx on the URL)
    protected $headerRow = '1'; // The row containing the names of your columns (eg. id, name, email, phone)
}
```

This model can do your basic Eloquent model stuff because it really is an Eloquent model. Though it's currently limited to read / list methods. Update and insert don't currently work because you do those things by editing your spreadsheet.

### What's Missing

Eventually I'd like to add insert and update methods that will let you append rows to your spreadsheet and edit existing rows. I already have the most important part done... a method to invalidate the cache when we update or insert. Now I just need the update and insert methods.

### Acknowledgements
This package wouldn't be possible without [Sushi](https://github.com/calebporzio/sushi) by Caleb Porzio. If you're not [sponsoring him on GitHub](https://github.com/sponsors/calebporzio) you should.
