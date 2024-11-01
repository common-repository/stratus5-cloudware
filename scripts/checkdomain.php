<?php

$url = $_POST['domain'];
$file_headers = @get_headers($url);
if(strpos($file_headers[0], '200')) {
    echo "YES";
}
else {
    echo "NO";
}

?>