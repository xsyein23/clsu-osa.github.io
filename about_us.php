<?php
session_start();
include 'backend/mysql_connect.php';

// Login
if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $ciphering = "AES-128-CTR";
  $option = 0;
  $encryption_iv = '1234567890123456';
  $encryption_key = "info";
  $encryption_email = openssl_encrypt($email, $ciphering, $encryption_key, $option, $encryption_iv);

  $ciphering = "AES-128-CTR";
  $option = 0;
  $encryption_iv = '1234567890123456';
  $encryption_key = "info";
  $encryption_password = openssl_encrypt($password, $ciphering, $encryption_key, $option, $encryption_iv);

  $sql = "SELECT * FROM account
      WHERE email = '$encryption_email'
      AND password = '$encryption_password'";

  $res = mysqli_query($conn, $sql);
  if (mysqli_num_rows($res) == 1) {
    $row = mysqli_fetch_assoc($res);

    $ciphering = "AES-128-CTR";
    $option = 0;
    $decryption_key = "info";
    $decryption_iv = '1234567890123456';
    $decryption = openssl_decrypt($row['email'], $ciphering, $decryption_key, $option, $decryption_iv);
    $session_email = $decryption;

    $_SESSION['fullname'] = $row['fullname'];
    $_SESSION['email'] = $session_email;
    $_SESSION['course'] = $row['course'];
    $_SESSION['userID'] = $row['userID'];
    $_SESSION['role'] = $row['role'];

    if (isset($row['userID'])) {
      $_SESSION['userID'] = $row['userID'];
    }
    
    if ($row['role'] == 1) {
      header("location:about_us.php");
      $_SESSION['status_success_admin'] = "success";
      session_unset($_SESSION['status_success_admin']);
    } else {
      header("location:about_us.php");
      $_SESSION['status_success_user'] = "success";
      session_unset($_SESSION['status_success_user']);
    }
  } else {
    $_SESSION['status_error'] = "error";
  }
}

//New Personnel
if (isset($_POST["handle_submit"])) {

  $name = $_POST['name'];
  $names = str_replace("'", "\'", $name);
  $position = stripslashes($_POST['position']);
  $positions = str_replace("'", "\'", $position);

  $temp = $_FILES['myfile']['tmp_name'];
  $directory = "upload/personnel/" . $_FILES['myfile']['name'];

  if (move_uploaded_file($temp, $directory)) {
    $sql = "INSERT INTO personnel SET 
              image='$directory',
              name = '$names',
              position='$positions',
               is_archive=0";
    $_SESSION['status_success_added'] = "success";
    if (mysqli_query($conn, $sql)) {
      $_SESSION['status_success_added'] = "success";
      header("location:about_us.php");
      session_unset($_SESSION['status_success_added']);
    } else {
      echo mysqli_error($conn);
      echo '<script>';
      echo "alert('Error Occur!');" . mysqli_error($conn);
      echo '</script>';
    }
  }
}

//Arhive Personnel
if (isset($_POST['archive'])) {
  $id = $_POST['archive_id_input'];
  $archive = 1;

  $sql = "UPDATE personnel SET is_archive='$archive' WHERE id=" . $id;
  if (mysqli_query($conn, $sql)) {
    $_SESSION['status_success_archive'] = "success";
    header("location:about_us.php");
    session_unset($_SESSION['status_success_archive']);
  } else {
    echo '<script language="javascript">';
    echo 'alert("error")';
    echo '</script>';
  }
}

// Update Personnel
if (isset($_POST["handle_submit_update"])) {

  $id = $_POST['per_id'];
  $name = $_POST['name'];
  $names = str_replace("'", "\'", $name);
  $positions = stripslashes($_POST['positions']);
  $position = str_replace("'", "\'", $positions);

  if (file_exists($_FILES['perImg']['tmp_name'])) {
    $temp = $_FILES['perImg']['tmp_name'];
    $directory = "upload/personnel/" . $_FILES['perImg']['name'];

    if (move_uploaded_file($temp, $directory)) {

      $sql = "UPDATE personnel SET 
          image='$directory',
          name = '$names',
          position='$position'
          WHERE id=" . $id;

      if (mysqli_query($conn, $sql)) {
        $_SESSION['status_success_update'] = "success";
        header("location:about_us.php");
        unlink("upload/personnel/" . $personnel['image']);
        session_unset($_SESSION['status_success_update']);
      } else {
        echo mysqli_error($conn);
        $_SESSION['status_error'] = "error";
        echo '<script>';
        echo "alert('Error Occur!');" . mysqli_error($conn);
        echo '</script>';
      }
    }
  } else {

    //echo "<script>alert('False');</script>";
    // Do this instead if image wasn't uplodaded
    $sql = "UPDATE personnel SET 
        name = '$names',
        position='$position'
        WHERE id=" . $id;

    if (mysqli_query($conn, $sql)) {
      $_SESSION['status_success_update'] = "success";
      header("location:about_us.php");
      session_unset($_SESSION['status_success_update']);
    } else {
      echo mysqli_error($conn);
      $_SESSION['status_error'] = "error";
      echo '<script>';
      echo "alert('Error Occur!');" . mysqli_error($conn);
      echo '</script>';
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="x-ua-compatible" content="ie=edge" />
  <title>Office of Student Affairs</title>
  <link rel="icon" href="assets/img/logo.png" class="icon">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/style_about.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/mdb.min.css" />
  <?php include 'embed/link.php'; ?>
</head>

<style>
  .card-container .cols {
    position: relative;
    overflow: hidden;
  }

  .card {
    position: relative;
    transition: transform 0.3s ease-in-out;
  }

  .card img {
    transition: filter ease-in-out;
    max-width: 100%;
    height: auto;
    display: block;
  }

  .buttons {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: none;
    /* Initially hide the buttons */
  }

  .buttons button {
    margin-right: 10px;
    /* Adjust the margin between buttons as needed */
  }

  .card-container:hover .buttons {
    display: flex;
    /* Show the buttons on hover */
  }

  .cols:hover .buttons {
    display: flex;
    /* Show the buttons on hover */
  }

  /* Additional CSS for role 1 users */
  <?php
  if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
    echo '
      .card-container:hover .card img {
        filter: blur(2px); /* Remove blur for role 1 users on hover */
      }

      .cols:hover .card img {
        filter: blur(2px); /* Remove blur for role 1 users on hover */
      }
    ';
  }
  ?>.fa-circle-exclamation {
    font-size: 110px;
    width: fit-content;
    margin-left: 35%;
    padding: 10px;
    margin-top: -15%;
    margin-bottom: 5%;
    background-color: #fff;
    border-radius: 50%;
    position: absolute;
  }
</style>

<body style="background-color: #fdfdfd">

  <div class="logo-header ">
    <div class="container-fluid">
      <div class="row d-flex justify-content-between">
        <div class="logo-header-left col-xl-7 col-md-7 col-xs-7 dp-xs-flex flex-row">
          <div class="logo mr-xs-3">
            <img src="assets/img/clsu-logo.png" alt="">
          </div>
          <div class="logo-text m-xs-0">
            <span class="logo-name">Central Luzon State University</span>
            <span class="logo-sub">Science City of Muñoz, Nueva Ecija, Philippines 3120</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid navi-section">
      <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars text-white"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="index.php" active>HOME</a>
          </li>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="about_us.php">ABOUT US</a>
          </li>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="impu/">IMPU</a>
          </li>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="cdesu/">CDESU</a>
          </li>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="gsu/">GSU</a>
          </li>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="sou/">SOU</a>
          </li>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="sdb/">SDB</a>
          </li>
          <?php
          //Archives link
          if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] == 1) {
              echo '<li class="nav-item me-2">
                      <a href="archive/" class="nav-link text-white">
                        ARCHIVES
                      </a>
                    </li>';
            }
          }


          // Complain link
          if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] == 1) {
              echo '<li class="nav-item me-2">
                    <a href="complain/" class="nav-link text-white">
                        COMPLAINS
                    </a>
                    </li>';
            }
          } ?>
        </ul>
      </div>
      <div class="d-flex align-items-center">
        <?php
        if (isset($_SESSION['role'])) {
          if ($_SESSION['role'] == 1 || $_SESSION['role'] == 0) {
            if ($_SESSION['role'] == 0) {
              $role = "Student";
            } elseif ($_SESSION['role'] == 1) {
              $role = "Admin";
            }

            echo '<li class="nav-item-out">
                        <div class="btn-group shadow-0">
                          <a type="button" class="link text-white dropdown-toggle" data-mdb-toggle="dropdown" aria-expanded="false">'
              . $_SESSION['fullname'] . ' | ' . $role .
              '</a>
                          <ul class="dropdown-menu">
                              <form action="backend/logout.php" method="POST">
                                  <li><button class="dropdown-item rounded-5" name="logout">LOGOUT</button></li>
                              </form>
                          </ul>
                        </div>
                    </li>';
          }
        } else {
          echo '<li class="nav-item-out">
                        <div class="btn-group shadow-0">
                        <a type="button" class="link text-white ps-3" data-mdb-toggle="modal" data-mdb-target="#login_Modal">
                            Login / Register
                        </a>
                        </div>
                    </li>
                  ';
        }
        ?>
      </div>
    </div>
  </nav>

  <div class="bg-image ripple" data-mdb-ripple-color="light">
    <img src="assets/img/banner1.png" class="banner__img" />
    <a href="#!">
      <div class="mask" style="background-color: hsla(0, 0%, 0%, 0.5)">
        <div class="row text-center">
          <div class="col-12 pt-3">
            <img src="assets/img/white-logo.png" alt="" class="banner_logo ">
          </div>
          <div class="col-12 pt-3">
            <h4 class="text-white mb-0 fw-bold">OFFICE OF STUDENT AFFAIRS</h4>
          </div>
        </div>
      </div>
    </a>
  </div>

  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">OVERVIEW</p>
        <p class="tag-sub ">The OSA serves as the center of information, activities, and services related to the co-curricular and extra-curricular needs of students. It also promotes the development of students’ talents, potentials, and leadership capabilities through its program thrusts of self-growth and awareness, cooperative living and learning, leadership development and enhancement, productive use of leisure, and enhanced cross-cultural adjustment.</p>
      </div>
    </div>
  </div>

  <div class="container pt-4">
    <div class="card mb-3 shadows border">
      <div class="row g-0">
        <div class="col-md-4">
          <img src="assets/img/osa-logo.jpg" alt="Trendy Pants and Shoes" class="img-fluid rounded-start" style="height: 40vh; object-fit: cover;" />
        </div>
        <div class="col-md-8">
          <div class="card-body">
            <h4 class="card-name fw-semibold">OSA MISSION</h4>
            <p class="card-text">
              OSA shall promote the development of the students’ talents,
              potentials and leadership capabilities through its program thrusts that promote
              self- awareness, self-growth and development, self- management, cooperative
              living and learning, leadership advancement, social responsibility, nationalism
              and patriotism and wise use and management of relevant information.
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-3 shadows border">
      <div class="row g-0">
        <div class="col-md-8">
          <div class="card-body">
            <h4 class="card-name fw-semibold">OSA VISION</h5>
              <p class="card-text">
                OSA-CLSU as a model center for student personnel services
                supportive of the co-curricular and extra-curricular needs of its clients for their
                well- rounded growth and development
              </p>
          </div>
        </div>
        <div class="col-md-4">
          <img src="assets/img/osa-logo.jpg" alt="Trendy Pants and Shoes" class="img-fluid rounded-end" style="height: 40vh; object-fit: cover;" />
        </div>
      </div>
    </div>
  </div>

  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">OSA PERSONNEL</p>
        <p class="tag-sub ">Meet all the Administrators and Staffs</p>
      </div>
    </div>
  </div>
  <div class="container d-flex justify-content-end mb-3">
    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
      echo '<button type="button" class="btn btn-primary shadows" data-mdb-toggle="modal" data-mdb-target="#add_personnel">
                <i class="fas fa-notes-medical"></i> Add Personnel
              </button>';
    }
    ?>
  </div>

  <!-- <div class="container mb-5">
    <?php
    $sql = "SELECT * FROM personnel where position='Dean'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) > 0) {
      while ($row = mysqli_fetch_assoc($res)) {
        $img = explode('/', $row['image']);
    ?>
        <div class="row m-3 row-cols-md-4">
          <div class="col m-auto">
            <div class="card-container">
              <div class="card h-100 shadows border">
                <img src="<?php echo $row['image']; ?>" class="card-img-top">
                <?php
                if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
                  <div class="buttons">
                    <?php
                    echo "<button type='button' data-id='$row[id]' class='btn btn-primary editper_Btn'>
                            <i class='fas fa-edit'></i>
                          </button>
                          
                          <button type='button' data-id='$row[id]' class='btn btn-danger delper_Btn'>
                            <i class='fas fa-trash'></i>
                          </button>";
                    ?>
                  </div>
                <?php   }
                ?>
                <div class="card-body">
                  <h5 class="card-name mt-3 text-center"><?php echo $row['name']; ?></h5>
                </div>
                <div class="card-footer">
                  <h5 class="text-secondary mt-3 text-center"><?php echo $row['position']; ?></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
    <?php
      }
    }
    ?>
    <div class="container">
      <div class="row m-3 mx-auto">
        <?php
        $sql = "SELECT * FROM personnel WHERE position <> 'Dean'";
        $res = mysqli_query($conn, $sql);
        $count = 0;

        if (mysqli_num_rows($res) > 0) {
          while ($row = mysqli_fetch_assoc($res)) {
            $img = explode('/', $row['image']);
            if ($count % 4 == 0 && $count > 0) { ?>
      </div>
      <div class="row m-3 mx-auto">
      <?php
            }
      ?>
      <div class="col cols">
        <div class="card h-100 shadows border">
          <img src="<?php echo $row['image']; ?>" class="card-img-top">
          <?php
            if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
            <div class="buttons">
              <?php
              echo "<button type='button' data-id='$row[id]' class='btn btn-primary editper_Btn'>
                            <i class='fas fa-edit'></i>
                          </button>
                          
                          <button type='button' data-id='$row[id]' class='btn btn-danger delper_Btn'>
                            <i class='fas fa-trash'></i>
                          </button>";
              ?>
            </div>
          <?php   }
          ?>
          <div class="card-body">
            <h5 class="card-name mt-3 text-center"><?php echo $row['name']; ?></h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center"><?php echo $row['position']; ?></h5>
          </div>
        </div>
      </div>
  <?php
            $count++;
          }
        }
  ?>
      </div>
    </div>
  </div> -->

  <div class="container mb-5">
    <?php
    $sql = "SELECT * FROM personnel where position='Dean' and is_archive=0";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) > 0) {
      while ($row = mysqli_fetch_assoc($res)) {
        $img = explode('/', $row['image']);
    ?>
        <div class="row m-3 row-cols-md-4">
          <div class="col m-auto">
            <div class="card-container">
              <div class="card h-100 shadows border">
                <img src="<?php echo $row['image']; ?>" class="card-img-top">
                <?php
                if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
                  <div class="buttons">
                    <?php
                    echo "<button type='button' data-id='$row[id]' class='btn btn-primary editper_Btn'>
                            <i class='fas fa-edit'></i>
                          </button>
                          
                          <button type='button' data-id='$row[id]' class='btn btn-danger arcper_Btn'>
                            <i class='fas fa-archive'></i>
                          </button>";
                    ?>
                  </div>
                <?php
                }
                ?>
                <div class="card-body">
                  <h5 class="card-name mt-3 text-center"><?php echo $row['name']; ?></h5>
                </div>
                <div class="card-footer">
                  <h5 class="text-secondary mt-3 text-center"><?php echo $row['position']; ?></h5>
                </div>
              </div>
            </div>
          </div>
        </div>
    <?php
      }
    }
    ?>
    <div class="container mt-4">
      <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php
        $sql = "SELECT * FROM personnel  WHERE position <> 'Dean' and is_archive=0";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) > 0) {
          while ($row = mysqli_fetch_assoc($res)) {
            $img = explode('/', $row['image']); ?>
            <div class="col cols">
              <div class="card h-100 shadows border">
                <img src="<?php echo $row['image']; ?>" class="card-img-top">
                <?php
                if (isset($_SESSION['role']) && $_SESSION['role'] == 1) { ?>
                  <div class="buttons">
                    <?php
                    echo "<button type='button' data-id='$row[id]' class='btn btn-primary editper_Btn'>
                            <i class='fas fa-edit'></i>
                          </button>
                          
                          <button type='button' data-id='$row[id]' class='btn btn-danger arcper_Btn'>
                            <i class='fas fa-archive'></i>
                          </button>";
                    ?>
                  </div>
                <?php   }
                ?>
                <div class="card-body">
                  <h5 class="card-name mt-3 text-center"><?php echo $row['name']; ?></h5>
                </div>
                <div class="card-footer">
                  <h5 class="text-secondary mt-3 text-center"><?php echo $row['position']; ?></h5>
                </div>
              </div>
            </div>

        <?php
          }
        } ?>
      </div>
    </div>
  </div>

  <!-- <div class="container mb-5">
  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">OSA PERSONNEL</p>
        <p class="tag-sub ">Meet all the Administrators and Staffs</p>
      </div>
    </div>
  </div>

  <div class="container mb-5">
    <div class="row m-3 row-cols-md-4">
      <div class="col m-auto">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Dean -  Dr. Irene G. Bustos.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Dr. Irene G. Bustos</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Dean</h5>
          </div>
        </div>
      </div>
    </div>

    <div class="row m-3">
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Assoc. Prof. Chrisdell C. Munsayac.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Assoc. Prof. Chrisdell C. Munsayac</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Assoc. Prof. Ferlyn Reyes-Colar.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Assoc. Prof. Ferlyn Reyes-Colar</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Assoc. Prof. Ma. Magdalena C. Galang.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Assoc. Prof. Ma. Magdalena C. Galang</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Asst. Prof. Mark Allan C. Mananggit.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Assoc. Prof. Mark Allan C. Mananggit</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>

    </div>

    <div class="row m-3">
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Dr. Jayson L. Marzan.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Dr. Jayson L. Marzan</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Ms. Anna Marie T. Del Rosario.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Anna Marie T. Del Rosario</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Ms. Bernadette O. Binayug.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Bernadette O. Binayug</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Faculty - Ms. Joan Katrina B. Pelagio.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Joan Katrina B. Pelagio</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Faculty</h5>
          </div>
        </div>
      </div>

    </div>

    <div class="row m-3">
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/In-Charge CDESU - Assoc. Prof. Rochelle Ann V. Pararuan.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Assoc. Prof. Rochelle Ann V. Pararuan</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">In-Charge CDESU</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/In-Charge GSU - Asst. Prof. Alexis G. Ramirez.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Asst. Prof. Alexis G. Ramirez</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">In-Charge GSU</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/In-Charge IMPU - Ms. Kathleen Kay C. Antonio.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Kathleen Kay C. Antonio</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">In-Charge IMPU</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/In-Charge SOU - Assoc. Prof. Ernesto T. Jimenez Jr..jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Assoc. Prof. Ernesto T. Jimenez Jr.</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">In-Charge SOU</h5>
          </div>
        </div>
      </div>

    </div>

    <div class="row m-3">
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Staff - Mr. Cesar C. Moises.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Mr. Cesar C. Moises</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Staff</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Staff - Ms. Evangeline P. Nabor.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Evangeline P. Nabor</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Staff</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Staff - Ms. Janina S. Martin.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Janina S. Martin</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Staff</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/personnel/Staff - Ms. Karen R. Salenga.jpg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-3 text-center">Ms. Karen R. Salenga.jpg</h5>
          </div>
          <div class="card-footer">
            <h5 class="text-secondary mt-3 text-center">Staff</h5>
          </div>
        </div>
      </div>

    </div>

  </div>

  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">OSA ADMINISTRATION</p>
        <p class="tag-sub ">Meet all the Administrators and Staffs</p>
      </div>
    </div>
  </div>

  <div class="container mb-5">
    <div class="row row-cols-1 row-cols-md-5 g-4">
      <div class="col">
        <a href="Staff/all_staffs.php">
          <div class="card h-100 shadows border">
            <img src="assets/img/Under construction-bro.svg" class="card-img-top">
            <div class="card-body">
              <h5 class="card-name mt-5 text-center">Information Management and Publication Unit</h5>
            </div>
          </div>
        </a>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/Under construction-bro.svg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-5 text-center">Career Development and Employment Services Unit</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/Under construction-bro.svg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-5 text-center">Guidance Service Unit</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/Under construction-bro.svg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-5 text-center">Student Organization Unit</h5>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 shadows border">
          <img src="assets/img/Under construction-bro.svg" class="card-img-top">
          <div class="card-body">
            <h5 class="card-name mt-5 text-center">Student Organization Unit</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div> -->

  <!-- Add Personnel Modal -->
  <div class="modal fade" id="add_personnel" tabindex="-1" aria-labelledby="add_personnel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header bg-success text-white p-3">
            <h5 class="modal-name">Add New Personnel</h1>
              <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <img class="card-img-top movie_input_img" id="addimage" src="../img/Default_images.svg" alt="&nbsp" style="width: 100%; height: 30vh; object-fit: cover;">
              <label for="myfile">Image<span class="text-danger"> *</span></label>
              <input type="file" class="form-control mt-2" id="myfile" name="myfile" accept="image/*" onchange="loadFile(event)" required />
            </div>
            <div class="mb-3">
              <label for="name">Name<span class="text-danger"> *</span></label>
              <input type="text" name="name" class="form-control" placeholder="Enter Name of Personnel" required>
            </div>
            <div class="mb-3">
              <label for="position">Position<span class="text-danger"> *</span></label>
              <textarea class="form-control" id="mytextarea" name="position" placeholder="Enter Position of Personnel" required></textarea>
            </div>
          </div>
          <div class="modal-footer pt-4 ">
            <button type="button" class="btn" data-mdb-dismiss="modal">Cancel</button>
            <button type="submit" name="handle_submit" class="btn btn-success">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Archive Confirmation Modal -->
  <div class="modal fade" id="archive" tabindex="-1" aria-labelledby="archive" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form method="POST" action="">
          <div class="modal-header bg-danger text-white p-4">
            <h5 class="modal-name" id="exampleModalLabel"></h5>
            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body ">
            <i class="fas fa-circle-exclamation text-danger justify-content-center d-flex"></i>
            <div class="col content-modal mt-5">
              <h4 class="justify-content-center d-flex fw-semibold pt-3">Archive Record Confirmation</h4>
              <div class="form-group">
                <input type="hidden" class="form-control" placeholder="Enter id" id="archive_id_input" name="archive_id_input" required>
              </div>

              <p class="justify-content-center d-flex text-black-50 mt-3">Are you sure you want to archive this record? <span hidden id="arc"></p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn" data-mdb-dismiss="modal">Cancel</button>
            <button type="submit" name="archive" class="btn btn-danger">Archive</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- edit_personnel_info Modal -->
  <div class="modal fade" id="edit_personnel_info" tabindex="-1" aria-labelledby="edit_personnel_info" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header bg-success text-white p-3">
            <h5 class="modal-title">Update Personnel Info</h1>
              <i data-bs-dismiss="modal" aria-label="Close"></i>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <input type="hidden" name="per_id" id="per_id">
              <img class="card-img-top movie_input_img" id="output" alt="" style="width: 100%; height: 30vh; object-fit: cover;">
              <label for="perImg" class="mt-2">Image</label>
              <input type="file" class="form-control mt-2" id="perImg" name="perImg" accept="image/*" onchange="loadFiles(event)" />
            </div>
            <div class="mb-3">
              <label for="name">Name<span class="text-danger"> *</span></label>
              <input type="text" name="name" class="form-control" id="name" placeholder="Enter Personnel Name" required>
            </div>
            <div class="mb-3">
              <label for="positions">Position<span class="text-danger"> *</span></label>
              <textarea class="form-control" id="positions" name="positions" placeholder="Enter Position of Personnel" required></textarea>
            </div>
          </div>
          <div class="modal-footer pt-4 ">
            <button type="button" class="btn" data-mdb-dismiss="modal">Cancel</button>
            <button type="submit" name="handle_submit_update" class="btn btn-success">Update</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- Modal Login-->
  <div class="modal fade" id="login_Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0">
          <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="justify-content-center d-flex" style="height: 50px;">
            <img src="assets/img/logo.png" alt="login-logo" class="shadow rounded-circle">
          </div>
          <div class="py-2 justify-content-center d-flex">
            <h5>CLSU Account for OSA</h5>
          </div>
          <div class="text-center">
            <p>Log in with the credentials of your account to get more accurate view of office of student affairs.</p>
          </div>
          <form method="POST">
            <!-- Email input -->
            <div class="form-outline mb-3 mt-4">
              <input type="email" id="email" name="email" class="form-control" required />
              <label class="form-label" for="email">Email address</label>
            </div>
            <!-- Password input -->
            <div class="form-outline mb-2">
              <input type="password" id="password" name="password" class="form-control" required />
              <label class="form-label" for="password">Password</label>
            </div>
            <div class="mb-4 justify-content-end d-flex">
              <a href="forgot_pw/" class="text-muted">Forgot password?</a>
            </div>
            <!-- Submit button -->
            <button type="submit" name="submit" class="btn btn-dark btn-block shadow-0">Continue</button>
            <div class="pt-3 text-center">
              Don't have an account? <a href="register.php" class="text-success">Register Here</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="mt-5 footer-section ">
    <footer class="text-center text-lg-start bg-light text-muted " style="background-image: url(assets/img/banner1.png);  background-repeat: no-repeat; background-size: cover; ">
      <section class="">
        <div class="container-fluid text-md-start pt-3 px-5">
          <div class="row mt-3">
            <div class="col-md-3 col-lg-4 col-xl-4 mx-auto mb-4">
              <img src="assets/img/white-logo.png" alt="" class="footer-logo text-center" style="height: 88px;">
              <h4 class="text-white fw-semibold mt-2">OFFICE OF STUDENT AFFAIRS</h5>
                <p class="text-white fw-light">Science City of Muñoz, Nueva Ecija</p>
                <small class="text-white fw-light" style="font-size: 13px;">© Copyright 2023 Central Luzon State University All Rights Reserved</small>
            </div>
            <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
              <h5 class="text-uppercase fw-semibold mb-4 " style="color: #cdfb13;">Contact</h5>
              <p class="text-white"><i class="fas fa-location-dot "></i> Central Luzon State University, Science City of Muñoz Nueva Ecija, Philippines</p>
              <p class="text-white">
                <i class="fas fa-envelope me-3 "></i>
                osa@clsu.edu.ph
              </p>
              <p class="text-white"><i class="fas fa-phone me-3 "></i> (044) 940 7030</p>
            </div>
            <div class="col-auto mx-auto mb-4">
              <h5 class="text-uppercase fw-semibold mb-4" style="color: #cdfb13;">
                SOCIAL MEDIA
              </h5>
              <div>
                <a href="https://www.facebook.com/officeofstudentaffairsCLSU" target="_blank" class="me-3 text-reset">
                  <i class="fab fa-facebook-square fa-lg text-white"></i>
                </a>
                <a href="https://twitter.com/clsu_official?lang=en" target="_blank" class="me-3 text-reset">
                  <i class="fab fa-twitter fa-lg text-white"></i>
                </a>
                <a href="" class="me-3 text-reset">
                  <i class="fab fa-google fa-lg text-white"></i>
                </a>
                <a href="" class="me-3 text-reset">
                  <i class="fab fa-instagram fa-lg text-white"></i>
                </a>
                <a href="" class="me-3 text-reset">
                  <i class="fab fa-linkedin fa-lg text-white"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </footer>
  </div>


  <!-- <script type="text/javascript" src="assets/js/mdb.min.js"></script> -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="assets/js/sweetalert2.js"></script>
  <?php include 'assets/js/alert.php'  ?>

  <script>
    var loadFiles = function(event) {
      var image = document.getElementById('output');
      image.src = URL.createObjectURL(event.target.files[0]);
      image.setAttribute("class", "out");
    };
    var loadFile = function(event) {
      var image = document.getElementById('addimage');
      image.src = URL.createObjectURL(event.target.files[0]);
      image.setAttribute("class", "out");
    };

    //Edit modal for publication
    $('.editper_Btn').on('click', function() {
      let id = $(this).attr('data-id');

      // Get publication info for edit modal
      $.ajax({
        method: 'POST',
        url: 'personnel_details.php',
        data: {
          fetchPer: true,
          per_id: id
        },
        success: function(response) {
          if (response === 'no rows') {
            console.log("Error");
          } else {
            // let data = jQuery.parseJSON(response);
            let data = JSON.parse(response);
            let tmp = data.image
            let img = tmp.split('/')
            $('#per_id').val(data.id);
            // $('#output').prop('src', 'upload/img/' + img[2]);
            $('#output').prop('src', data.image);
            $('#name').val(data.name);
            $('#positions').val(data.position);
            // tinyMCE.get('mytextarea').setContent(data.descriptions);
            //console.log(img[2]);
          }
        }
      })
      //Show modal
      $('#edit_personnel_info').modal('show');
    })

    $('.arcper_Btn').on('click', function() {
      let id = $(this).attr('data-id');

      // Set the value of archive_id_input
      $('#archive_id_input').val(id);

      // Show modal
      $('#archive').modal('show');
    })

    // function getID(id) {
    //   let del_id = document.getElementById("del");
    //   del_id.innerText = id;
    //   document.getElementById("delete_id_input").value = id
    // }
  </script>

</body>

</html>