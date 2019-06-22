<?php
  $errorlog = $_SERVER['DOCUMENT_ROOT'] . "/error.log";
  ini_set('display_errors', FALSE);
  ini_set('log_errors', TRUE);
  ini_set('error_log', $errorlog);
  $dbcfg = include('config.php');
  $connection = new mysqli($dbcfg['host'], $dbcfg['user'], $dbcfg['pass'], $dbcfg['dbname']);
  $sql = "SELECT auth_id,
    name,
    COUNT(ps.demo_id) AS total_games,
    SUM(total_dmg) AS total_dmg,
    SUM(kills) AS total_kills,
    SUM(assists) AS total_assists,
    SUM(deaths) AS total_deaths,
    SUM(kills) / SUM(deaths) AS overall_kd,
    SUM(kills) / SUM(rounds_played) AS overall_kpr,
    SUM(total_dmg) / SUM(rounds_played) AS overall_dpr,
    SUM(kills) / COUNT(kills) AS average_kpg,
    SUM(total_dmg) / COUNT(kills) AS average_dpg,
    (SUM(CASE WHEN (ps.team = 't' AND d.t_rounds > d.ct_rounds) OR (ps.team = 'ct' AND d.t_rounds < d.ct_rounds) THEN 1 END) / COUNT(ps.demo_id)) * 100 AS win_rate
    FROM lithium.demo_player_stats AS ps
    INNER JOIN demo_demos AS d
    ON ps.demo_id = d.demo_id
    GROUP BY auth_id;";
  $result = $connection->query($sql);
?>
<html>
  <head>
    <title>Scrim Stats</title>
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
        <table id="scrim_stats" cellspacing="0" class="stats-table table table-hover table-dark table-sm shadow" width="100%">
          <thead>
            <tr>
              <td>Player</td>
              <td>Games Played</td>
              <td>Total Damage</td>
              <td>Kills</td>
              <td>Assists</td>
              <td>Deaths</td>
              <td>Overall K/D</td>
              <td>Overall K/R</td>
              <td>Overall D/R</td>
              <td>Average K/G</td>
              <td>Average D/G</td>
              <td>Winrate</td>
            </tr>
          </thead>
          <tbody>
            <?php
              while ($row = mysqli_fetch_array($result)) {
                echo '
                  <tr>
                  <td>' . $row["name"] . '</td>
                  <td>' . $row["total_games"] . '</td>
                  <td>' . $row["total_dmg"] . '</td>
                  <td>' . $row["total_kills"] . '</td>
                  <td>' . $row["total_assists"] . '</td>
                  <td>' . $row["total_deaths"] . '</td>
                  <td>' . round($row["overall_kd"], 2) . '</td>
                  <td>' . round($row["overall_kpr"], 2) . '</td>
                  <td>' . round($row["overall_dpr"], 2) . '</td>
                  <td>' . round($row["average_kpg"], 2) . '</td>
                  <td>' . round($row["average_dpg"], 2) . '</td>
                  <td>' . round($row["win_rate"], 2) . '</td>
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
        order: [[1, "desc"]],
        searching: true,
        info: false,
        lengthChange: false,
        pageLength: 20,
        autoWidth: false
      });
    });
  </script>
  <link rel="stylesheet" href="site.css" />
</html>
