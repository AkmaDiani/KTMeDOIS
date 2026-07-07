<?php
require_once __DIR__ . '/../../bootstrap.php';

session_start();

$supplier_id = $_SESSION['supplier_id'];

$do_id = $_GET["id"] ?? null;

$service = new DOService($pdo);
$result = $service->getDODetails($do_id, (int)$supplier_id);

if (!$result) {
    die("Delivery Order not found or access denied.");
}

$do = $result["do"];
$items = $result["items"];
?>

<!DOCTYPE html>
<html>

<head>
    <title>DO Submission Detail</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f7fb;
            padding: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0 2px 8px #ccc;
        }

        h2 {
            color: #0057d9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #f1f4f9;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #0057d9;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 10px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <?php include("../SharedUI/sidebarM2.php"); ?>

    <div class="content"></div>

    <div class="card">
        <h2>Delivery Order Submission Detail</h2>

        <p><b>DO Number:</b> <?= htmlspecialchars($do["DO_number"]) ?></p>
        <p><b>PO Number:</b> <?= htmlspecialchars($do["PO_number"]) ?></p>
        <p><b>Supplier:</b> <?= htmlspecialchars($do["Supplier_name"]) ?></p>
        <p><b>Status:</b> <?= htmlspecialchars($do["Status"]) ?></p>
        <p><b>Created Date:</b> <?= htmlspecialchars($do["created_date"]) ?></p>

        <h3>Submitted Files</h3>

        <a class="btn" target="_blank" href="../../Application/Controllers/view_file.php?id=<?= urlencode($do_id) ?>&type=do">
            View DO File
        </a>

        <a class="btn" target="_blank" href="../../Application/Controllers/view_file.php?id=<?= urlencode($do_id) ?>&type=proof">
            View Proof File
        </a>

        <h3>Item Details</h3>

        <table>
            <tr>
                <th>Item No</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>

            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["item_no"]) ?></td>
                    <td><?= htmlspecialchars($item["item_description"]) ?></td>
                    <td><?= htmlspecialchars($item["quantity"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <a class="btn" href="submit_do.php">Submit Another DO</a>
        <a class="btn" href="do_history.php">Back to My DOs</a>
    </div>
    </div>
</body>

</html>