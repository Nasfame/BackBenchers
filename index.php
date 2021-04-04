<?php


echo "You are Logging In As Guest, Tap Submit & Post your Name";
$user = "Guest";
printf("<form method = GET action =  discuss.php >
<input name = user type=hidden value=%s> 
<input type = submit>
</form>
",$user);

// header("Location: discuss.php");
exit();

x
?>