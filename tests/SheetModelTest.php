<?php

namespace Tests;

use Grosv\EloquentSheets\Models\TestModel;
use Illuminate\Support\Facades\File;

class SheetModelTest extends TestCase
{
    /**
     * @var TestModel
     */
    private $sheet;
    /**
     * @var string
     */
    private $cachePath;

    public function setUp(): void
    {
        parent::setUp();
        config(['sushi.cache-path' => $this->cachePath = __DIR__.'/cache']);
    }

    /** @test */
    public function can_read_from_google_sheets()
    {
        $sheet = new TestModel();
        File::cleanDirectory($this->cachePath);
        $this->assertFileNotExists($sheet->cacheFile);
        $this->assertIsArray($sheet->getRows());
        $this->assertTrue($sheet->cacheCreated);
        $this->assertFileExists($sheet->cacheFile);
    }

    /** @test */
    public function does_not_hit_google_sheets_if_json_exists()
    {
        $sheet = new TestModel();
        $this->assertFileExists($sheet->cacheFile);
        $this->assertFalse($sheet->cacheCreated);
        $this->assertIsArray($sheet->getRows());
    }

    /** @test */
    public function can_do_basic_eloquent_stuff()
    {
        $sheet = TestModel::find(1);
        $this->assertEquals('Ed', $sheet->name);

        $sheet = TestModel::where('email', 'ed@gros.co')->first();
        $this->assertEquals(1, $sheet->id);

        $sheet = TestModel::where('name', 'Milo')->first();
        $this->assertEquals('Kid', $sheet->title);
    }

    /** @test */
    public function can_forget_sheet_when_update_notification_comes_in()
    {
        $sheet = TestModel::find(1);
        $this->assertFileExists($sheet->cacheFile);
        $response = $this->get('/eloquent_sheets_forget/'.$sheet->cacheId);
        $response->assertSuccessful();
        $this->assertFileNotExists($sheet->cacheFile);
    }
}