<?php

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

it('includes meta and links on movies index', function () {
    Movie::factory(5)->create();
    /** @var TestCase $this */
    $this->getJson('/api/movies')
        ->assertOk()
        ->assertJsonStructure([
            'data', 
            'links' => ['first', 'last', 'prev', 'next' ], 
            'meta' => ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total'],
        ]);
  
});

it('allows configurable per_page on movie index', function () {
    Movie::factory(10)->create();
    /** @var TestCase $this */
    $this->getJson('/api/movies?per_page=4')
        ->assertOk()
        ->assertJsonPath('meta.per_page', 4)
        ->assertJsonCount(4, 'data');
  
});