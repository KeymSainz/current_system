<?php
/**
 * Fix&Go — OTP redirect shim
 * login backend sends redirect:'otp.php' — this forwards to the actual OTP page.
 */
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
$host = 'https://' . $_SERVER['HTTP_HOST'];
header('Location: ' . $host . '/fixandgo/otp.html' . $qs, true, 302);
exit;
