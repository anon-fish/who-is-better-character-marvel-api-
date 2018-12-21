<?php
require 'vendor/autoload.php';
include 'data/id_numbers.php';
require_once __DIR__.'/bootstrap.php';

use GuzzleHttp\Client;


//marvel api authentication
$ts = time();
$public_key = '017b3e045b0cf05932bb09c10114297a';
$private_key = '28dc3cab9e440d3bb25c5f561b17780deef91a2a';
$hash = md5($ts . $private_key . $public_key);
//query options
$query_params = [
    'apikey' => $public_key,
    'ts' => $ts,
    'hash' => $hash
];

//instantiate guzzle client class with configuration
$client = new Client([
    'base_uri' => 'http://gateway.marvel.com/v1/public/',
    'query' => [
        'apikey' => $public_key,
        'ts' => $ts,
        'hash' => $hash
    ]
]);

function generate_charcter($id_numbers, $client){
    //pick random charchter id
    $rand_key = rand(0,sizeof($id_numbers));
    $rand_id = $id_numbers[$rand_key];

    //send get request
    $query = $client->getConfig('query');
    $response = $client->get('http://gateway.marvel.com/v1/public/characters/' . $rand_id, ['query' => $query]);
    //trasform the json response-.body into php array
    $response = json_decode($response->getBody(), true);

    //charcter data from the response
    $character_data = $response['data']['results'][0];
    //charcter image and name
    $image = $character_data["thumbnail"]["path"] . '.' . $character_data["thumbnail"]["extension"];
    $name = $character_data['name'];

    $data = ["name" => $name, "image"=>$image, "id" => $rand_id];
    return $data;
}

$char1 = generate_charcter($id_numbers, $client);
$char2 = generate_charcter($id_numbers, $client);
$fighters = ["character1" => $char1, "character2" => $char2];

//print_r($fighters);

echo $twig->render('voting.html', ['fighters' => $fighters]);

