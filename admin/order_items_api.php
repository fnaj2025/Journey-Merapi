<?php
require "../include/db.php";

$id = intval($_GET['id']);

$q = mysqli_query($conn, "
    SELECT * FROM order_items 
    WHERE ID_Order = $id
");

echo "
<style>
.detail-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
}

.detail-table th {
    padding: 12px;
    background: #2c3e50;
    color: white;
    font-weight: 600;
    text-align: left;
    font-size: 13px;
    text-transform: uppercase;
}

.detail-table td {
    padding: 12px;
    border-bottom: 1px solid #ecf0f1;
    font-size: 14px;
    color: #2c3e50;
}

.detail-table tr:last-child td {
    border-bottom: none;
}

.detail-table tr:hover {
    background: #f8f9fa;
}

.empty-message {
    text-align: center;
    padding: 30px;
    color: #95a5a6;
}

.empty-message p {
    font-size: 16px;
    margin-bottom: 5px;
}

.price-highlight {
    color: #f0393d;
    font-weight: 600;
}
</style>

<table class='detail-table'>
<thead>
<tr>
    <th>Destinasi</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>
</thead>
<tbody>
";

if (mysqli_num_rows($q) === 0) {
    echo "
    <tr>
        <td colspan='4'>
            <div class='empty-message'>
                <p>Tidak ada destinasi pada pesanan ini</p>
            </div>
        </td>
    </tr>
    ";
} else {
    $total = 0;
    while ($d = mysqli_fetch_assoc($q)) {
        $price = number_format($d['Price'], 0, ",", ".");
        $subtotal = number_format($d['Subtotal'], 0, ",", ".");
        $total += $d['Subtotal'];

        echo "
        <tr>
            <td>" . htmlspecialchars($d['Tour_Name']) . "</td>
            <td>IDR {$price}</td>
            <td>{$d['Quantity']} pax</td>
            <td class='price-highlight'>IDR {$subtotal}</td>
        </tr>
        ";
    }
    
    // Total row
    $total_formatted = number_format($total, 0, ",", ".");
    echo "
    <tr style='background: #f8f9fa; font-weight: 700;'>
        <td colspan='3' style='text-align: right;'>TOTAL:</td>
        <td class='price-highlight' style='font-size: 16px;'>IDR {$total_formatted}</td>
    </tr>
    ";
}

echo "
</tbody>
</table>
";
?>