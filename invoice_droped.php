<?php
include_once("include/header.php");

// Initialize parameters for filters
$param = array();
$sql_get_dropped_data = "SELECT * FROM drop_invoice WHERE status = 0"; // Base query

// Filter by Invoice Number
if (isset($_POST['submit'])) {
    if (!empty($_POST['inv_num'])) {
        $inv_num = mysqli_real_escape_string($GLOBALS['con'], $_POST['inv_num']);
        $condition = "inv_num LIKE '%" . $inv_num . "%'";
        array_push($param, $condition);
    }

    // Filter by Customer Name
    if (!empty($_POST['customer_name'])) {
        $customer_name = mysqli_real_escape_string($GLOBALS['con'], $_POST['customer_name']);
        $condition = "customer_name LIKE '%" . $customer_name . "%'";
        array_push($param, $condition);
    }

    // Filter by Customer Phone
    if (!empty($_POST['customer_phone'])) {
        $customer_phone = mysqli_real_escape_string($GLOBALS['con'], $_POST['customer_phone']);
        $condition = "customer_phone LIKE '%" . $customer_phone . "%'";
        array_push($param, $condition);
    }

    // Filter by Created Date
    if (!empty($_POST['created_date'])) {
        $created_date = mysqli_real_escape_string($GLOBALS['con'], $_POST['created_date']);
        $condition = "DATE(created_at) = '" . $created_date . "'";
        array_push($param, $condition);
    }

    // Combine conditions
    if (!empty($param)) {
        $sql_get_dropped_data .= " AND " . implode(" AND ", $param);
    }
}

$sql_get_dropped_data .= " ORDER BY created_at DESC;";
$result_dropped = mysqli_query($GLOBALS['con'], $sql_get_dropped_data);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <div class="app-main__inner">
        <form id="search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Search Dropped Invoices</div>
                        <div class="card-body">
                            <div class="position-relative row form-group">

                                <!-- Invoice Number -->
                                <div class="col-sm-3">
                                    <p class="text">Invoice Number:</p>
                                    <input autocomplete="off" type="text" name="inv_num" value="<?php if (isset($_POST['inv_num'])) echo $_POST['inv_num']; ?>" class="form-control" placeholder="Enter Invoice Number">
                                </div>

                                <!-- Customer Name -->
                                <div class="col-sm-3">
                                    <p class="text">Customer Name:</p>
                                    <input autocomplete="off" type="text" name="customer_name" value="<?php if (isset($_POST['customer_name'])) echo $_POST['customer_name']; ?>" class="form-control" placeholder="Enter Customer Name">
                                </div>

                                <!-- Customer Phone -->
                                <div class="col-sm-3">
                                    <p class="text">Customer Phone:</p>
                                    <input autocomplete="off" type="text" name="customer_phone" value="<?php if (isset($_POST['customer_phone'])) echo $_POST['customer_phone']; ?>" class="form-control" placeholder="Enter Customer Phone">
                                </div>

                                <!-- Created Date -->
                                <div class="col-sm-3">
                                    <p class="text">Date:</p>
                                    <input class="form-control" type="date" name="created_date" value="<?php if (isset($_POST['created_date'])) echo $_POST['created_date']; ?>">
                                </div>

                            </div>
                            <div class="position-relative row form-group p-t-10">
                                <div class="col-sm-4">
                                    <input type="submit" value="Search" name="submit" class="btn btn-secondary">
                                    <button type="button" class="btn btn-info" id="clearBtn">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">Dropped Invoices</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Invoice Number</th>
                                        <th class="text-center">Customer Name</th>
                                        <th class="text-center">Customer Phone</th>
                                        <th class="text-center">Customer Address</th>
                                        <th class="text-center">Total Price</th>
                                        <th class="text-center">Discount</th>
                                        <th class="text-center">Payable Price</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th> <!-- New Status column -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $j = 1;
                                    while ($dropped = mysqli_fetch_array($result_dropped)) { ?>
                                        <tr>
                                            <td class="text-center"><?php echo $j; ?></td>
                                            <td class="text-center"><?php echo $dropped['inv_num']; ?></td>
                                            <td class="text-center"><?php echo $dropped['customer_name']; ?></td>
                                            <td class="text-center"><?php echo $dropped['customer_phone']; ?></td>
                                            <td class="text-center"><?php echo $dropped['customer_address']; ?></td>
                                            <td class="text-center"><?php echo $dropped['totalPrice']; ?></td>
                                            <td class="text-center"><?php echo $dropped['discount']; ?></td>
                                            <td class="text-center"><?php echo $dropped['payable_price']; ?></td>
                                            <td class="text-center">
                                                <?php
                                                // Format the created_at date
                                                $date = new DateTime($dropped['created_at']);
                                                echo $date->format('d-m-Y'); // Output format: DD-MM-YYYY
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-danger" style="font-weight: 700;">Dropped</span>
                                            </td>
                                        </tr>
                                    <?php
                                        $j++;
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#clearBtn').click(function() {
                // Clear the form by refreshing
                window.location.href = 'invoice_droped.php'; // Adjust this to the appropriate page if necessary
            });
        });
    </script>
</body>

</html>