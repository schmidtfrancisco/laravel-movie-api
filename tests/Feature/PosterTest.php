<?php

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function()
{
    Storage::fake('public');
});

it('allows an editor to upload a poster', function () {
    $movie = Movie::factory()->create();
    $file = UploadedFile::fake()->image('poster.jpg', 800, 600);
    /** @var TestCase $this */
    $this->actingAs(editor(), 'api')
        ->postJson("/api/movies/{$movie->id}/poster", ['poster' => $file])
        ->assertOk()
        ->assertJsonPath('message', 'Poster actualizado exitosamente.')
        ->assertJsonStructure(['data' => ['poster_url']]);

    $path = $movie->refresh()->poster;
    expect($path)->not->toBeNull();
    expect(Storage::disk('public')->exists($path))->toBeTrue();
});
