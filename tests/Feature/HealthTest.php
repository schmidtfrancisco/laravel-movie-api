<?php

use Tests\TestCase;

test('return ok on health endpoint', function () {
    /** @var TestCase $this */
    $response = $this->get('/api/health');

    $response->assertStatus(200);
    $response->assertJson(['data' => ['status' => 'OK', 'app' => 'MovieAPI'], 'message' => 'Health check']);
});
