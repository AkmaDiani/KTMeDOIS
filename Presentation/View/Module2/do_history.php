<?php
require_once __DIR__ . "/../../../Data/db.php";
require_once __DIR__ . "/../../../Application/Controller/DOService.php";

session_start();

// Get database connection
$pdo = Database::getInstance()->getConnection();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
    header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
    exit;
}

// Use supplier_id (which is the same as vendor_id in your system)
$supplier_id = $_SESSION['vendor_id'] ?? null;

if (!$supplier_id) {
    die('Supplier ID not found. Please login again.');
}

$service = new DOService($pdo);

function filterByMonth($service, $supplier_id)
{
    $selected_month = $_GET["month"] ?? "";
    return $service->getDOHistory((int)$supplier_id, $selected_month);
}

function displayHistory($dos)
{
    if (count($dos) > 0) {
        foreach ($dos as $do) {
            $statusClass = str_replace(" ", "", $do["Status"]);
?>
            <tr>
                <td><?= htmlspecialchars($do["DO_number"]) ?></td>
                <td><?= htmlspecialchars($do["PO_number"]) ?></td>
                <td><?= htmlspecialchars($do["Supplier_name"]) ?></td>
                <td>
                    <span class="status <?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars($do["Status"]) ?>
                    </span>
                </td>
                <td><?= date("d/m/Y h:i A", strtotime($do["created_date"])) ?></td>
                <td>
                    <a class="btn" href="view_do.php?id=<?= urlencode($do["DO_id"]) ?>">View</a>
                </td>
            </tr>
        <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="6" style="text-align:center;">No delivery orders found.</td>
        </tr>
<?php
    }
}

$selected_month = $_GET["month"] ?? "";
$dos = filterByMonth($service, $supplier_id);
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Delivery Orders</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f7fb;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
        }

        h2 {
            color: #0057d9;
        }

        .filter-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: end;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 7px;
        }

        button,
        .btn {
            padding: 10px 15px;
            background: #0057d9;
            color: white;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-clear {
            background: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f1f4f9;
            text-align: left;
            padding: 12px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .status {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .Submitted {
            background: #e7f1ff;
            color: #0057d9;
        }

        .UnderReview {
            background: #fff3cd;
            color: #856404;
        }

        .Approved {
            background: #d4edda;
            color: #155724;
        }

        .Rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <?php include("../includes/sidebar.php"); ?>

    <div class="content"></div>

    <div class="container">
        <h2>My Delivery Orders</h2>

        <form method="GET" class="filter-box">
            <div>
                <label>Filter by Month</label><br>
                <input type="month" name="month" value="<?= htmlspecialchars($selected_month) ?>">
            </div>

            <button type="submit">Filter</button>
            <a href="do_history.php" class="btn btn-clear">Clear</a>
            <a href="submit_do.php" class="btn">+ Submit New DO</a>
        </form>

        <table>
            <tr>
                <th>DO Number</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Action</th>
            </tr>

            <?php displayHistory($dos); ?>
        </table>
    </div>
    </div>
</body>

</html>