<?php
/*
    $curl = curl_init();
    $url = "https://www.marvel.com/comics/characters";
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);

    preg_match_all('#(?<=\/comics\/characters\/)(.*?)(?=\/)#', $result, $match);

    $characters = $match[0];
    $sorted = array_unique($characters);
    asort($sorted);
    $final = array_values($sorted);
    //suggestion:for updating make it possible to generate and write the array into a file that's included in the main code

    foreach($final as $id){
        echo $id;
        echo ',';
    }

    //insert the id values into database *-for production only-*
*/

//////////////////////////////////////////////////////////////////////////

//scrape for id => name from a custom (not atuomated) source
//$file = fopen("data/source.html", "r")  or die("Unable to open file!");
preg_match_all('#(?<=\/comics\/characters\/)(.*?)(?=\")#', file_get_contents("data/source.html"), $matched);
$characters = $matched[0];

//print_r($characters);
foreach($characters as $character){
    $character = preg_split("#/#", $character);
    $ids_names[$character[0]] = str_replace('_',' ',$character[1]);
    //print_r($character); echo '<br>';
}
//var_export($ids_names);
file_put_contents('data/ids_names.php', var_export($ids_names, true));


//////////////////////////////////////////////////////////////////////////

/*
//spam calling the api for names
//**FAILED**

set_time_limit(0);

require 'vendor/autoload.php';
include 'data/id_numbers.php';

use GuzzleHttp\Client;

//marvel api authentication
$ts = time();
$public_key = '471ec0f061cbce7ea087b45e36c62971';
$private_key = '5293b2ada40321cd2f102632e71c734e46926ca0';
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
$i = 0;
$file = fopen("data/ids_names.php", "a")  or die("Unable to open file!");
fwrite($file, "<?php \n \$ids_names = [\n");
foreach($id_numbers as $id){
    $query = $client->getConfig('query');
    $response = $client->get('http://gateway.marvel.com/v1/public/characters/' . $id, ['query' => $query]);
    //trasform the json response-.body into php array
    $response = json_decode($response->getBody(), true);
    $name = $response['data']['results'][0]['name'];
    fwrite($file, $id . '=>' . '"' . $name . '"' . ",\n");
    echo $i++ . "\r";
    //echo $id . '=>' . $name . "\n";
}
fwrite($file, "];");
*/

?>