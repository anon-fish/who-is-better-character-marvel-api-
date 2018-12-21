<?php
require 'vendor/autoload.php';
require_once __DIR__.'/bootstrap.php';
try {
    require_once 'pdo_connect.php';
} catch (Exception $e) {
    $error = $e->getMessage();
}
include 'data/ids_names.php';
//only rank those who had the honor of existing in a battle
$sql = "SELECT * FROM `characters_elo` WHERE `played` = 1 ORDER BY `elo` DESC";
$scores = $conn->query($sql);
$scores->setFetchMode(PDO::FETCH_ASSOC);
$scores = $scores->fetchAll();


if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}


//number of characters per page
$limit = 10;
$offset = ($page - 1) * $limit;
//calculate total no. of pages
$sql = "SELECT COUNT(*) FROM `characters_elo` WHERE `played` = 1";
$scores = $conn->query($sql);
$scores->setFetchMode(PDO::FETCH_ASSOC);
$scores_count = $scores->fetchAll();

$total_pages = ceil($scores_count[0]['COUNT(*)'] / $limit);

$sql = "SELECT * FROM `characters_elo` WHERE `played` = 1 ORDER BY `elo` DESC LIMIT $offset, $limit";
$scores = $conn->query($sql);
$scores->setFetchMode(PDO::FETCH_ASSOC);
$scores = $scores->fetchAll();
//get characters names
$i = $offset;
$ranked_characters = [];
foreach($scores as $score){
    $i++;
    array_push($ranked_characters, array('rank'=>$i,'name'=>$id_name[$score['id']], 'score'=>$score['elo']));
}



//what we need to send is the offset data based on page no., total no. of pages
echo $twig->render('ranking.html', ['scores' => $ranked_characters, 'tpages' => $total_pages, 'currentPage' => $page]);



//
?>