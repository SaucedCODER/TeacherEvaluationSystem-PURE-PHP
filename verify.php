<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    
</head>
<?php include 'header.php' ?>
<body class="hold-transition login-page bg-black"style="background-color:#026e02;display:flex;
flex-direction:column;align-items:center;">
  <!-- <h2><b><?php //echo $_SESSION['system']['name'] ?> - Admin</b></h2> -->
  <style>
  .container{
    display: flex;
    justify-content: center;
    text-align: center;
    width: 60vw;
  }
  .text-con p{
    font-size: 20px;
  }
  form p{
    text-align: center;
    margin:0;
    margin-top: 1rem;
    color: black;
  }
  .text-con p,.text-con h4{
    color:#ececec;
  }
  .container img{
    width: 100px;
    height: 100px;
    margin:0 .8rem ;
  }
  .card {
    text-align:center;
    padding:45px;
  }
  .verify {
    font-family: 'Nunito', sans-serif;
   font-size:30px;

  }
 
  </style>
<div class="container">
  <img src="./ibsmafiles/logo2ibsma.jpg" alt="logo1" >
  <div class="text-con">
  <h1>Teacher Evaluation System</h1>
<p>Version 1.0</p>
<h4>Institute of Business Science and Medical Arts</h4>
  </div>
  <img src="./ibsmafiles/logoibsma.png" alt="logo1" >


</div>
  

<div class="login-box"style="width:50vw;">
  <div class="login-logo">
    <a href="#" class="text-white"></a>
  </div>
  <!-- /.login-logo -->
  <div class="card" >

  <p>
  <span class="fa-regular fa-font-case verify " style = "color:black">Code Verification </span>
  </p>
  
  
    <div class="card-body login-card-body">
      <form action="" id="login-form">
        <div class="input-group mb-3">
          <input type="number" class="form-control" name="email" required placeholder="Enter verification code">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fa-solid fa-shield-halved"></span>
              
            </div>
          </div>
        </div>
      
        <button type="submit" class="btn btn-primary btn-block"style="width:100%;background-color:green;border-color:green;">Submit</button>

   
<?php include 'footer.php' ?>

</body>
</html>
