<?php
  $errorlog = $_SERVER['DOCUMENT_ROOT'] . "/error.log";
  ini_set('display_errors', FALSE);
  ini_set('log_errors', TRUE);
  ini_set('error_log', $errorlog);
  $dbcfg = include('config.php');
  $connection = new mysqli($dbcfg['host'], $dbcfg['user'], $dbcfg['pass'], $dbcfg['dbname']);
  $sql = "SELECT auth_id,
    name,
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
  $result = $connection->query($sql);
?>
<html>
  <head>
  <title>Scrim Stats</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/cosmo/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.css" />
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.js"></script>
  <link rel="shortcut icon" href="https://xenogamers.com/favicon.ico" />
  </head>
  <body>
    <div class="jumbotron shadow"><div class="container-fluid"><h1 style="width:75%;margin:auto;margin-top:0px;">xG Scrim Stats</h1></div></div>
    <div class="container-fluid body-content text-center"><br />
      <div style="margin:auto;margin-top:0px;">
        <?php
          if ($connection->connect_error) {
            echo "<div class=\"alert alert-dismissible alert-danger\">\n";
            echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n";
            echo "<p class=\"mb-0\">Unable to connect to database. Data may be unavailable.</p>\n";
            echo "</div>\n";
          }
        ?>
        <table id="scrim_stats" cellspacing="0" class="stats-table table table-hover table-dark table-sm shadow" width="100%">
          <thead>
            <tr>
              <td>Player</td>
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
          <tbody>
            <?php
              while ($row = mysqli_fetch_array($result)) {
                echo '
                  <tr>
                  <td>' . $row["name"] . '</td>
                  <td>' . $row["total_Games"] . '</td>
                  <td>' . $row["total_Rounds"] . '</td>
                  <td>' . $row["total_Dmg"] . '</td>
                  <td>' . $row["total_Kills"] . '</td>
                  <td>' . $row["total_Assists"] . '</td>
                  <td>' . $row["total_Deaths"] . '</td>
                  <td>' . round($row["KDR"], 2) . '</td>
                  <td>' . round($row["AKPR"], 2) . '</td>
                  <td>' . round($row["AKPG"], 2) . '</td>
                  <td>' . round($row["ADPR"], 2) . '</td>
                  <td>' . round($row["ADPG"], 2) . '</td>
                  <td>' . round($row["AKDR"], 2) . '</td>
                  <td>' . round($row["WON"], 2) . '</td>
                  <td>' . round($row["WIN_PERCENT"], 2) . '</td>
                  </tr>
                ';
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>
  <script>
    $(document).ready(function() {
      $('#scrim_stats').DataTable({
        "order": [[3, "asc"]],
        "searching": true,
        "info": false,
        "lengthChange": false,
        "pageLength": 20,
        "autoWidth":false,
      });
    });
  </script>
  <link rel="stylesheet" href="site.css" />
</html>
