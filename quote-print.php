<?php
// quote-print.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$quote_items = [];
$subtotal = 0;
$vat_rate = 0.14; // Standard 14% VAT (Adjust if non-vatable)

if (isset($_SESSION['quote']) && !empty($_SESSION['quote'])) {
    $pdo = getDB();
    foreach ($_SESSION['quote'] as $key => $item) {
        $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name FROM products p JOIN brands b ON p.brand_id = b.id WHERE p.id = ? AND p.is_active = 1");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $stmt = $pdo->prepare("SELECT price, sku FROM product_variants WHERE product_id = ? AND size = ? AND color = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$item['product_id'], $item['size'], $item['color']]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($variant) {
                $line_subtotal = $variant['price'] * $item['quantity'];
                $quote_items[] = [
                    'product' => $product,
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'quantity' => $item['quantity'],
                    'price' => $variant['price'],
                    'sku' => $variant['sku'] ?? 'N/A',
                    'subtotal' => $line_subtotal
                ];
                $subtotal += $line_subtotal;
            }
        }
    }
}

$vat_amount = $subtotal * $vat_rate;
$grand_total = $subtotal + $vat_amount;
$quote_no = "QT-" . date('Ymd') . "-" . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
$quote_date = date('d/m/Y');
$valid_until = date('d/m/Y', strtotime('+14 days'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation #<?= $quote_no ?> – Kit Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --erp-primary: #1a2530;
            --erp-accent: #e63946;
            --erp-border: #dcdfe3;
            --erp-bg-light: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            background-color: #f4f6f9;
            font-size: 0.875rem;
            line-height: 1.4;
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .document-wrapper {
            background: #ffffff;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            border: 1px solid var(--erp-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Top Header Details */
        .company-brand img {
            max-width: 200px;
            height: auto;
        }

        .document-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--erp-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0;
        }

        .meta-table td {
            padding: 2px 8px;
            font-size: 0.85rem;
        }

        .meta-table td.label {
            font-weight: 600;
            color: #6c757d;
            text-align: right;
        }

        .meta-table td.value {
            font-weight: 700;
            color: var(--erp-primary);
            text-align: right;
        }

        /* Address & Billing Blocks */
        .address-block {
            border: 1px solid var(--erp-border);
            border-radius: 4px;
            padding: 12px 15px;
            background-color: var(--erp-bg-light);
            height: 100%;
        }

        .address-block h6 {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #6c757d;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--erp-border);
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        /* Line Items Table */
        .erp-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .erp-table th {
            background-color: var(--erp-primary);
            color: #ffffff;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            font-weight: 600;
            border: 1px solid var(--erp-primary);
        }

        .erp-table td {
            padding: 8px 10px;
            border: 1px solid var(--erp-border);
            vertical-align: top;
        }

        .erp-table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Totals Block */
        .totals-table {
            width: 100%;
            margin-top: 15px;
        }

        .totals-table td {
            padding: 5px 10px;
            font-size: 0.875rem;
        }

        .totals-table tr.grand-total {
            background-color: var(--erp-primary);
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
        }

        /* Payment & Terms Section */
        .terms-section {
            border-top: 2px solid var(--erp-primary);
            margin-top: 30px;
            padding-top: 15px;
            font-size: 0.8rem;
        }

        .bank-details-box {
            background-color: var(--erp-bg-light);
            border: 1px dashed var(--erp-border);
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        /* Print Media Settings */
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                font-size: 11pt;
            }
            .document-wrapper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
            .erp-table th {
                background-color: #1a2530 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .totals-table tr.grand-total {
                background-color: #1a2530 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Screen Only) -->
    <div class="container max-width-900 mb-3 no-print" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 border rounded shadow-sm">
            <a href="quote.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Cart
            </a>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-semibold" style="background-color: var(--erp-accent); border-color: var(--erp-accent);">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Official Document Wrapper -->
    <div class="document-wrapper">
        
        <!-- Header Section -->
        <div class="row align-items-start pb-3 border-bottom">
            <div class="col-7">
                <div class="company-brand mb-2">
                    <img src="assets/images/kitGroup.webp" alt="Kit Group Botswana" title="Kit Group">
                </div>
                <div class="small text-muted">
                    <strong>Kit Group Botswana (Pty) Ltd</strong><br>
                    Plot 123, Block 3, Industrial Area<br>
                    Gaborone, Botswana<br>
                    <strong>TEL:</strong> +267 31 234 567 | <strong>EMAIL:</strong> sales@kitgroup.co.bw<br>
                    <strong>VAT REG NO:</strong> C12345678901 | <strong>CO. REG NO:</strong> BW00000123456
                </div>
            </div>
            <div class="col-5 text-end">
                <h1 class="document-title">Quotation</h1>
                <table class="meta-table ms-auto mt-2">
                    <tr>
                        <td class="label">Quote Reference:</td>
                        <td class="value"><?= $quote_no ?></td>
                    </tr>
                    <tr>
                        <td class="label">Date Issued:</td>
                        <td class="value"><?= $quote_date ?></td>
                    </tr>
                    <tr>
                        <td class="label">Valid Until:</td>
                        <td class="value"><?= $valid_until ?></td>
                    </tr>
                    <tr>
                        <td class="label">Currency:</td>
                        <td class="value">BWP (P)</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Addresses Row -->
        <div class="row mt-4">
            <div class="col-6">
                <div class="address-block">
                    <h6>Customer / Bill To</h6>
                    <div class="fw-bold text-dark">Cash Customer / Counter Quote</div>
                    <div class="text-muted small">
                        Gaborone Branch<br>
                        Botswana<br>
                        <strong>Contact:</strong> Over-the-counter Inquiry
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="address-block">
                    <h6>Dispatch & Sales Channel</h6>
                    <div class="small text-muted">
                        <strong>Issued By:</strong> Web / POS System Admin<br>
                        <strong>Store Branch:</strong> Main Warehouse (Gaborone)<br>
                        <strong>Payment Terms:</strong> Pre-payment / Cash On Delivery<br>
                        <strong>Status:</strong> Estimate / Pending Confirmation
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($quote_items)): ?>
            <div class="text-center py-5 my-4 border rounded bg-light">
                <i class="bi bi-cart-x text-muted display-4"></i>
                <p class="mt-2 text-muted fw-bold">No active items found for quotation generating.</p>
            </div>
        <?php else: ?>
            <!-- Line Items Table -->
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">#</th>
                        <th style="width: 40%;">Item Description</th>
                        <th style="width: 10%; text-align: center;">Size</th>
                        <th style="width: 10%; text-align: right;">Unit Price</th>
                        <th style="width: 8%; text-align: center;">Qty</th>
                        <th style="width: 12%; text-align: right;">Total (Excl)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quote_items as $index => $item): ?>
                        <tr>
                            <td style="text-align: center; color: #6c757d;"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($item['product']['name']) ?></div>
                                <div class="small text-muted">Brand: <?= htmlspecialchars($item['product']['brand_name']) ?> | Color: <?= htmlspecialchars($item['color']) ?></div>
                            </td>
                            <td style="text-align: center;"><?= htmlspecialchars($item['size']) ?></td>
                            <td style="text-align: right;">P <?= number_format($item['price'], 2) ?></td>
                            <td style="text-align: center; font-weight: 600;"><?= $item['quantity'] ?></td>
                            <td style="text-align: right; font-weight: 600;">P <?= number_format($item['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Calculations & Summary Block -->
            <div class="row mt-3">
                <div class="col-6">
                    <div class="bank-details-box mt-2">
                        <div class="fw-bold mb-1 text-dark"><i class="bi bi-bank me-1"></i> Direct Banking / EFT Details</div>
                        <div class="row g-1">
                            <div class="col-4 text-muted">Bank Name:</div>
                            <div class="col-8 fw-semibold">First National Bank (FNB)</div>
                            <div class="col-4 text-muted">Account Name:</div>
                            <div class="col-8 fw-semibold">Kit Group Botswana</div>
                            <div class="col-4 text-muted">Account No:</div>
                            <div class="col-8 fw-semibold">62123456789</div>
                            <div class="col-4 text-muted">Branch Code:</div>
                            <div class="col-8 fw-semibold">281411 (Corporate)</div>
                            <div class="col-4 text-muted">Reference:</div>
                            <div class="col-8 fw-semibold text-danger"><?= $quote_no ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <table class="totals-table">
                        <tr>
                            <td class="text-end text-muted">Subtotal (Net):</td>
                            <td class="text-end fw-semibold" style="width: 40%;">P <?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <tr>
                            <td class="text-end text-muted">VAT (14%):</td>
                            <td class="text-end fw-semibold">P <?= number_format($vat_amount, 2) ?></td>
                        </tr>
                        <tr class="grand-total">
                            <td class="text-end">Grand Total (Incl. VAT):</td>
                            <td class="text-end">P <?= number_format($grand_total, 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Notes & Standard ERP Legal Terms -->
            <div class="terms-section">
                <div class="row">
                    <div class="col-8">
                        <h6 class="fw-bold text-uppercase mb-1" style="font-size: 0.75rem;">Terms & Conditions</h6>
                        <ol class="ps-3 mb-0 text-muted" style="font-size: 0.75rem;">
                            <li>Prices are quoted in Botswana Pula (BWP) and are valid for 14 days from date of issue.</li>
                            <li>Goods remain the property of Kit Group Botswana until paid for in full.</li>
                            <li>Branded or customized safety workwear orders cannot be cancelled once production has commenced.</li>
                        </ol>
                    </div>
                    <div class="col-4 text-end d-flex flex-column justify-content-end">
                        <div class="border-top pt-2 mt-4 text-center">
                            <span class="small text-muted">Authorized Signature / Stamp</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ERP System Footer -->
            <div class="mt-4 pt-2 border-top text-center text-muted" style="font-size: 0.7rem;">
                This document is an official computer-generated quotation produced by Kit Group ERP Engine. No physical signature is required.
            </div>
        <?php endif; ?>

    </div>

</body>
</html>