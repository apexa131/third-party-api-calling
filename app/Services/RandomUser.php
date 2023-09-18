<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;

class RandomUser
{
    protected $limit;

    public function __construct($limit = 10)
    {
        $this->limit = $limit;
    }

    public function get()
    {
        $apiUrl = config('services.random_user.api_url');
        $users = [];

        for ($i = 0; $i < $this->limit; $i++) {
            $userResponse = (new HttpClient($apiUrl))->get();
            $user = data_get($userResponse['data'], 'results.0');

            if ($userResponse['status'] !== 200) {
                return (new BoredActivities($this->limit))->get();
            }

            $users[] = [
                'full_name' => data_get($user, 'name.first') . data_get($user, 'name.last'),
                'phone' => data_get($user, 'phone'),
                'email' => data_get($user, 'email'),
                'country' => data_get($user, 'location.country'),
            ];
        }

        return collect($users)->sortByDesc('full_name')->toArray();

    }
}
