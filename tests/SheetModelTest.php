<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Tests\Models\TestModel;

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
        File::cleanDirectory(config('sushi.cache-path'));
        $this->assertFileNotExists('tests/cache/sushi-tests-models-test-model.sqlite');
        $sheet = new TestModel();
        $this->assertIsArray($sheet->getRows());
    }

    /** @test */
    public function does_not_hit_google_sheets_if_cache_exists()
    {
        $sheet = new TestModel();
        $this->assertFileExists('tests/cache/sushi-tests-models-test-model.sqlite');
        $this->assertStringContainsString(
            'tests/cache/sushi-tests-models-test-model.sqlite',
            $sheet->getConnection()->getDatabaseName()
        );
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
    public function can_invalidate_cache()
    {
        $sheet = TestModel::find(1);
        $this->assertFileExists('tests/cache/sushi-tests-models-test-model.sqlite');
        $sheet->invalidateCache();
        $this->assertFileNotExists('tests/cache/sushi-tests-models-test-model.sqlite');
        $sheet = TestModel::find(2);
        $this->assertEquals('Justine', $sheet->name);
    }

    /** @test */
    public function can_invalidate_cache_by_request()
    {
        $sheet = TestModel::find(1);
        $this->assertFileExists('tests/cache/sushi-tests-models-test-model.sqlite');
        $response = $this->get('/eloquent_sheets_forget/'.$sheet->cacheName);
        $response->assertSuccessful();
        $this->assertFileNotExists('tests/cache/sushi-tests-test-model.sqlite');
    }
}
