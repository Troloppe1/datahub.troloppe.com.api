<?php

namespace Tests\Feature;

use Tests\TestCase;

class FilamentAssetUrlTest extends TestCase
{
    public function test_filament_assets_use_the_forwarded_public_host_when_behind_a_proxy(): void
    {
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '10.0.0.10',
            'HTTP_HOST' => '127.0.0.1:8000',
            'HTTP_X_FORWARDED_HOST' => 'api-datahub.troloppe.com',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
            'SERVER_PORT' => 8000,
        ])->get('/console/login');

        $response->assertOk();
        $response->assertSee('https://api-datahub.troloppe.com/css/filament/forms/forms.css', false);
        $response->assertSee('https://api-datahub.troloppe.com/js/filament/filament/app.js', false);
        $response->assertDontSee('http://127.0.0.1:8000/js/filament/filament/app.js', false);
    }
}
