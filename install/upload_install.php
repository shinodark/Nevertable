<?php

require("dialog.php");

$up_dir = "./replays";

if (chmod($up_dir, 0777))
{
  echo "Permission change successfully on ".$up_dir;
}
else
{
  echo "Error in chmod on ".$up_dir;
}
