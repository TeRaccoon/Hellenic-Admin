<?php
session_start();

require 'dbh/initialise.php';
$conn = initialise("low");

?>
<!DOCTYPE html>
<html>

<head>
    <title>Welcome</title>
    <?php include 'templates/head.php'; ?>
</head>
    <body>
        <div id="nav-placeholder"></div>
        <div class="main main-content">
            <h5>Pages / <p class="inline-shallow">Welcome</p>
            </h5>
            <div class="welcome-grid">
                <div class="card welcome-item-1">
                    <div class="welcome-title">
                        <h3>Welcome to Hellenic Grocery</h3>
                        <span><img style="width:100px;height:100px;" src="images/logo.jpg" alt="Logo"></span>
                    </div>
                </div>
                <div class="card welcome-item-2">
                    <h3>Notifications</h3>
                    <ul class="notification-list">
                        <li><a href="#">Invoice: INV32 needs printing</a></li>
                        <li><a href="#">Shipment: Shipment 045SB arriving today</a></li>
                        <li><a href="#">Stock: New stock needs entering to system</a></li>
                    </ul>
                </div>
                <div class="card welcome-item-3">
                    
                </div>
            </div>
        </div>
    </body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadElement("sidenav.php", "nav-placeholder");
});
</script>