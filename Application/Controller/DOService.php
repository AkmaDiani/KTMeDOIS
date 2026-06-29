<?php
require_once __DIR__ . "/../Model/DeliveryOrder.php";
require_once __DIR__ . "/../Model/Item.php";
require_once __DIR__ . "/../Model/Supplier.php";
require_once __DIR__ . "/../Model/Notification.php";
require_once __DIR__ . "/../Model/PO.php";

class DOService
{
    private $pdo;
    private $doModel;
    private $itemModel;
    private $supplierModel;
    private $notificationModel;
    private $poModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->doModel = new DeliveryOrder($pdo);
        $this->itemModel = new Item($pdo);
        $this->supplierModel = new Supplier($pdo);
        $this->notificationModel = new Notification($pdo);
        $this->poModel = new PO($pdo);
    }

    private function validateFile($file)
    {
        $allowedTypes = [
            "application/pdf",
            "image/jpeg",
            "image/png"
        ];

        if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed.");
        }

        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("File size must not exceed 10MB.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes, true)) {
            throw new Exception("Only PDF, JPG, JPEG, and PNG files are allowed.");
        }

        return file_get_contents($file["tmp_name"]);
    }

    public function submitDO($post, $files, $supplier_id)
    {
        $this->pdo->beginTransaction();

        try {
            $po_number = trim($post["po_number"] ?? "");

            $staff_id = null;


            if ($supplier_id <= 0 || empty($po_number)) {
                throw new Exception("Supplier and PO Number are required.");
            }

            if (
                empty($post["item_no"]) ||
                empty($post["item_description"]) ||
                empty($post["quantity"])
            ) {
                throw new Exception("At least one item is required.");
            }

            $supplier = $this->supplierModel->getSupplierById($supplier_id);

            if (!$supplier) {
                throw new Exception("Supplier not found.");
            }

            if (strtolower($supplier["status"]) !== "active") {
                throw new Exception("Inactive supplier cannot submit Delivery Order.");
            }

            $doFile = $this->validateFile($files["do_file"]);
            $proofFile = $this->validateFile($files["proof_file"]);

            if ($this->poModel->poNumberExists($po_number)) {

                if ($this->doModel->poHasActiveSubmission($po_number)) {
                    throw new Exception("This PO Number has already been submitted and is currently Under Review or Approved.");
                }

                $status = "Under Review";
                $reason = "-";
            } else {
                $status = "Rejected";
                $reason = "Invalid PO Number";
            }

            $do_id = $this->doModel->getNextDOId();
            $do_number = "DO-2026-" . str_pad($do_id - 40000, 4, "0", STR_PAD_LEFT);

            $this->doModel->createDO([
                "DO_id" => $do_id,
                "DO_number" => $do_number,
                "PO_number" => $po_number,
                "supplier_ID" => $supplier_id,
                "Staff_id" => $staff_id,
                "DO_link" => $doFile,
                "Proof_link" => $proofFile,
                "Status" => $status,
                "Reason" => $reason,
                "created_by" => $supplier["Supplier_name"]
            ]);

            $itemNosInThisForm = [];

            for ($i = 0; $i < count($post["item_no"]); $i++) {
                $item_no = trim((string)$post["item_no"][$i]);
                $description = trim((string)$post["item_description"][$i]);
                $quantity = (int)$post["quantity"][$i];

                if ($item_no === "" || $description === "" || $quantity <= 0) {
                    throw new Exception("All item rows must have item number, description, and quantity.");
                }

                if (in_array($item_no, $itemNosInThisForm, true)) {
                    throw new Exception("Duplicate Item No. $item_no in the form.");
                }

                if ($this->itemModel->itemNoExists($item_no)) {
                    throw new Exception("Item No. $item_no already exists. Please use a unique item number.");
                }

                $itemNosInThisForm[] = $item_no;

                $this->itemModel->addItem(
                    $item_no,
                    $description,
                    $quantity,
                    $do_id
                );
            }

            $this->notificationModel->createNotification(
                $staff_id,
                $supplier_id,
                "Delivery Order $do_number has been submitted with status: $status."
            );

            $this->pdo->commit();

            return $do_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getSupplierById($supplier_id)
    {
        return $this->supplierModel->getSupplierById($supplier_id);
    }

    public function getDODetails($do_id, $supplier_id = null)
    {
        if (empty($do_id)) {
            return null;
        }

        $do = $this->doModel->getDOById($do_id, $supplier_id);

        if (!$do) {
            return null;
        }

        return [
            "do" => $do,
            "items" => $this->itemModel->getItemsByDOId($do_id)
        ];
    }

    public function getDOHistory($supplier_id, $month = '')
    {
        return $this->doModel->getDOHistoryBySupplier($supplier_id, $month);
    }

    public function getFile($do_id, $type, $supplier_id = null)
    {
        if (empty($do_id)) {
            return null;
        }

        return $this->doModel->getFile($do_id, $type, $supplier_id);
    }
}