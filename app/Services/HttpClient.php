<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;

class HttpClient
{
    protected $apiEndPoint;

    public function __construct($apiEndPoint)
    {
        $this->apiEndPoint = $apiEndPoint;
    }

    public function get()
    {
        try {
            $response = Http::get($this->apiEndPoint);

            if ($response->ok()) {
                return [
                    'data' => $response->json($key = null, $default = []),
                    'status' => 200
                ];
            }
        } catch (\Exception $exception) {
            error_log($exception->getMessage());
            return ['data' => [], 'status' => $exception->getCode()];
        }
    }
}
