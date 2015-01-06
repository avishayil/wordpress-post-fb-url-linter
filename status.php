<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
include( $parse_uri[0] . 'wp-load.php' );

$post_id = $_GET['id'];
$status = get_post_status($post_id);

echo "The status of the post " . $post_id . " is: " . $status;
?>