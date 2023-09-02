  <style>
    .user-img {
      border-radius: 50%;
      height: 25px;
      width: 25px;
      object-fit: cover;
    }

    @media screen and (max-width: 800px) {
      .haha11 {
        width: auto !important;

      }


    }

    @media screen and (max-width: 500px) {

      .arrowdaw,
      #fulss {
        display: none;
      }

      #t-eval-title {
        padding: 8px 0 !important;
        font-size: .85rem;
        line-height: 1.8;
      }

      .boxxx {
        padding: 8px 0 !important;
        line-height: .5;


      }


    }
  </style>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-primary navbar-dark " style="background-color:black;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <?php if (isset($_SESSION['login_id'])) : ?>
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
        </li>
      <?php endif; ?>
      <li id="fulss">
        <a class="nav-link text-white " id="t-eval-title" href="./" role="button">
          <large><b id="t-eval-title"><?php echo $_SESSION['system']['name'] ?></b></large>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">

      <li class="nav-item" id="fulss">
        <a class="nav-link boxxx" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt boxxx"></i>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link haha11" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
          <span>
            <div class="d-felx badge-pill">
              <span class=""><img src="assets/uploads/<?php echo $_SESSION['login_avatar'] ?>" alt="" class="user-img border "></span>
              <span><b><?php echo ucwords($_SESSION['login_firstname']) ?></b></span>
              <span class="fa fa-angle-down ml-2"></span>
            </div>
          </span>
        </a>
        <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
          <a class="dropdown-item" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i> Manage Account</a>
          <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <script>
    $('#manage_account').click(function() {
      uni_modal('Manage Account', 'manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
    })
  </script>