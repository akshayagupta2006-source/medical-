<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that root URL redirects appropriately.
     */
    public function test_the_application_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }
}
