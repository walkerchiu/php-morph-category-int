<?php

namespace WalkerChiu\MorphCategory;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\MorphCategory\Models\Entities\Category;
use WalkerChiu\MorphCategory\Models\Entities\CategoryLang;

class CategoryLangTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\MorphCategory\MorphCategoryServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on CategoryLang.
     *
     * For WalkerChiu\Core\Models\Entities\Lang
     *     WalkerChiu\MorphCategory\Models\Entities\CategoryLang
     *
     * @return void
     */
    public function testMorphCategoryLang()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-category.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-category.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-category.soft_delete', 1);

        // Give
        factory(Category::class, 2)->create();
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'description']);
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CategoryLang::class)->create(['morph_id' => 2, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CategoryLang::class)->create(['morph_id' => 2, 'morph_type' => Category::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get records after creation
            // When
            $records = CategoryLang::all();
            // Then
            $this->assertCount(6, $records);

        // Get record's morph
            // When
            $record = CategoryLang::find(1);
            // Then
            $this->assertNotNull($record);
            $this->assertInstanceOf(Category::class, $record->morph);

        // Scope query on whereCode
            // When
            $records = CategoryLang::ofCode('en_us')
                                   ->get();
            // Then
            $this->assertCount(4, $records);

        // Scope query on whereKey
            // When
            $records = CategoryLang::ofKey('name')
                                   ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereCodeAndKey
            // When
            $records = CategoryLang::ofCodeAndKey('en_us', 'name')
                                   ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereMatch
            // When
            $records = CategoryLang::ofMatch('en_us', 'name', 'Hello')
                                   ->get();
            // Then
            $this->assertCount(1, $records);
            $this->assertTrue($records->contains('id', 1));
    }

    /**
     * A basic functional test on CategoryLang.
     *
     * For WalkerChiu\Core\Models\Entities\LangTrait
     *     WalkerChiu\MorphCategory\Models\Entities\CategoryLang
     *
     * @return void
     */
    public function testMorphCategory()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-category.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-category.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-category.soft_delete', 1);

        // Give
        factory(Category::class, 2)->create();
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'description']);
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(CategoryLang::class)->create(['morph_id' => 1, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CategoryLang::class)->create(['morph_id' => 2, 'morph_type' => Category::class, 'code' => 'en_us', 'key' => 'name']);
        factory(CategoryLang::class)->create(['morph_id' => 2, 'morph_type' => Category::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get lang of record
            // When
            $record_1 = Category::find(1);
            $lang_1   = CategoryLang::find(1);
            $lang_4   = CategoryLang::find(4);
            // Then
            $this->assertNotNull($record_1);
            $this->assertTrue(!$lang_1->is_current);
            $this->assertTrue($lang_4->is_current);
            $this->assertCount(4, $record_1->langs);
            $this->assertInstanceOf(CategoryLang::class, $record_1->findLang('en_us', 'name', 'entire'));
            $this->assertEquals(4, $record_1->findLang('en_us', 'name', 'entire')->id);
            $this->assertEquals(4, $record_1->findLangByKey('name', 'entire')->id);
            $this->assertEquals(2, $record_1->findLangByKey('description', 'entire')->id);

        // Get lang's histories of record
            // When
            $histories_1 = $record_1->getHistories('en_us', 'name');
            $record_2 = Category::find(2);
            $histories_2 = $record_2->getHistories('en_us', 'name');
            // Then
            $this->assertCount(1, $histories_1);
            $this->assertCount(0, $histories_2);
    }
}
