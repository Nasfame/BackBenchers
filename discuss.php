<?php
// https://webdamn.com/build-discussion-forum-with-php-and-mysql/

function connecFailed($sql)
{
    if ($sql->connect_error) 
        return true;
    return false;
}

function refresh($time,$url)
{
    header('Refresh: ' . $time.$url);
}

function invalid($sql,$query){
    if($sql->query($query)==FALSE){
        return True;
    }
    return False;
}

function get($sql, $query)
{
    $result = $sql->query($query);
    while ($row = $result->fetch_object()) {
        yield $row;
    }
    $result->free_result();
}

function insert($sql, $user, $comment){
    $getid = sprintf("select id from users where name =\"%s\" limit 1; ", $user); //User quotes " " sql parser
    if(invalid($sql,$getid)){
        echo "Invalid User";
        return;
    }
    foreach(get($sql,$getid) as $s){
        $id = $s->id;
    }
    $query = sprintf(
        "insert into discussion (user_id,comments,time) values (%d,\"%s\",now());",
        $id,
        $comment
    );
    if($sql->query($query)==True)
        $url = "discuss.php";
        $_GET['user']=$user;
        $_SERVER["REQUEST_METHOD"] = "GET";
        discussion($sql,$url);
}

function htmlHead()
{
    echo "
    <html>
    <head> </head>
	<title>Back Bencher</title>
    <body>";
}

function htmlFoot()
{
    echo "</body> </html>";
}

function csshead()
{
    echo "<style>";
}

function cssfoot()
{
    echo "</style>";
}

function submit($url, $user)
{
    printf(
        "
        <form method = POST> 
        <br> <label class = user>%s</label> </br> 
        <input name = user type=hidden value=%s> 
        <input type = text name = comment class = comments>
        <input type = submit>
        </form>    
    ",
        $user,
        $user,
    );
}

function discussion($sql, $url)
{
    $query =
        "select u.name as user,d.comments as comment from discussion d join users u on u.id = d.user_id order by d.time asc;";
    $posts = get($sql, $query);
    htmlHead();
    foreach ($posts as $p) {
        csshead();
        echo ".comments { //&nbsp;
            width: 400px;
            height: 40px;
            margin-left:50px;
            font: TimesNewRoman;
            font-color:Red;
            background-color: powderblue;
        }";
        echo ".user { //&nbsp;
            width: 400px;
            height: 40px;
            font: TimesNewRoman;
            font-color:Red;
            background-color: Red;
      }";
        cssfoot();
        printf( 
            "<div class = user> %s <div class = comments> %s </div> </div>",
            $p->user,
            $p->comment
        );
    }
    if (isset($_GET["user"])) {
        $user = $_GET["user"];
        submit($url, $user);
    }
}

$sql = new mysqli(
    "remotemysql.com:3306",
    "berY011Sco",
    "GMAjJahgB1",
    "berY011Sco"
);
$url = "discuss.php"; //$_SERVER['PHP_SELF'];

if (connecFailed($sql)) {
    echo "Connection Unsuccessful, Refreshing";
    refresh(0.5, $_SERVER['PHP_SELF']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_REQUEST['user'];
    $comment = $_REQUEST['comment'];
    insert($sql, $user, $comment);
}
else{ 
    discussion($sql,$url);
}

htmlFoot();
$sql->close();
// phpinfo();
?>