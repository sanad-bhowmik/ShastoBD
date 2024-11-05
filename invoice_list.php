<?php
include_once("include/header.php");

if (isset($_POST['submit'])) {
  $param = array();

  // Search by Invoice Number
  if (!empty($_POST['inv_num'])) {
    $inv_num = mysqli_real_escape_string($GLOBALS['con'], $_POST['inv_num']);
    $condition = "inv_num LIKE '%" . $inv_num . "%'";
    array_push($param, $condition);
  }

  // Search by Customer Name
  if (!empty($_POST['customer_name'])) {
    $customer_name = mysqli_real_escape_string($GLOBALS['con'], $_POST['customer_name']);
    $condition = "customer_name LIKE '%" . $customer_name . "%'";
    array_push($param, $condition);
  }

  // Search by Customer Phone
  if (!empty($_POST['customer_phone'])) {
    $customer_phone = mysqli_real_escape_string($GLOBALS['con'], $_POST['customer_phone']);
    $condition = "customer_phone LIKE '%" . $customer_phone . "%'";
    array_push($param, $condition);
  }

  // Search by Created Date (single date field)
  if (!empty($_POST['created_date'])) {
    $created_date = mysqli_real_escape_string($GLOBALS['con'], $_POST['created_date']);
    $condition = "DATE(created_at) = '" . $created_date . "'";
    array_push($param, $condition);
  }

  // Combine all conditions
  $condition = implode(" AND ", $param);
  $sql_get_data = "SELECT * FROM sale_info WHERE status = 1"; // Ensure to include status = 1
  if (!empty($condition)) {
    $sql_get_data .= " AND " . $condition;
  }
  $sql_get_data .= " ORDER BY created_at DESC";

  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
} else {
  // Default query without any filters
  $sql_get_data = "SELECT * FROM sale_info WHERE status = 1 ORDER BY created_at DESC;";
  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
}
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
            <div class="card-header">Search Sales</div>
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

                <!-- Created Date (single date field) -->
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
          <div class="card-header">Sales Information</div>
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
                    <th class="text-center">Action</th> <!-- New Action column -->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $i = 1;
                  while ($rs = mysqli_fetch_array($result)) { ?>
                    <tr>
                      <td class="text-center"><?php echo $i; ?></td>
                      <td class="text-center"><?php echo $rs['inv_num']; ?></td>
                      <td class="text-center"><?php echo $rs['customer_name']; ?></td>
                      <td class="text-center"><?php echo $rs['customer_phone']; ?></td>
                      <td class="text-center"><?php echo $rs['customer_address']; ?></td>
                      <td class="text-center"><?php echo $rs['totalPrice']; ?></td>
                      <td class="text-center"><?php echo $rs['discount']; ?></td>
                      <td class="text-center"><?php echo $rs['payable_price']; ?></td>
                      <td class="text-center">
                        <?php
                        // Format the created_at date
                        $date = new DateTime($rs['created_at']);
                        echo $date->format('d-m-Y'); // Output format: DD-MM-YYYY
                        ?>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-danger" onclick="confirmDrop('<?php echo $rs['inv_num']; ?>')">Drop</button>
                      </td>
                    </tr>
                  <?php
                    $i++;
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
    function confirmDrop(invNum) {
      if (confirm("Are you sure you want to drop this invoice?")) {
        $.ajax({
          url: 'drop_invoice.php', // The PHP file that will handle the drop request
          type: 'POST',
          data: {
            inv_num: invNum
          },
          success: function(response) {
            // Use Toastr for success notifications
            toastr.success(response, 'Success');
            location.reload(); // Reload the page to see updated data
          },
          error: function() {
            // Use Toastr for error notifications
            toastr.error("An error occurred while dropping the invoice.", 'Error');
          }
        });
      }
    }

    $(document).ready(function() {
      $('#clearBtn').click(function() {
        window.location.href = 'invoice_list.php'; // Clear the form by refreshing
      });
    });
  </script>
</body>

</html>