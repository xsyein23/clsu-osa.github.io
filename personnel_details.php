
<?php 
    include 'backend/mysql_connect.php';

    if(isset($_POST['fetchPer']))
    {
        $per_id = $_POST['per_id'];
        $sql = "SELECT * FROM `personnel` WHERE id = $per_id";
        $res = mysqli_query($conn, $sql);

        if(mysqli_num_rows($res) > 0)
        {
            foreach($res as $row)
            {
                echo json_encode($row);
            }
        }
        else
        {
            echo "no rows";
        }
    }
?>

