<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Vite;

uses(RefreshDatabase::class);

test('the layout loads the css and javascript entry points through vite', function () {
    $response = $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('<script type="module"', false);

    if (is_file(public_path('hot'))) {
        $response
            ->assertSee('@vite/client', false)
            ->assertSee('resources/css/app.css', false)
            ->assertSee('resources/js/app.js', false);

        return;
    }

    $response->assertSee(builtAsset('resources/css/app.css'), false)
        ->assertSee(builtAsset('resources/js/app.js'), false);
});

test('the layout loads the built css and javascript assets from the vite manifest', function () {
    if (! is_file(public_path('build/manifest.json'))) {
        $this->markTestSkipped('Vite build manifest is missing. Run npm run build first.');
    }

    Vite::useHotFile(public_path('build/hot-file-does-not-exist'));

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('<link rel="stylesheet"', false)
        ->assertSee('<script type="module"', false)
        ->assertSee(builtAsset('resources/css/app.css'), false)
        ->assertSee(builtAsset('resources/js/app.js'), false);
});

function builtAsset(string $entry): string
{
    $manifest = json_decode(
        file_get_contents(public_path('build/manifest.json')),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    return $manifest[$entry]['file'];
}
