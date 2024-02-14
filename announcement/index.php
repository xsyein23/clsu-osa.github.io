<?php
session_start();
include '../backend/mysql_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Fatal error: Maximum execution time of 120 seconds exceeded in C:\xampp\htdocs\OSA\announcement\index.php on line 3

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
      header("location:../announcement/");
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

//New announcement
if (isset($_POST["handle_submit"])) {

  require '../includes/PHPMailer.php';
  require '../includes/SMTP.php';
  require '../includes/Exception.php';

  function decryptEmail($encryptedEmail)
  {
    $ciphering = "AES-128-CTR";
    $option = 0;
    $decryption_iv = '1234567890123456';
    $decryption_key = "info";
    $decryptedEmail = openssl_decrypt($encryptedEmail, $ciphering, $decryption_key, $option, $decryption_iv);
    return $decryptedEmail;
  }

  $title = $_POST['title'];
  $titles = str_replace("'", "\'", $title);
  date_default_timezone_set("Asia/Manila");
  $date_created = date_create();
  $created_at = date_format($date_created, "Y-M-d h:i a");
  $description = stripslashes($_POST['description']);
  $descriptions = str_replace("'", "\'", $description);

  $temp = $_FILES['myfile']['tmp_name'];
  $imageDirectory = "../upload/";

  // Initialize an empty array to store uploaded image file paths
  $imagePaths = array();

  // Loop through each uploaded file
  foreach ($_FILES['myfile']['name'] as $key => $imageName) {
    $directory = $imageDirectory . $imageName;

    if (move_uploaded_file($temp[$key], $directory)) {
      $imagePaths[] = $directory;
    }
  }

  // Combine image paths into a single string separated by commas
  $imagePathsString = implode(',', $imagePaths);

  // Your existing SQL query for inserting announcement
  $sql = "INSERT INTO announcement SET 
            image='$imagePathsString',
            title = '$titles',
            date_created='$created_at',
            descriptions='$descriptions',
            is_archive=0;";

  // $_SESSION['status_success_added'] = "success";
  if (mysqli_query($conn, $sql)) {


    $_SESSION['status_success_added'] = "success";
    header("location:../announcement/");
    session_unset($_SESSION['status_success_added']);
  } else {
    echo mysqli_error($conn);
    echo '<script>';
    echo "alert('Error Occur!');" . mysqli_error($conn);
    echo '</script>';
  }


  $body = '  <body style="background-color: #fdfdfd">
                    <div class="fluid-container" style="padding: 5% 20% 10px">
                        <div class="card-box"
                            style="
                                display: block;
                                justify-content: center;
                                border: 1px solid #f5f5f5f5;
                                border-radius: 10px;
                            "
                        >
                            <div
                                class="img-card"
                                align="center"
                                style="width: 100%; margin-top: 30px"
                            >
                                <img
                                    src="https://i.imgur.com/DTcwEeE.png"
                                    alt="email_logo"
                                    style="height: 150px"
                                />
                            </div>
                            <div
                                class="card-body-container"
                                style="width: 84%; margin: 8% 0% 0% 8%"
                            >
                                <h1
                                    class="card-title-name"
                                    align="center"
                                    style="color: #006000"
                                >
                                    News post announcement
                                </h1>
                                <br />
                                <h3 class="card-text-content"></h3>
                                <p>
                                    Good day!
                                </p>
                                <p>Check out the latest news post ,' . $title . ', from the Office of Student Affairs.
                                </p>
                                <p>Thank You!</p>

                            </div>
                            <div
                                class="card-footer-name"
                                align="center"
                                style="
                                    background: #006000;
                                    color: #ffff;
                                    border-radius: 3px;
                                    padding: 10px;
                                "
                            >
                                <b>CLSU | Office of Student Affairs</b>
                            </div>
                        </div>
                    </div>
                </body>';
  //Create instance of PHPMailer
  $mail = new PHPMailer();
  //Set mailer to use smtp
  $mail->isSMTP();
  //Define smtp host
  $mail->Host = "smtp.gmail.com";
  //Enable smtp authentication
  $mail->SMTPAuth = true;
  //Set smtp encryption type (ssl/tls)
  $mail->SMTPSecure = "tls";
  //Port to connect smtp
  $mail->Port = "587";
  //Set gmail username
  $mail->Username = "Office of Student Affairs";
  //Set gmail password
  $mail->Password = "vxysdrlygvebegfg";
  //Email subject
  $mail->Subject = "Check out latest news post!";
  //Set sender email
  $mail->setFrom('noreply.clsu.osa@gmail.com');
  //Enable HTML
  $mail->isHTML(true);
  //Attachment
  // $mail->addAttachment('img/attachment.png');
  //Email body
  $mail->Body = $body;
  //Add recipient
  // Retrieve email addresses from the database
  $sql = "SELECT email FROM account"; // Replace 'your_table_name' with your actual table name
  $result = mysqli_query($conn, $sql);

  // Loop through the result set
  while ($row = mysqli_fetch_assoc($result)) {
    // Decrypt the email address using the decryptEmail function
    $decrypted_email = decryptEmail($row['email']);

    // Add recipient email address
    $mail->addAddress($decrypted_email);

    // Send email
    if ($mail->send()) {
      // Email sent successfully
      $_SESSION['status_success_send'] = "success";
    } else {
      // Error occurred while sending email
      echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }

    // Clear recipient email address for the next iteration
    $mail->clearAddresses();
  }

  // Close SMTP connection
  $mail->smtpClose();
}

if (isset($_POST["handle_submits"])) {

  require '../includes/PHPMailer.php';
  require '../includes/SMTP.php';
  require '../includes/Exception.php';

  function decryptEmail($encryptedEmail)
  {
    $ciphering = "AES-128-CTR";
    $option = 0;
    $decryption_iv = '1234567890123456';
    $decryption_key = "info";
    $decryptedEmail = openssl_decrypt($encryptedEmail, $ciphering, $decryption_key, $option, $decryption_iv);
    return $decryptedEmail;
  }

  $title = $_POST['title'];
  $titles = str_replace("'", "\'", $title);
  date_default_timezone_set("Asia/Manila");
  $date_created = date_create();
  $created_at = date_format($date_created, "Y-M-d h:i a");
  $description = stripslashes($_POST['description']);
  $descriptions = str_replace("'", "\'", $description);

  $date = date_create();
  $stamp = date_format($date, "Y");
  $temp = $_FILES['myfile']['tmp_name'];
  $directory = "../upload/" . $_FILES['myfile']['name'];

  if (move_uploaded_file($temp, $directory)) {
    $sql = "INSERT INTO announcement SET 
              image='$directory',
              title = '$titles',
              date_created='$created_at',
              descriptions='$descriptions',
              is_archive=0;";
    $_SESSION['status_success_added'] = "success";
    if (mysqli_query($conn, $sql)) {
      $_SESSION['status_success_added'] = "success";
      header("location:../announcement/");
      session_unset($_SESSION['status_success_added']);
    } else {
      echo mysqli_error($conn);
      echo '<script>';
      echo "alert('Error Occur!');" . mysqli_error($conn);
      echo '</script>';
    }
  }

  $body = '  <body style="background-color: #fdfdfd">
                    <div class="fluid-container" style="padding: 5% 20% 10px">
                        <div class="card-box"
                            style="
                                display: block;
                                justify-content: center;
                                border: 1px solid #f5f5f5f5;
                                border-radius: 10px;
                            "
                        >
                            <div
                                class="img-card"
                                align="center"
                                style="width: 100%; margin-top: 30px"
                            >
                                <img
                                    src="https://i.imgur.com/DTcwEeE.png"
                                    alt="email_logo"
                                    style="height: 150px"
                                />
                            </div>
                            <div
                                class="card-body-container"
                                style="width: 84%; margin: 8% 0% 0% 8%"
                            >
                                <h1
                                    class="card-title-name"
                                    align="center"
                                    style="color: #006000"
                                >
                                    News post announcement
                                </h1>
                                <br />
                                <h3 class="card-text-content"></h3>
                                <p>
                                    Good day!
                                </p>
                                <p>Check out the latest news post <b>' . $title . '</b> from the Office of Student Affairs.
                                </p>
                                <p>Thank You!</p>

                            </div>
                            <div
                                class="card-footer-name"
                                align="center"
                                style="
                                    background: #006000;
                                    color: #ffff;
                                    border-radius: 3px;
                                    padding: 10px;
                                "
                            >
                                <b>CLSU | Office of Student Affairs</b>
                            </div>
                        </div>
                    </div>
                </body>';
  //Create instance of PHPMailer
  $mail = new PHPMailer();
  //Set mailer to use smtp
  $mail->isSMTP();
  //Define smtp host
  $mail->Host = "smtp.gmail.com";
  //Enable smtp authentication
  $mail->SMTPAuth = true;
  //Set smtp encryption type (ssl/tls)
  $mail->SMTPSecure = "tls";
  //Port to connect smtp
  $mail->Port = "587";
  //Set gmail username
  $mail->Username = "noreply.clsu.osa@gmail.com";
  //Set gmail password
  $mail->Password = "vxysdrlygvebegfg";
  //Email subject
  $mail->Subject = "Check out latest news post!";
  //Set sender email
  $mail->setFrom('noreply.clsu.osa@gmail.com');
  //Enable HTML
  $mail->isHTML(true);
  //Attachment
  // $mail->addAttachment('img/attachment.png');
  //Email body
  $mail->Body = $body;
  //Add recipient
  // Retrieve email addresses from the database
  $sql = "SELECT email FROM account"; // Replace 'your_table_name' with your actual table name
  $result = mysqli_query($conn, $sql);

  // Loop through the result set
  while ($row = mysqli_fetch_assoc($result)) {
    // Decrypt the email address using the decryptEmail function
    $decrypted_email = decryptEmail($row['email']);

    // Add recipient email address
    $mail->addAddress($decrypted_email);

    // Send email
    if ($mail->send()) {
      // Email sent successfully
      $_SESSION['status_success_send'] = "success";
    } else {
      // Error occurred while sending email
      echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }

    // Clear recipient email address for the next iteration
    $mail->clearAddresses();
  }

  // Close SMTP connection
  $mail->smtpClose();
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
  <?php include '../embed/link.php'; ?>
</head>

<style>
  .card-body,
  .card-header {
    color: black;
  }
</style>

<body style="background-color: #fdfdfd">

  <!-- header -->
  <?php include '../embed/header.php'; ?>

  <div class="container pt-5">
    <div class="row">
      <div class="osa-tag">
        <p class="tag-info">NEWS</p>
        <p class="tag-sub">Access all news from the Office of Student Affairs (OSA)</p>
      </div>
    </div>
  </div>

  <div class="container d-flex justify-content-end mb-3">
    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
      echo '<button type="button" class="btn btn-primary shadows" data-mdb-toggle="modal" data-mdb-target="#add_announcement">
                <i class="fas fa-notes-medical"></i> Add news post
              </button>';
    }
    ?>
  </div>

  <div class="container">
    <div class="row row-cols-1 row-cols-md-2 g-4">
      <?php
      $sql = "SELECT * FROM announcement WHERE is_archive=0 ORDER BY date_created DESC";
      $res = mysqli_query($conn, $sql);
      if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) { ?>
          <div class="col">
            <a href="<?php echo '../announcement/details.php?announcement_id=' . $row['id']; ?>" class="card-text">

              <div class="card mb-3 shadows h-100 border">
                <div class="card-header">
                  <!-- <h6><?php echo $row['title']; ?></h6>
                  <small><?php echo $row['date_created']; ?></small> -->

                  <div class="row mt-3">
                    <div class="col ">
                      <h6><?php echo $row['title']; ?></h6>
                    </div>
                    <div class="col text-muted d-flex justify-content-end">
                      <p><?php echo $row['date_created']; ?></p>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <p>
                    <?php
                    $details = $row['descriptions'];
                    echo substr_replace($details, '...', 70);
                    ?>
                  </p>
                </div>
                <!-- <div class="card-footer justify-content-end d-flex border-0">
                <a href="<?php echo '../announcement/details.php?announcement_id=' . $row['id']; ?>" class="card-text">
                  <button class="btn btn-dark shadow-0"><i class="fas fa-eye"></i> View Details</button>
                </a>
              </div> -->
              </div>
            </a>
          </div>
        <?php }
      } else { ?>
        <div class="container p-2 justify-content-center d-flex mt-5">
          <h1 class="text-warning mt-5">No Data Found!</h1>
        </div>
      <?php } ?>
    </div>
  </div>


  <!-- Add Announcement Modal -->
  <div class="modal fade" id="add_announcement" tabindex="-1" aria-labelledby="add_announcement" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header bg-success text-white p-3">
            <h5 class="modal-title">Add News</h1>
              <i data-bs-dismiss="modal" aria-label="Close"></i>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <img class="card-img-top movie_input_img" id="output" src="../img/Default_images.svg" alt="&nbsp" style="width: 100%; height: 20vh; object-fit: cover;">
              <label for="myfile">Image<span class="text-danger"> *</span></label>
              <input type="file" class="form-control mt-2" id="myfile" name="myfile[]" accept="image/*" onchange="loadFile(event)" multiple required />
            </div>
            <div class="mb-3">
              <label for="title">News Title<span class="text-danger"> *</span></label>
              <input type="text" name="title" class="form-control" id="title" placeholder="Enter news title" required>
            </div>
            <div class="mb-3">
              <label for="description">Description<span class="text-danger"> *</span></label>
              <input type="text" class="form-control" id="mytextarea" name="description" placeholder="Enter news description">
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

  <?php include_once '../embed/footer.php' ?>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.js"></script>
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