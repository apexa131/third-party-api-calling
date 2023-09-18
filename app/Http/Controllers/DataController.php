<?php

namespace App\Http\Controllers;

use App\Services\RandomUser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DataController extends Controller
{
    public function __invoke()
    {
        $limit = request()->limit ?? 10;

        try {
            \request()->validate([
                'limit' => ['nullable', 'numeric', 'min:1', 'max:20']
            ]);
        } catch (ValidationException $exception) {
            return response()->xml([
                'data' => $exception->errors()
            ], 422);
        }

        return response()->xml([
            'data' => (new RandomUser($limit))->get()
        ]);
    }
}
