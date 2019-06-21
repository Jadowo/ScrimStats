<?php  
$connect = mysqli_connect("localhost", "root", "password", "schematest");  
$query = "SELECT auth_id, name,
COUNT(ps.demo_id) AS total_Games,
SUM(rounds_played) AS total_Rounds,
SUM(total_dmg) AS total_Dmg,
SUM(kills) AS total_Kills,
SUM(assists) AS total_Assists,
SUM(deaths) AS total_Deaths,
SUM(kills)/SUM(deaths) AS KDR,
SUM(kills)/SUM(rounds_played) AS AKPR,
SUM(kills)/COUNT(kills) AS AKPG,
SUM(total_dmg)/SUM(rounds_played) AS ADPR,
SUM(total_dmg)/COUNT(kills) AS ADPG,
SUM(kills/deaths)/COUNT(kills) AS AKDR,
SUM(CASE WHEN (ps.team = 't' AND d.t_rounds > d.ct_rounds) OR (ps.team = 'ct' AND d.t_rounds < d.ct_rounds) THEN 1 END) AS WON,
SUM(CASE WHEN (ps.team = 't' AND d.t_rounds > d.ct_rounds) OR (ps.team = 'ct' AND d.t_rounds < d.ct_rounds) THEN 1 END) / COUNT(ps.demo_id) AS WIN_PERCENT
FROM demo_player_stats AS ps
INNER JOIN demo_demos AS d
ON ps.demo_id = d.demo_id 
GROUP BY auth_id";  
$result = mysqli_query($connect, $query);  
?>
<html>  
    <head>  
    <title>Scrim Stats</title>  
    <link rel="stylesheet" href="style.css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>            
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />  
    </head>
    <style>
        body{
            background-color:#cacaca;
        }
    </style>
    <body>  
        <br /><br/>  
        <div class="container">  
            <h3>Scrim Stats</h3>  
            <br/>  
            <div class="table-responsive">  
                <table id="scrim_stats" cellspacing="0" class="cell-border hover order-column row-border" width="100%">  
                    <thead>  
                        <tr>  
                            <td>Name</td>
                            <td>Games Played</td>
                            <td>Rounds Played</td>
                            <td>Total Damage</td>
                            <td>Kills</td>
                            <td>Assists</td>
                            <td>Deaths</td>
                            <td>K/D</td>
                            <td>Average Kills Per Round</td>
                            <td>Average Kills Per Game</td>
                            <td>Average Damage Per Round</td>
                            <td>Average Damage Per Game</td>
                            <td>Average K/D</td>
                            <td>Games Won</td>
                            <td>Win Percent</td>
                        </tr>  
                    </thead>  
                    <?php
                    while($row = mysqli_fetch_array($result)){  
                        echo '  
                        <tr>  
                        <td>'.$row["name"].'</td>
                        <td>'.$row["total_Games"].'</td>
                        <td>'.$row["total_Rounds"].'</td>
                        <td>'.$row["total_Dmg"].'</td>
                        <td>'.$row["total_Kills"].'</td>
                        <td>'.$row["total_Assists"].'</td>
                        <td>'.$row["total_Deaths"].'</td>
                        <td>'.$row["KDR"].'</td>
                        <td>'.$row["AKPR"].'</td>
                        <td>'.$row["AKPG"].'</td>
                        <td>'.$row["ADPR"].'</td>
                        <td>'.$row["ADPG"].'</td>
                        <td>'.$row["AKDR"].'</td>
                        <td>'.$row["WON"].'</td>
                        <td>'.$row["WIN_PERCENT"].'</td>
                        </tr>  
                        ';  
                    }  
                    ?>  
                </table>  
            </div>  
         </div>  
    </body>  
</html>  
<script>  
$(document).ready(function(){  
    $('#scrim_stats').DataTable();  
});  
</script> 