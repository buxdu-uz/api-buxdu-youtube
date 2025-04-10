<?php

namespace App\Http\Controllers\Hemis;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacultyResource;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HemisController extends Controller
{
    /**
     * @var mixed|Client
     */
    public mixed $clients;

    /**
     * @var mixed|string[]
     */
    public mixed $headers;

    public function __construct()
    {
        $this->clients = new Client();
        $this->headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
    }

    public function getAllFaculties()
    {
        $request = new \GuzzleHttp\Psr7\Request(
            'GET',
            config('hemis.host') . 'data/department-list?_structure_type=11&limit=' . config('hemis.limit'),
            $this->headers
        );

        $response = $this->clients->sendAsync($request)->wait()->getBody();
        $result = json_decode($response)->data->items;

        return FacultyResource::collection($result);
    }
}
