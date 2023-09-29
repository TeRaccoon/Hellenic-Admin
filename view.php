<?php
session_start();
include 'dbh/data_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title id="pageTitle"></title>
    <?php include 'templates/head.php'; ?>
</head>
<body>
    <div id="nav-placeholder"></div>
    <div class="main main-content">
        <h5>Pages / <p id="pageName" class="inline-shallow"></p></h5>
        <div id="widget-placeholder" class="grid-container">
            <div id="table-placeholder" class="card item12">
                <div class="tabs" id="tabs">
                </div>
                <?php include 'templates/table.php'; ?>
            </div>
        </div>
    </div>
    <div id="form-placeholder">
        <?php include 'templates/forms.php'; ?>
        <div id="add-form-container" class="popup-form">
        </div>
        <div id="edit-form-container" class="popup-form">
        </div>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadElement("sidenav.php", "nav-placeholder");
        loadElement("toolbar.html", "widget-placeholder", configure);
    });
    function configure()
    {
        formatPage();
        collectErrorData();
    }
</script>