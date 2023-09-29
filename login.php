<?php
    session_start();
    session_destroy();
    require 'dbh/dbh.php';
?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="./styles/login.css">
</head>
<body>
  <div class="popup-form">
    <h2>Login</h2>
    <form id="login-form" action="dbh/manage_data.php" method="post">
      <p class="error" id="error"></p>
      <input type="text" id="username" name="username" placeholder="Username" required>
      <input type="password" id="password" name="password" placeholder="Password" required>
      <button name="action" value="login" type="submit" id="login">Login</button>
    </form>
  </div>
</body>
</html>
<script>
    checkError();
    function checkError() {
        var errorMsg = "<?php echo $_SESSION['login_error']; ?>";
        if (errorMsg != null) {
            document.getElementById("error").innerText = errorMsg;
        }
    }
</script>