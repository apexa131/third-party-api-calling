<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DataApiTest extends TestCase
{
    public function test_data_api_success(): void
    {
        $response = $this->get('/api/data');

        $response->assertStatus(200);
    }

    public function test_data_api_min_limit_parameter_error(): void
    {
        $response = $this->get('/api/data?limit=0');

        $response->assertStatus(422);
    }

    public function test_data_api_max_limit_parameter_error(): void
    {
        $response = $this->withHeaders(['Content-Type', 'application/xml'])->get('/api/data?limit=100');

        $response->assertStatus(422)
            ->assertHeader('Content-Type', 'application/xml');
    }

    public function test_data_api_response_count(): void
    {
        $response = $this->get('/api/data?limit=2');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_data_api_response_is_in_xml_format(): void
    {
        $response = $this->withHeaders(['Content-Type', 'application/xml'])->get('/api/data?limit=2');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml');

    }

    public function test_data_api_response_includes_fields()
    {
        // Mock the API response data with a sample XML
        $sampleXml = '<?xml version="1.0" encoding="UTF-8"?>
            <users>
                <user>
                    <full_name>John Doe</full_name>
                    <phone>123-456-7890</phone>
                    <email>john.doe@example.com</email>
                    <country>USA</country>
                </user>
                <user>
                    <full_name>Jane Smith</full_name>
                    <phone>987-654-3210</phone>
                    <email>jane.smith@example.com</email>
                    <country>Canada</country>
                </user>
            </users>';

        Http::fake([
            '/api/data?limit=2' => Http::response($sampleXml, 200),
        ]);

        $response = $this->get('/api/data?limit=2');

        // Assert that the response was successful (status code 200)
        $response->assertStatus(200);

        // Extract and parse the XML content
        $xml = simplexml_load_string($response->getContent());

        // Loop through each user in the XML and assert the presence of required fields
        foreach ($xml->user as $user) {
            $this->assertNotNull($user->full_name);
            $this->assertNotNull($user->phone);
            $this->assertNotNull($user->email);
            $this->assertNotNull($user->country);
        }
    }

    public function test_data_api_concurrent_requests()
    {
        // Mock the API response data with a sample XML
        $sampleXml = '<?xml version="1.0" encoding="UTF-8"?>
            <users>
                <user>
                    <full_name>John Doe</full_name>
                    <phone>123-456-7890</phone>
                    <email>john.doe@example.com</email>
                    <country>USA</country>
                </user>
            </users>';

        Http::fake([
            '/api/data?limit=1' => Http::response($sampleXml, 200),
        ]);

        $concurrentRequests = 10;
        $responses = [];

        // Use a loop to make concurrent requests
        for ($i = 0; $i < $concurrentRequests; $i++) {
            $responses[] = $this->get('/api/data?limit=1');
        }

        // Assert that each response was successful (status code 200)
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        foreach ($responses as $response) {
            $xml = simplexml_load_string($response->getContent());

            // Loop through each user in the XML and assert the presence of required fields
            foreach ($xml->user as $user) {
                $this->assertNotNull($user->full_name);
                $this->assertNotNull($user->phone);
                $this->assertNotNull($user->email);
                $this->assertNotNull($user->country);
            }
        }
    }

}
