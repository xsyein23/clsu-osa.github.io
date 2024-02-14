<?php
session_start();
include '../backend/mysql_connect.php';

//Login 
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
      header("location:../announcement/details.php?announcement_id=" . $id);
      $_SESSION['status_success_admin'] = "success";
      session_unset($_SESSION['status_success_admin']);
    } else {
      $_SESSION['status_success_user'] = "success";
      header("location:../announcement/");
      session_unset($_SESSION['status_success_user']);
    }
  } else {
    $_SESSION['status_error'] = "error";
  }
}

//Get announcement info
if (isset($_GET['announcement_id'])) {
  $id = $_GET['announcement_id'];
  $sql = "SELECT title, date_created, descriptions, GROUP_CONCAT(image) AS image, is_archive FROM announcement WHERE id=" . $id;
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    // $announcement_details['image'] = explode(',', $announcement_details['image']);
    $announcement_details = mysqli_fetch_assoc($result);
  }
}

//Archive announcement
if (isset($_POST['archive'])) {

  $id = $_GET['announcement_id'];
  $archive = 1;

  $sql = "UPDATE announcement SET is_archive='$archive' WHERE id=" . $id;
  if (mysqli_query($conn, $sql)) {
    $_SESSION['status_success_archive'] = "success";
    header("refresh:2;url=../announcement/");
    // session_unset($_SESSION['status_success_archive']);
  } else {
    echo '<script language="javascript">';
    echo 'alert("error")';
    echo '</script>';
  }
}

// Upadate announcement
if (isset($_POST["handle_submit_update"])) {
  $id = $_GET['announcement_id'];
  $title = $_POST['title'];
  $titles = str_replace("'", "\'", $title);
  $date_created = date_create();
  $created_at = date_format($date_created, "Y-M-d");
  $description = stripslashes($_POST['description']);
  $descriptions = str_replace("'", "\'", $description);

  $date = date_create();
  $stamp = date_format($date, "Y");

  if (file_exists($_FILES['myfile']['tmp_name'])) {
    $temp = $_FILES['myfile']['tmp_name'];
    $directory = "../upload/" . $_FILES['myfile']['name'];

    if (move_uploaded_file($temp, $directory)) {
      $sql = "UPDATE announcement SET 
                image='$directory',
                title = '$titles',
                date_created='$created_at',
                descriptions='$descriptions',
                is_archive=0
                WHERE id=" . $id;

      if (mysqli_query($conn, $sql)) {
        $_SESSION['status_success_update'] = "success";
        header("location:../announcement/details.php?announcement_id=" . $id);
        unlink("../upload/" . $announcement_details['image']);
        unset($_POST['handle_submit_update']);
        session_unset($_SESSION['status_success_update']);
      } else {
        echo mysqli_error($conn);
        echo '<script>';
        echo "alert('Error Occur!');" . mysqli_error($conn);
        echo '</script>';
      }
    }
  } else {
    $sql = "UPDATE announcement SET 
                title = '$titles',
                date_created='$created_at',
                descriptions='$descriptions',
                is_archive=0
                WHERE id=" . $id;

    if (mysqli_query($conn, $sql)) {
      $_SESSION['status_success_update'] = "success";
      header("location:../announcement/details.php?announcement_id=" . $id);
      unlink("../upload/" . $announcement_details['image']);
      unset($_POST['handle_submit_update']);
      session_unset($_SESSION['status_success_update']);
    } else {
      echo mysqli_error($conn);
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
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Office of Student Affairs</title>
  <link rel="icon" href="../assets/img/logo.png" class="icon">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/mdb.min.css" />
</head>
<style>
  .fa-circle-exclamation {
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

  .image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    /* Adjust minmax values as needed */
    gap: 10px;
    /* Adjust gap between images */
  }

  .image-item {
    overflow: hidden;
  }
</style>

<body style="background-color: #fdfdfd">

  <!-- header -->
  <?php include '../embed/header.php'; ?>

  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">NEWS DETAILS</p>
        <p class="tag-sub">Here are the detail of the news provided by the Office of Student Affairs (OSA)</p>
      </div>
    </div>
  </div>

  <div class="container p-2 mt-2">
    <div class="row">
      <?php
      // Assuming $announcement_details contains the fetched row from the announcement table
      $imagePaths = explode(",", $announcement_details['image']);
      ?>

      <div class="image-grid">
        <?php foreach ($imagePaths as $imagePath) : ?>
          <div class="image-item">
            <img src="../upload/<?php echo trim($imagePath); ?>" class="img-fluid rounded shadows border" alt="" style="object-fit: cover;" />
          </div>
        <?php endforeach; ?>
      </div>


    </div>
    <div class="col">
      <div class="card-body">
        <div class="row mt-3">
          <div class="col ">
            <h5><?php echo $announcement_details['title']; ?></h5>
          </div>
          <div class="col text-muted d-flex justify-content-end">
            <p><?php echo $announcement_details['date_created']; ?></p>
          </div>
        </div>
        <p class="card-text description-left-border mt-5">
          <?php echo $announcement_details['descriptions']; ?>
        </p>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col">
        <?php
        if (isset($_SESSION['role'])) {
          if ($_SESSION['role'] == 1) {
            echo '
                    <button class="btn btn-success shadows" data-mdb-toggle="modal" data-mdb-target="#update_announcement"><i class="fas fa-pen-to-square"></i> Update</button>
                    <button class="btn btn-danger shadows" data-mdb-toggle="modal" data-mdb-target="#archive"><i class="fas fa-box-archive"></i> Archive</button>';
          }
        } else {
          echo '';
        }
        ?>
      </div>
    </div>
  </div>
  <div class="container d-flex justify-content-center">
    <a href="../announcement/" class="btn btn-dark shadows">View More News</a>
  </div>

  <!-- archive modal -->
  <div class="modal fade" id="archive" tabindex="-1" role="dialog" aria-labelledby="archive" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white p-4">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body ">
            <i class="fas fa-circle-exclamation text-danger justify-content-center d-flex"></i>
            <div class="col content-modal mt-5">
              <h4 class="justify-content-center d-flex fw-semibold pt-3">Archive Announcement</h4>
              <p class="justify-content-center d-flex text-black-50 mt-3">Are you sure you want to archive this announcement?</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn" data-mdb-dismiss="modal">
              Cancel
            </button>

            <button type="submit" name="archive" class="btn btn-danger px-4">
              archive
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Update Announcement -->
  <div class="modal fade" id="update_announcement" tabindex="-1" aria-labelledby="update_announcement" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header bg-success text-white p-3">
            <h5 class="modal-title">Update Announcement</h1>
              <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <div class="mb-3">
              <label for="myfile">Image<span class="text-danger"> *</span></label>
              <img class="card-img-top" id="output" src="../upload/<?php echo $announcement_details['image']; ?>" alt="Card image" style="width: 100%; height: 40vh; object-fit: cover;">
              <input type="file" class="form-control mt-2" id="myfile" name="myfile[]" accept="image/*" onchange="loadFile(event)" multiple />
            </div>
            <div class="mb-3">
              <label for="title">Announcement Title<span class="text-danger"> *</span></label>
              <input value="<?php echo $announcement_details['title']; ?>" type="text" name="title" class="form-control" id="title" placeholder="Enter Name of Location" required>
            </div>
            <div class="mb-3">
              <label for="description">Description<span class="text-danger"> *</span></label>
              <textarea class="form-control" id="mytextarea" name="description"><?php echo $announcement_details['descriptions']; ?></textarea>
            </div>
          </div>
          <!-- <div class="modal-footer pt-4 ">
            <button type="submit" name="handle_submit_update" class="btn mx-auto w-100 btn-success fw-semibold">Submit</button>
          </div> -->
          <div class="modal-footer pt-4 ">
            <button type="button" class="btn" data-mdb-dismiss="modal">Cancel</button>
            <button type="submit" name="handle_submit_update" class="btn btn-success">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include_once '../embed/footer.php' ?>

  <!-- MDB -->
  <script type="text/javascript" src="../assets/js/mdb.min.js"></script>
  <!-- Custom scripts -->
  <script type="text/javascript"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>

  <!-- Display preview image function -->
  <script>
    var loadFile = function(event) {
      var image = document.getElementById('output');
      image.src = URL.createObjectURL(event.target.files[0]);
      image.setAttribute("class", "out");
    };
  </script>

  <!-- tiny mce function -->
  <script>
    tinymce.init({
      selector: "#mytextarea"
    });
  </script>


</body>

</html>
