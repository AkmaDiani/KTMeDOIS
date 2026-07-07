<?php
// Application/Middleware/API_gateways/invoicePdf.php

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Invoice.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Item.php';
require_once ROOT_PATH . '/Application/Model/modelM3/DeliveryOrder.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Supplier.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Staff.php';

class InvoicePdf {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function generateAndDownload($invoiceId, $userRole, $vendorId = null) {
        $invoice = new Invoice();
        if (!$invoice->load($invoiceId)) {
            throw new Exception('Invoice not found.');
        }

        if ($userRole === 'Vendor') {
            if ($invoice->supplier_ID != $vendorId) {
                throw new Exception('You can only download your own invoices.');
        }
        } elseif (!in_array($userRole, ['KTM Officer', 'Finance Officer'])) {
            throw new Exception('Unauthorized role: ' . $userRole);
        }

        $items = $invoice->getItems();
        $do = $invoice->getDO();
        $supplier = $invoice->getSupplier();
        $staff = $invoice->getStaff();

        // Convert supplier to array if it's an object
        if (is_object($supplier)) {
            $supplier = (array) $supplier;
        }

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->AddPage();

        $html = $this->buildInvoiceHtml($invoice, $items, $do, $supplier, $staff);
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdfContent = $pdf->Output('', 'S');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Invoice_' . $invoice->Invoice_num . '.pdf"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    public function generatePreview($do, $items, $supplier, $subtotal, $tax, $discount, $total) {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT Invoice_num FROM invoice WHERE Invoice_num LIKE ? ORDER BY Invoice_id DESC LIMIT 1");
        $stmt->execute(["INV-$year-%"]);
        $last = $stmt->fetch();
        if ($last) {
            $parts = explode('-', $last['Invoice_num']);
            $num = intval($parts[2]) + 1;
        } else {
            $num = 1;
        }
        $invoiceNum = sprintf("INV-%s-%04d", $year, $num);

        $invoice = new stdClass();
        $invoice->Invoice_num = $invoiceNum;
        $invoice->issue_date = date('Y-m-d');
        $invoice->Subtotal = $subtotal;
        $invoice->Tax = $tax;
        $invoice->discount = $discount;
        $invoice->penalty = 0;
        $invoice->Total = $total;
        $invoice->Description = '';
        $invoice->Staff_id = $do['Staff_id'] ?? '';

        // ✅ Convert supplier to array if it's an object
        if (is_object($supplier)) {
            $supplier = (array) $supplier;
        }

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->AddPage();

        $html = $this->buildInvoiceHtml($invoice, $items, $do, $supplier, null);
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('Invoice_Preview.pdf', 'I');
        exit;
    }

    // Converts any PNG/JPG to a base64 JPEG <img> tag.
    private function imgTag($absolutePath, $width = 100, $alt = '') {
        $realPath = realpath($absolutePath);
        if (!$realPath || !file_exists($realPath)) {
            return '';
        }

        $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));

        if ($ext === 'png' && function_exists('imagecreatefrompng') && function_exists('imagejpeg')) {
            $src = @imagecreatefrompng($realPath);
            if ($src) {
                $w = imagesx($src);
                $h = imagesy($src);
                $flat = imagecreatetruecolor($w, $h);
                $white = imagecolorallocate($flat, 255, 255, 255);
                imagefilledrectangle($flat, 0, 0, $w, $h, $white);
                imagecopy($flat, $src, 0, 0, 0, 0, $w, $h);
                imagedestroy($src);
                ob_start();
                imagejpeg($flat, null, 95);
                $data = ob_get_clean();
                imagedestroy($flat);
                $b64 = base64_encode($data);
                return '<img src="data:image/jpeg;base64,' . $b64 . '" width="' . $width . '" alt="' . htmlspecialchars($alt) . '">';
            }
        }

        $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
        $b64 = base64_encode(file_get_contents($realPath));
        return '<img src="data:' . $mime . ';base64,' . $b64 . '" width="' . $width . '" alt="' . htmlspecialchars($alt) . '">';
    }

    // HTML builder — mirrors the sample invoice layout
    private function buildInvoiceHtml($invoice, $items, $do, $supplier, $staff) {
        $issueDate = date('d-M-Y', strtotime($invoice->issue_date));
        $dueDate = date('d-M-Y', strtotime($invoice->issue_date . ' +30 days'));

        // ✅ Use array access for supplier
        $supplierName = is_array($supplier) ? ($supplier['Supplier_name'] ?? 'N/A') : 'N/A';
        $supplierAddress = is_array($supplier) ? ($supplier['Billing_address'] ?? 'N/A') : 'N/A';
        $staffId = $invoice->Staff_id ?? $do['Staff_id'] ?? 'N/A';

        $subtotal = number_format($invoice->Subtotal, 2);
        $tax = number_format($invoice->Tax, 2);
        $total = number_format($invoice->Total, 2);
        $shipping = '0.00';
        $payments = '-' . $total;
        $credits = '0.00';
        $finCharge = '0.00';

        $itemRows = '';
        $lineTotal = 0;
        $counter = 1;
        foreach ($items as $item) {
            $desc = $item['item_description'] ?? $item['description'] ?? '';
            $qty = $item['quantity'] ?? 0;
            $price = $item['unit_price'] ?? 0;
            $amount = $qty * $price;
            $lineTotal += $amount;
            $itemRows .= '
            <tr>
                <td style="border:1px solid #ffffff;padding:4px 5px;">' . $counter++ . '</td>
                <td style="border:1px solid #ffffff;padding:4px 5px;"></td>
                <td style="border:1px solid #ffffff;padding:4px 5px;">' . htmlspecialchars($desc) . '</td>
                <td style="border:1px solid #ffffff;padding:4px 5px;text-align:center;"> </td>
                <td style="border:1px solid #ffffff;padding:4px 5px;text-align:right;">' . $qty . '</td>
                <td style="border:1px solid #ffffff;padding:4px 5px;text-align:right;">' . number_format($price, 2) . '</td>
                <td style="border:1px solid #ffffff;padding:4px 5px;text-align:right;">' . number_format($amount, 2) . '</td>
            </tr>';
        }
        if ($counter === 1) {
            $itemRows = '<tr><td colspan="7" style="text-align:center;color:#999;padding:12px;border:1px solid #ffffff;">No items found</td></tr>';
        }
        $lineTotalFmt = number_format($lineTotal, 2);

        $logoHtml = $this->imgTag(__DIR__ . '/../../../Presentation/Public/assets/ktm_logo(1).jpg', 110);
        $qrHtml = $this->imgTag(__DIR__ . '/../../../Presentation/Public/assets/qr.jpg', 90, 'QR');

        $html = '
<style>
    body, td, th { font-family: Arial, Helvetica, sans-serif; font-size: 9px; color: #222; }
    .blue { color: #003580; }
</style>

<p style="text-align:center;font-size:20px;font-weight:bold;margin:0 0 6px 0;">INVOICE</p>

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="40%" valign="middle">' . $logoHtml . '</td>
        <td width="60%" valign="top" style="text-align:right;font-size:9px;line-height:1.6;">
            <strong style="font-size:11px;">Keretapi Tanah Melayu Berhad</strong><br>
            KTMB Headquarters<br>
            Jalan Sultan Hishamuddin<br>
            50621 Kuala Lumpur<br>
            Company Registration No: 199101015631<br>
            SST No : W10-1808-31002103<br>
            Supplier TIN : C4893200000<br>
            Tel : 03-2263 1111<br>
            <span class="blue">Web : www.ktmb.com.my</span>
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #ccc;border-collapse:collapse;">
    <tr>
        <td width="28%" valign="top" style="border:1px solid #ccc;padding:6px;">
            <span style="color:#000000;font-weight:bold;">Bill-to</span><br><br>
            <strong>' . htmlspecialchars($supplierName) . '</strong><br>
            ' . nl2br(htmlspecialchars($supplierAddress)) . '<br>
        </td>
        <td width="28%" valign="top" style="border:1px solid #ccc;padding:6px;">
            <span style="color:#000000;font-weight:bold;">Ship-to</span><br><br>
        </td>
        <td width="44%" valign="top" style="padding:0;">
            <table width="100%" cellpadding="4" cellspacing="0">
                <tr>
                    <td style="color:#000000;font-weight:bold;border-bottom:1px solid #ccc;padding:5px 8px;">Invoice No</td>
                    <td style="border-bottom:1px solid #ccc;padding:5px 8px;">' . htmlspecialchars($invoice->Invoice_num) . '</td>
                </tr>
                <tr>
                    <td style="color:#000000;font-weight:bold;border-bottom:1px solid #ccc;padding:5px 8px;">Invoice Date</td>
                    <td style="border-bottom:1px solid #ccc;padding:5px 8px;">' . $issueDate . '</td>
                </tr>
                <tr>
                    <td style="color:#000000;font-weight:bold;padding:5px 8px;">UUID</td>
                    <td style="padding:5px 8px;">1PMMRDJWNTPX9R1DE88CE6</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="4" cellspacing="0">
    <tr>
        <td width="55%" valign="top">
            <table cellpadding="3" cellspacing="0">
                <tr>
                    <td style="color:#555;padding-right:12px;">Customer No</td>
                    <td><strong>' . htmlspecialchars((string)$staffId) . '</strong></td>
                </tr>
                <tr>
                    <td style="color:#555;padding-right:12px;">Shipped Date</td>
                    <td>' . $issueDate . '</td>
                </tr>
            </table>
        </td>
        <td width="45%" valign="top">
            <table width="100%" cellpadding="3" cellspacing="0">
                <tr><td>Line Total</td><td style="text-align:right;">' . $subtotal . '</td></tr>
                <tr><td>Service Tax</td><td style="text-align:right;">' . $tax . '</td></tr>
                <tr><td>Shipping</td><td style="text-align:right;">' . $shipping . '</td></tr>
                <tr><td colspan="2"><hr style="border:1px solid #ccc;margin:2px 0;"></td></tr>
                <tr><td><strong>Total</strong></td><td style="text-align:right;"><strong>' . $total . '</strong></td></tr>
                <tr><td>Payments</td><td style="text-align:right;">' . $payments . '</td></tr>
                <tr><td>Credits</td><td style="text-align:right;">' . $credits . '</td></tr>
                <tr><td>Financial Charges</td><td style="text-align:right;">' . $finCharge . '</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="5" cellspacing="0" style="background:#003580;color:#fff;border-collapse:collapse;">
    <tr>
        <td style="padding:6px 10px;">
            <strong>Payment Terms</strong>&nbsp;&nbsp;30 DAYS
            &nbsp;&nbsp;&nbsp;&nbsp;
            <strong>Due Date</strong>&nbsp;&nbsp;' . $dueDate . '
        </td>
        <td style="text-align:right;padding:6px 10px;font-size:12px;">
            <strong>Balance Due &nbsp;&nbsp;RM0.00</strong>
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:9px;">
    <thead>
        <tr style="background:#003580;color:#fff;">
            <th style="padding:5px;text-align:left;width:5%;">No.</th>
            <th style="padding:5px;text-align:left;width:10%;">Product</th>
            <th style="padding:5px;text-align:left;width:35%;">Description</th>
            <th style="padding:5px;text-align:center;width:10%;">UOM</th>
            <th style="padding:5px;text-align:right;width:10%;">Quantity</th>
            <th style="padding:5px;text-align:right;width:15%;">Unit Price</th>
            <th style="padding:5px;text-align:right;width:15%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        ' . $itemRows . '
        <tr>
            <td colspan="6" style="border:1px solid #ccc;text-align:right;padding:4px 5px;">Line Total</td>
            <td style="border:1px solid #ccc;text-align:right;padding:4px 5px;">' . $lineTotalFmt . '</td>
        </tr>
    </tbody>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="65%" valign="top">
            <strong>Please make payment to :</strong><br><br>
            Account Name : KERETAPI TANAH MELAYU BERHAD<br>
            Acc. No &nbsp;&nbsp;&nbsp;: 514011336586<br>
            Bank Name &nbsp;: Malayan Banking Bhd<br>
            <br>
            <strong>Salesperson</strong><br><br>
        </td>
        <td width="35%" valign="bottom" style="text-align:right;">
            ' . $qrHtml . '
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="text-align:center;font-size:8px;color:#555;border-top:1px solid #ccc;padding-top:6px;">
            THIS IS A COMPUTER GENERATED. NO SIGNATURE REQUIRED &nbsp;&nbsp;&nbsp;&nbsp; Page 1 of 1
        </td>
    </tr>
</table>';

        return $html;
    }
}