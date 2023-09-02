<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('./db_connect.php');
ob_start();
// if(!isset($_SESSION['system'])){

$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach ($system as $k => $v) {
  $_SESSION['system'][$k] = $v;
}
// }
ob_end_flush();
?>
<?php
if (isset($_SESSION['login_id']))
  header("location:index.php?page=home");
?>
<?php include 'header.php' ?>

<body class="hold-transition bg-black " style="background-color:white;display:flex;width:100vw;height:100vh;
align-items:center;">
  <!-- <h2><b><?php //echo $_SESSION['system']['name'] 
              ?> - Admin</b></h2> -->
  <style>
    .container {
      display: flex;
      justify-content: center;
      text-align: center;
      width: 60vw;
    }

    .text-con p {
      font-size: 20px;
    }

    .text-con {
      color: #333;
    }

    form p {
      text-align: center;
      margin: 0;
      margin-top: 1rem;
      color: black;
    }

    .text-con p,
    .text-con h4 {
      color: gray;
    }

    .container img {
      width: 100px;
      height: 100px;
      margin: 0 .8rem;
    }

    @media screen and (max-width: 800px) {
      .login-box {
        width: 70vw !important;
        height: 50vh !important;
        margin: 0 !important;
      }
    }

    @media screen and (max-width: 600px) {

      body {
        padding: 0 !important;
        margin: 0;
        align-items: stretch !important;

      }

      .login-box {
        width: 100vw !important;
        height: 50vh !important;
        margin: 0 !important;
      }

      .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 1.5rem auto !important;
      }

      .container img[alt="logo1"] {
        width: 150px !important;
        height: auto !important;
        margin: 1rem auto;

      }

      .container h1 {
        font-size: 1.5rem !important;
      }

      .container img[alt="logo2"],
      .container h4 {
        display: none;
      }

      .card {
        width: 100% !important;
        height: 100% !important;

      }

      .card-body {
        padding: 3em 1em !important;

      }

    }
  </style>
  <div class="container">


    <div class="text-con">
      <h1>Teacher Evaluation System</h1>
      <p>2023 Modified</p>

    </div>


  </div>


  <div class="login-box" style="width:50vw;">
    <div class="login-logo">
      <a href="#" class="text-white"></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body" style="padding:2em 4em;">
        <form action="" id="login-form">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" required placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3 ">
            <input type="password" class="form-control" name="password" id="e-password" required placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <i class="fa-solid fa-eye-slash" id="showpassword" style="cursor: pointer;"></i>
                <!-- <span class="fas fa-lock"></span> -->
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block" style="width:100%;background-color:black;border-color:green;">Log In</button>

          <p>Forgot password? <a href="forgot-password.php">Click here!</a></p>

        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
  <script>
    const Showpassword = document.querySelector('#showpassword');
    const passwordInput = document.querySelector('#e-password');
    Showpassword.addEventListener("click", function() {
      this.classList.toggle("fa-eye");
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
    })
    $(document).ready(function() {
      $('#login-form').submit(function(e) {
        e.preventDefault()
        start_load()
        if ($(this).find('.alert-danger').length > 0)
          $(this).find('.alert-danger').remove();
        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: $(this).serialize(),
          error: err => {
            console.log(err)
            end_load();
          },
          success: function(resp) {
            if (resp == 1) {
              location.href = 'index.php?page=home';
            } else {
              $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
              end_load();
            }
          }
        })
      })
    })
  </script>
  <script src="https://kit.fontawesome.com/15c0af9a21.js" crossorigin="anonymous"></script>
  <?php include 'footer.php' ?>

</body>

</html>