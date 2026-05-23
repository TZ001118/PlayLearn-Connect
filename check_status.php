<?php
require 'db_conn.php';

$res = $conn->query("SELECT config_value FROM site_config WHERE config_key = 'maintenance_mode'");
echo trim($res->fetch_assoc()['config_value']); // 只返回 1 或 0
?>