<?php 
use \Firebase\JWT\JWT;

include('vendor/autoload.php');
include('head.php');
include('pdo_connection.php');

$usernames;
$users = $pdo->query('SELECT username FROM Users')->fetchAll();

foreach ($users as $key => $user) {
  $usernames[] = $user[0];
}

var_dump($_POST);
if(!$_POST['token']) {
    die(0);
} 

$decoded_token = JWT::decode(trim($_POST["token"]), 'jeanmiconstruction', ['HS256']);
$decoded_token = (array) $decoded_token;
$decoded_token["data"] = (array) $decoded_token["data"];

?>

<section>
  <div class="container mb-4" style="text-align: center; margin-top: 2%;">
    Well played, you accessed the admin page. Here is the flag : ENSIBS{je4nmicOnstructi0ncyb3rcl0uDbigd4t4m4nag3ment_etape1}
  </div>

  <div class="container text-uppercase mb-4" style="text-align: center;">
    <div> Site views :</div>
    <canvas id="site-views" width="60%" height="20%"></canvas>

    <div style="margin-top: 5%"> Potential income :</div>
    <canvas id="pot-income" width="60%" height="20%"></canvas>
  </div>

<?php 
if(in_array($decoded_token["data"]["username"], $usernames) && $decoded_token["data"]["is_jeanmi_the_god"] == "true"){
?>
<hr>
<div class="container mb-4" style="text-align: center; margin-top: 4%;">
Welcome, Jean-Mi ! Second flag is : ENSIBS{je4nmicOnstructi0nP4trickBNic0l4sSetG3rardDvsLEFISC_etape2}
</div>


<div class="container mb-4">
    <button class="btn btn-xl" onclick="sendCommand()"> Check server's SSH status : </button>
    <form id="command-form" action="console.php" method="POST">
        <input type="hidden" id="command-content" name="command" value="systemctl status sshd">
        <input type="hidden" id="anti-cheat-command" name="cheat" value="bdf1ed5d23a113128980c5e304cae020">
    </form>
    <div id="console-div" class="console">
        $
    </div>
</div>
<?php
}
?>


</section>

<script src="js/Chart.js"></script>
<script>
var ctx = document.getElementById("site-views").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["5 days ago", "4 days ago", "3 days ago", "2 days ago", "Yesterday", "Today"],
        datasets: [{
            label: '# of unique user access (in thousand)',
            data: [12, 19, 3, 5, 2, 31],
            backgroundColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

var ctx2 = document.getElementById("pot-income").getContext('2d');
var myLineChart = new Chart(ctx2, {
  type: 'line',
  data: {
    labels: ["5 days ago", "4 days ago", "3 days ago", "2 days ago", "Yesterday", "Today"],
    datasets: [{
        label: 'Possible income (in $)',
        data: [4000000, 6000000, 600000, 800000, 100000, 11000000],
        backgroundColor: 'rgba(255, 99, 132, 1)',
        borderColor: 'rgba(255, 255, 255, 1)'

    }]
  }
});
</script>


<script>
function sendCommand(){
    const req = new XMLHttpRequest();
    req.open('POST', 'console.php', true); 
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    req.onreadystatechange = function() {
        if(req.readyState == 4 && req.status == 200) {
            document.getElementById("console-div").innerHTML = '$ ' + req.responseText;
            console.log(req.responseText);
        }
    }

    req.send(
        'cheat=' + document.getElementById("anti-cheat-command").value + 
        '&command=' + document.getElementById("command-content").value
    );
}
</script>


<?php include('foot.php'); ?>