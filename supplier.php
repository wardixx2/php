<?php

include_once 'connectdb.php';
session_start();

// Check if user is logged in and has the correct role
if (empty($_SESSION['useremail']) || $_SESSION['role'] == "User ") {
    header('location:../index.php');
    exit();
}

// Include the appropriate header based on user role
if ($_SESSION['role'] == "Admin") {
    include_once "header.php";
} else {
    include_once "headeruser.php";
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle deletion of supplier
$id = $_GET['id'] ?? null; // Use null coalescing operator to avoid undefined index notice

if (isset($id)) {
    $delete = $pdo->prepare("DELETE FROM tbl_supplier WHERE supid = :id");
    $delete->bindParam(':id', $id, PDO::PARAM_INT);

    if ($delete->execute()) {
        $_SESSION['status'] = "Account deleted successfully";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Account is not deleted";
        $_SESSION['status_code'] = "warning";
    }
}

// Handle supplier insertion
if (isset($_POST['btnsave'])) {
    $supid = $_POST['txtsupid'] ?? null; // Ensure this is set
    $supplier = $_POST['txtsupplier'] ?? null; // Corrected input name
    $supemail = $_POST['txtsupemail'] ?? null; // Corrected input name
    $contact = $_POST['txtcontact'] ?? null;

    // Validate input
    if (empty($supplier) || empty($supemail) || empty($contact)) {
        $_SESSION['status'] = "All fields are required.";
        $_SESSION['status_code'] = "warning";
    } else {
        // Check if email already exists
        $select = $pdo->prepare("SELECT * FROM tbl_supplier WHERE supemail = :supemail");
        $select->bindParam(':supemail', $supemail);
        $select->execute();

        if ($select->rowCount() > 0) {
            $_SESSION['status'] = "Email already exists. Create an account with a new email.";
            $_SESSION['status_code'] = "warning";
        } else {
            // Insert new supplier
            $insert = $pdo->prepare("INSERT INTO tbl_supplier (supid, supplier, supemail, contact) VALUES (:supid, :supplier, :supemail, :contact)");
            $insert->bindParam(':supid', $supid);
            $insert->bindParam(':supplier', $supplier);
            $insert->bindParam(':supemail', $supemail);
            $insert->bindParam(':contact', $contact);

            if ($insert->execute()) {
                $_SESSION['status'] = "Inserted successfully into the supplier";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "Error inserting into the supplier: " . implode(", ", $insert->errorInfo());
                $_SESSION['status_code'] = "error";
            }
        }
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Supplier</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- Breadcrumbs can be added here -->
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="m-0">Supplier</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Supplier</label>
                                    <input type="text" class="form-control" placeholder="Supplier Name" name="txtsupplier" required>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Supplier Email</label>
                                    <input type="email" class="form-control" placeholder="Supplier Email" name="txtsupemail" required>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Contact Number</label>
                                    <input type="text" class="form-control" placeholder="Contact Number" name="txtcontact" required>
                                </div>

                                <input type="hidden" name="txtsupid" value="<?php echo uniqid(); ?>"> <!-- Generate unique supplier ID -->
                                <button type="submit" name="btnsave" class="btn btn-primary">Save Supplier</button>
                            </form>
                        </div>

                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Supplier ID</th>
                                        <th>Supplier Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch and display suppliers
                                    $query = $pdo->query("SELECT * FROM tbl_supplier");
                                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>
                                                <td>{$row['supid']}</td>
                                                <td>{$row['supplier']}</td>
                                                <td>{$row['supemail']}</td>
                                                <td>{$row['contact']}</td>
                                                <td>
                                                    <a href='?id={$row['supid']}' class='btn btn-danger'>Delete</a>
                                                </td>
                                              </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once "footer.php";
?> 
<?php
if(isset($_SESSION['status']) && $_SESSION['status']!='')

{

?>
<script>

  Swal.fire({
    icon: '<?php echo $_SESSION['status_code'];?>',
    title: '<?php echo $_SESSION['status'];?>'
  });

  </script>
  <?php
  unset($_SESSION['status']);
}
?>