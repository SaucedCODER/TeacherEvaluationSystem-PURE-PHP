  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
      <div class="dropdown">
        <a style="position:relative;height:60px;background-size:cover;background-position:center;" href="./" class="brand-link">
          <h1 class="text-center" style="  display: inline-block;font-size:1.7em;padding-top:.2em;position:absolute;margin:0;color:white;inset:0 0 0 0;background-color:rgba(0, 0, 0, 0.2)">TES</h1>
        </a>

      </div>


    </div>
    <div class="sidebar " style="margin-top:0px;">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="./" class="nav-link nav-home">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item dropdown">


            <a href="./index.php?page=<?php
                                      $checking = $conn->query("SELECT id from student_list sl where sl.id = {$_SESSION['login_id']} and sl.status = 'Irregular'")->num_rows;
                                      echo ($checking <= 0) ? 'evaluate' : 'evaluate2';
                                      ?>" class="nav-link nav-evaluate">
              <i class="nav-icon fas fa-th-list"></i>
              <p>
                Evaluate
              </p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
  <script>
    $(document).ready(function() {
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if (s != '')
        page = page + '_' + s;
      if ($('.nav-link.nav-' + page).length > 0) {
        $('.nav-link.nav-' + page).addClass('active')
        if ($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
          $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if ($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
          $('.nav-link.nav-' + page).parent().addClass('menu-open')
        }

      }

    })
  </script>