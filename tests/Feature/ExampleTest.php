<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Prueba basica de ejemplo.
     */
    public function test_la_aplicacion_devuelve_una_respuesta_exitosa(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
