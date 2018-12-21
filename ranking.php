<?php
try {
    require_once 'pdo_connect.php';
} catch (Exception $e) {
    $error = $e->getMessage();
}

//retrieve score (game result) from voting.html
if (($_SERVER['REQUEST_METHOD'] == 'POST') && (!empty($_POST['winner']))){
    //print_r($_POST);
    $ids = preg_split("/,/", $_POST["ids"]);
    $winner = $_POST["winner"];
    if($ids[0] == $winner){
        $loser = $ids[1];
    }else{
        $loser = $ids[0];
    }
}

//retrieve elo from database
//winner at index 0, loser at index 1
$sql = 'SELECT * FROM `characters_elo` WHERE `id` IN (' . $winner . ',' . $loser . ')';
$elo = $conn->query($sql);
$elo->setFetchMode(PDO::FETCH_ASSOC);
// something like this [0] => ( [id] => 1009383 [elo] => 1500 ) [1] => ( [id] => 1009534 [elo] => 1500 )
$elo = $elo->fetchAll();

//calculate the new elo based on game score.
$k = 16;
$eloW = $elo[0]['elo'];
$Wplayed = $elo[0]['played'];
$eloL = $elo[1]['elo'];
$Lplayed = $elo[1]['played'];




//expected score before game
$exW = 1 / (1 + (pow(10, ($eloL - $eloW) / 400)));
$exL = 1 / (1 + (pow(10, ($eloW - $eloL) / 400)));
//new elo for each player
$neweloW = $eloW + ($k * (1 - $exW));
$neweloL = $eloL + ($k * (0 - $exL));
//don't allow player elo to become less than
if ($neweloL < 1500){
    $neweloL = 1500;
}

//update the database with new values
$sql1 = "UPDATE `characters_elo` SET `elo`=:newelo WHERE `id` = :id";
$sql2 = "UPDATE `characters_elo` SET `elo`=:newelo WHERE `id` = :id";
$stmt = $conn->prepare($sql1);
$stmt->execute(['newelo'=>$neweloW, 'id'=>$winner]);
$stmt = $conn->prepare($sql2);
$stmt->execute(['newelo'=>$neweloL, 'id'=>$loser]);

//in case they are new to the ranking
    if($Wplayed == 0){
        $sql = "UPDATE `characters_elo` SET `played`=1 WHERE `id` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id'=>$winner]);
    }
    if ($Lplayed == 0){
        $sql = "UPDATE `characters_elo` SET `played`=1 WHERE `id` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id'=>$loser]);
    }
//redirect to voting page (for the client it will be just relaod)
header('Location: http://localhost/projects/marvel/prototype/');
?>