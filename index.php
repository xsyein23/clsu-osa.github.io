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
      header("location:index.php");
      $_SESSION['status_success_admin'] = "success";
      session_unset($_SESSION['status_success_admin']);
    } else {
      $_SESSION['status_success_user'] = "success";
      header("location:index.php");
      session_unset($_SESSION['status_success_user']);
    }
  } else {
    $_SESSION['status_error'] = "error";
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/mdb.min.css" />
  <link rel="stylesheet" href="assets/css/index.css" />
</head>

<style>
  /* .carousel-item{
    height: 80vh;
  }  */

  .carousel-item {
    /* width: 50%; */
    /* object-fit: contain; */
    height: 500px;
    /* width : 1000px; */
  }

  a,
  a:hover,
  a:focus,
  a:active {
    text-decoration: none;
    color: inherit;
  }

  .carousel-caption {
    text-shadow: -1px 1px 2px #000,
      1px 1px 6px #000,
      1px -1px 0 #000,
      -1px -1px 0 #000;
  }

  .carousel-inner {
    position: relative;
  }

  .carousel-item::before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.3));
    /* background: rgb(215, 214, 236);
    background: linear-gradient(90deg, rgba(215, 214, 236, 0.03405112044817926) 0%, rgba(0, 255, 81, 0.700717787114846) 100%, rgba(0, 255, 81, 0.03125) 100%); */
    /* Adjust the gradient colors and transparency as needed */
    z-index: 1;
  }

  .carousel-caption {
    z-index: 2;
    /* Ensure the caption is above the overlay */
    color: #fff;
    /* Set the text color for better readability on the overlay */
  }
</style>

<body style="background-color: #fdfdfd">

  <div class="logo-header ">
    <div class="container-fluid">
      <div class="row d-flex justify-content-between">
        <div class="logo-header-left col-xl-7 col-md-7 col-xs-7 dp-xs-flex flex-row">
          <div class="logo mr-xs-3">
            <img src="assets/img/clsu-logo.png" alt="CLSU_LOGO">
          </div>
          <div class="logo-text m-xs-0">
            <span class="logo-title">Central Luzon State University</span>
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

          <!-- Button trigger modal -->
          <!-- <?php
                if (isset($_SESSION['role'])) {
                  if ($_SESSION['role'] == 0) {
                    echo '<li class="nav-item me-2">
                        <a href=""class="nav-link text-white" data-mdb-toggle="modal" data-mdb-target="#chatModal">
                            FILE A COMPLAINT
                        </a>
                        </li>';
                  }
                }
                ?> -->

          <?php

          // Archive link
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

            echo "  <li class='nav-item-out'>
                                <span class='nav-link text-white'>
                                    </span>
                            </li>";
            echo '<li class="nav-item-out">
                              <div class="btn-group shadow-0">
                              <a type="button" class="link text-white ps-3 dropdown-toggle" data-mdb-toggle="dropdown" aria-expanded="false">
                              ' . $_SESSION['fullname'] . " | " . $role . '
                              </a>
                              <ul class="dropdown-menu">
                              <form action="backend/manage_profile.php" method="POST">
                                      <li><button class="dropdown-item rounded-5" name="logout">Profile</button></li>
                                  </form>
                                  <form action="backend/logout.php" method="POST">
                                      <li><button class="dropdown-item rounded-5" name="logout">Logout</button></li>
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
                      </li>';
        }
        ?>
      </div>
    </div>
  </nav>

  <div class="carousel-section d-none d-sm-block">
    <div id="myCarousel" class="carousel slide" data-mdb-ride="carousel">
      <div class="carousel-inner">
        <!-- <div class="carousel-item active">
          <img src="assets/img/banner1.png" class="d-block w-100" alt="Wild Landscape" />
          <div class="carousel-caption">
            <h1>OFFICE OF STUDENT AFFAIRS</h1>
            <p>The Office for Student Affairs takes charge of the campus life of the students,
              their welfare and discipline, and dormitory facilities. As such, it guides and
              supervises the recognized student organizations, the student councils, the
              COMELECs; and conducts capability-building seminars for the organization
              advisers. The OSA looks into all student-initiated and student-related
              activities.
            </p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/img/banner2.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>CLSU MENTAL HEALTH PROVIDERS</h1>
            <p>The Guidance Services Unit of OSA is providing online and tele counseling services for all CLSU students. Counselors and mental health professionals can be reached by students through their Messenger account and mobile numbers.
            </p>
          </div>
        </div> -->
        <div class="carousel-item active">
          <img src="assets/img/carousel/1.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>OSA SPECTRUM</h1>
            <p>The IMPU instigates the publication of the OSA Spectrum, OSA’s official newspaper, featuring the services, programs and activities offered and provided by the OSA for the students. The OSA Spectrum gets published biannually. There is an OSA Editorial Staff who regularly contributes articles for the OSA Spectrum and members undergo training on news and feature writing to update their writing skills.
            </p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/img/carousel/2.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>Student Leaders in an International and National Arena</h1>
            <p>The Office of Student Affairs through the Student Organizations Unit sends student delegates to local, regional, national and even international conferences, trainings and workshops to enhance their leadership abilities. Interested students are selected through interviews and on the basis of their performance as student leaders.</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/img/carousel/3.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>Transform! Young Leaders’ Convention</h1>
            <p>Empowering the Filipino youth leadership and participation in local and national communities, 18 delegates from the different student formations represented Central Luzon State University (CLSU) in the 10th Philippines I Transform! Young Leaders’ Convention (PITYLC) held at Teachers’ Camp, Baguio City last August 17 to 22, 2023. Anchored from its theme: “A Decade of Action: Leading Amid COVID and the Better Normal,” the convention organized by Youthlead Philippines focused on the incorporation of Sustainable Development Goals (SDG) in the service of young leaders.
            </p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/img/carousel/4.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PGTA, CDESU Programs
            </h1>
            <p>To prepare students as part of the workforce of the country, the Career Development and Employment Services Unit (CDESU) facilitates a four-part Career Development Seminar at the CLSU Auditorium, November 9 and 15, 2023. The seminar is designed to cater the career and employment needs of CLSU students and graduates to make them more globally competitive and productive. Moreover, The Office of Student Affairs (OSA) organized the Parents, Guardians, and Teachers’ Association (PGTA) General Assembly at the CLSU Auditorium on August 12, 2023.
              PGTA reports on budget allocation, accomplishments, and projects including the renovation of OSA Tambayan were delivered by Mrs. Jovita Fajardo, PGTA President, and Asst. Prof. Alexis Ramirez, PGTA Auditor.
            </p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/img/carousel/5.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>RACE Against Suicide</h1>
            <p>RACE Against Suicide is a program under the Guidance Service Unit that aims to equip the teachers with the knowledge, skills and attitude that they need to properly respond to our students’ mental health concerns, debunk myths about suicide, and empower them to be an effective ‘gatekeeper’ through appropriate identification, management and referral of learners-at-risk.</p>
          </div>
        </div>
        <div class="carousel-item">
          <img src="assets/img/carousel/6.png" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>Mental Health Awareness Seminar
            </h1>
            <p>In celebration of Mental Health Awareness Month, Central Luzon State University (CLSU) through the Guidance Services Unit of the Office of Student Affairs spearheads a Seminar on Mental Health Awareness with the theme “Mental Health is a Universal Human Right,” at CLSU Auditorium, October 26. Dr. Brian Limson of the Philippine Normal University serves as Resource Speaker which covers topics such as Understanding Stress, Self-Care and how people’s physical routine affects the mental health of students and faculty. Around 2200 students from different colleges of the university attended the lecture.
            </p>
          </div>
        </div>

        <!-- <div class="carousel-item">
          <img src="assets/img/carousel/1.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/2.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/3.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/4.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/5.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/6.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/7.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/8.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/9.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/10.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div>

        <div class="carousel-item">
          <img src="assets/img/carousel/11.jpg" class="d-block w-100" alt="Camera" />
          <div class="carousel-caption">
            <h1>PLACEHOLDER</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
          </div>
        </div> -->


      </div>

      <button class="carousel-control-prev" type="button" data-mdb-target="#myCarousel" data-mdb-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-mdb-target="#myCarousel" data-mdb-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">NEWS</p>
        <p class="tag-sub ">Stay updated with the latest news from the Office of Student Affairs (OSA)</p>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="col justify-content-end d-flex mb-3">
      <a href="announcement/">
        <button type="button" class="btn fw-semibold shadows" data-mdb-ripple-color="dark">View All <i class="fas fa-angle-right"></i></button>
      </a>
    </div>
  </div>

  <!-- <div class="container">
    <?php
    $sql = "SELECT * FROM announcement WHERE is_archive=0 limit 4";
    $res = mysqli_query($conn, $sql); ?>
    <?php if (mysqli_num_rows($res) > 0) : ?>
      <?php while ($row = mysqli_fetch_assoc($res)) : ?>
        <div class="row g-0">
          <div class="card mb-3 shadows border">
            <div class="card-header">
              <div class="row">
                <div class="col">
                  <h6><?php echo $row['title']; ?></h6>
                </div>
                <div class="col justify-content-end d-flex">
                  <small><?php echo $row['date_created']; ?></small>
                </div>
              </div>
            </div>
            <div class="card-body">
              <p>
                <?php
                $details = substr($row['descriptions'], 0, 350);
                if ($details > 350) {
                  echo $details . "...";
                }
                ?>
              </p>
            </div>
            <div class="card-footer border-0 justify-content-end d-flex">
              <a href="<?php echo 'announcement/details.php?announcement_id=' . $row['id']; ?>" class="card-text">
                <button class="btn btn-dark shadow-0"><i class="fas fa-eye"></i> View Details</button>
              </a>
            </div>
          </div>
        </div>
      <?php endwhile ?>
    <?php else : ?>
      <div class="container p-2 justify-content-center d-flex">
        <h1 class="text-warning">No Data Found!</h1>
      </div>
    <?php endif ?>
  </div> -->

  <!-- <div class="container">
    <?php
    $sql = "SELECT * FROM announcement WHERE is_archive=0";
    $res = mysqli_query($conn, $sql);
    if (mysqli_num_rows($res) > 0) {
      while ($row = mysqli_fetch_assoc($res)) { ?>
        <div class="row g-0">
          <div class="card mb-3 shadows h-100 border">
            <div class="card-header">
              <div class="row">
                <div class="col">
                  <h6><?php echo $row['title']; ?></h6>
                </div>
                <div class="col justify-content-end d-flex">
                  <small><?php echo $row['date_created']; ?></small>
                </div>
              </div>
            </div>
            <div class="card-body">
              <p>
                <?php
                $details = $row['descriptions'];
                echo substr_replace($details, '...', 100);
                ?>
              </p>
            </div>
            <div class="card-footer border-0 justify-content-end d-flex">
              <a href="<?php echo 'announcement/details.php?announcement_id=' . $row['id']; ?>" class="card-text">
                <button class="btn btn-dark shadow-0"><i class="fas fa-eye"></i> View Details</button>
              </a>
            </div>
          </div>
        </div>
      <?php }
    } else { ?>
      <div class="container p-2 justify-content-center d-flex mt-5">
        <h1 class="text-warning mt-5">No Data Found!</h1>
      </div>
    <?php } ?>
  </div> -->

  <div class="container mt-4">
    <div class="row row-cols-1  g-4">
      <?php
      $sql = "SELECT * FROM announcement WHERE is_archive=0 ORDER BY date_created DESC LIMIT 3";
      $res = mysqli_query($conn, $sql);

      if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
          // Use basename() to get the filename from the image path
          $filename = basename($row['image']);
      ?>
          <div class="col cols">
            <a href="announcement/details.php?announcement_id=<?php echo $row['id']; ?>" class="card-link">
              <div class="card h-100 shadows border">
                <div class="card-header">
                  <div class="row">
                    <div class="col">
                      <h6><?php echo $row['title']; ?></h6>
                    </div>
                    <div class="col justify-content-end d-flex">
                      <small><?php echo $row['date_created']; ?></small>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <?php
                  $details = $row['descriptions'];
                  echo substr_replace($details, '...', 600);
                  ?>
                </div>
              </div>
            </a>
          </div>
        <?php
        }
      } else { ?>
        <div class="container p-2 justify-content-center d-flex">
          <h1 class="text-warning">No Data Found!</h1>
        </div>
      <?php  }
      ?>
    </div>
  </div>

  <!-- Login Modal -->
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
  <div class="mt-5 footer-section">
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
  <script>
    /* document.addEventListener('contextmenu', (e) => e.preventDefault());

    function ctrlShiftKey(e, keyCode) {
    return e.ctrlKey && e.shiftKey && e.keyCode === keyCode.charCodeAt(0);
    }

    document.onkeydown = (e) => {
    if (
        event.keyCode === 123 ||
        ctrlShiftKey(e, 'I') ||
        ctrlShiftKey(e, 'J') ||
        ctrlShiftKey(e, 'C') ||
        (e.ctrlKey && e.keyCode === 'U'.charCodeAt(0))
    )
        return false;
    };*/
  </script>
  <script src="assets/js/sweetalert2.js"></script>
  <?php
  if (isset($_SESSION['status_success_admin'])) { ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      Toast.fire({
        icon: 'success',
        title: 'Welcome Back Admin!'
      })
    </script>
  <?php
    unset($_SESSION['status_success_admin']);
  }

  if (isset($_SESSION['status_success_user'])) { ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      Toast.fire({
        icon: 'success',
        title: 'Welcome <?php echo $_SESSION['fullname'] ?>!'
      })
    </script>
  <?php
    unset($_SESSION['status_success_user']);
  }

  if (isset($_SESSION['status_error'])) {
  ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      Toast.fire({
        icon: 'error',
        title: 'Credentials error'
      })
    </script>
  <?php

  }
  ?>

  <!-- MDB -->
  <script type="text/javascript" src="assets/js/mdb.min.js"></script>
  <!-- Custom scripts -->
  <script type="text/javascript"></script>
</body>

</html>