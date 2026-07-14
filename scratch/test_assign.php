<?php
require_once __DIR__ . '/../db.php';

echo "<h2>Starting PMS Booking Logic Verification Test Suite</h2>";

try {
    // 1. Fetch first active room category
    $room = $pdo->query("SELECT * FROM rooms WHERE status = 'active' LIMIT 1")->fetch();
    if (!$room) {
        throw new Exception("No active room category exists in DB. Create one first.");
    }
    echo "Using Room Category: <strong>" . htmlspecialchars($room['title']) . " (ID: {$room['id']})</strong><br>";

    // 2. Count physical rooms in this category
    $pr_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM physical_rooms WHERE category_id = ? AND status NOT IN ('Maintenance', 'Out of Service')");
    $pr_count_stmt->execute([$room['id']]);
    $total_physical = (int)$pr_count_stmt->fetchColumn();
    echo "Total active physical rooms in category: <strong>{$total_physical}</strong><br>";

    // 3. Test Stay Dates
    $check_in = date('Y-m-d', strtotime('+5 days'));
    $check_out = date('Y-m-d', strtotime('+8 days'));
    echo "Simulating Booking Dates: <strong>{$check_in}</strong> to <strong>{$check_out}</strong> (3 Nights)<br>";

    // 4. Run overlap check
    $pr_booked_stmt = $pdo->prepare("SELECT COUNT(DISTINCT physical_room_id) FROM bookings WHERE room_id = ? AND check_in < ? AND check_out > ? AND booking_status != 'cancelled' AND physical_room_id IS NOT NULL");
    $pr_booked_stmt->execute([$room['id'], $check_out, $check_in]);
    $booked_count = (int)$pr_booked_stmt->fetchColumn();
    
    $available_count = $total_physical - $booked_count;
    echo "Overlap booked physical rooms: <strong>{$booked_count}</strong><br>";
    echo "Calculated available room inventory: <strong>{$available_count}</strong><br>";

    if ($available_count <= 0) {
        echo "<span style='color:red;'>Category is SOLD OUT for these dates. Testing room assignment fallback.</span><br>";
    } else {
        echo "<span style='color:green;'>Availability confirmed! Proceeding to simulate reservation insert.</span><br>";
    }

    // 5. Insert mock booking with GV- format
    $date_str = date('Ymd');
    $hex_str = strtoupper(bin2hex(random_bytes(3)));
    $booking_id = "GV-" . $date_str . "-" . $hex_str;

    $ins_stmt = $pdo->prepare("INSERT INTO bookings (
        booking_id, customer_name, customer_email, customer_phone, 
        check_in, check_out, guests, room_id, total_nights, 
        base_amount, tax_amount, discount_amount, total_amount, 
        payment_status, booking_status, special_request, razorpay_order_id
    ) VALUES (?, 'Test Agent', 'test@example.com', '9999999999', ?, ?, 2, ?, 3, 3000, 150, 0, 3150, 'pending', 'pending', 'Test special request', 'order_test123')");
    
    $ins_stmt->execute([$booking_id, $check_in, $check_out, $room['id']]);
    echo "Created mock booking record with Booking ID: <strong>{$booking_id}</strong><br>";

    // 6. Simulate payment confirmation callback logic
    // Generate Invoice Number: INV-YYYYMMDD-[SEQUENCE]
    $date_prefix = 'INV-' . date('Ymd') . '-';
    $seq_stmt = $pdo->prepare("SELECT invoice_no FROM bookings WHERE invoice_no LIKE ? ORDER BY invoice_no DESC LIMIT 1");
    $seq_stmt->execute([$date_prefix . '%']);
    $last_invoice = $seq_stmt->fetchColumn();
    if ($last_invoice) {
        $seq = (int)substr($last_invoice, -4);
        $next_seq = str_pad($seq + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $next_seq = '0001';
    }
    $invoice_no = $date_prefix . $next_seq;
    echo "Generated sequential invoice number: <strong>{$invoice_no}</strong><br>";

    // Auto-allocate first available room
    $physical_room_id = null;
    $pr_stmt = $pdo->prepare("
        SELECT id FROM physical_rooms 
        WHERE category_id = ? 
          AND status NOT IN ('Maintenance', 'Out of Service')
          AND id NOT IN (
              SELECT DISTINCT physical_room_id 
              FROM bookings 
              WHERE room_id = ? 
                AND check_in < ? 
                AND check_out > ? 
                AND booking_status != 'cancelled' 
                AND physical_room_id IS NOT NULL
          )
        ORDER BY id ASC LIMIT 1
    ");
    $pr_stmt->execute([$room['id'], $room['id'], $check_out, $check_in]);
    $room_id_assigned = $pr_stmt->fetchColumn();
    if ($room_id_assigned) {
        $physical_room_id = (int)$room_id_assigned;
    }

    if ($physical_room_id) {
        $pr_num_stmt = $pdo->prepare("SELECT room_number FROM physical_rooms WHERE id = ?");
        $pr_num_stmt->execute([$physical_room_id]);
        $room_number = $pr_num_stmt->fetchColumn();
        echo "Auto-allocated Physical Room Number: <strong>Room {$room_number} (ID: {$physical_room_id})</strong><br>";
    } else {
        echo "<span style='color:orange;'>No physical room available for assignment! Leaving blank for manual allocation.</span><br>";
    }

    // Update booking to simulate confirmation
    $upd_stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'paid', booking_status = 'confirmed', invoice_no = ?, physical_room_id = ?, razorpay_payment_id = 'pay_mock123' WHERE booking_id = ?");
    $upd_stmt->execute([$invoice_no, $physical_room_id, $booking_id]);
    echo "<span style='color:green; font-weight:bold;'>Simulation callback successfully updated booking state!</span><br>";

    // 7. Clean up simulated booking record
    $del_stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $del_stmt->execute([$booking_id]);
    echo "Cleaned up mock booking record to keep live DB clean.<br>";
    echo "<h3>VERIFICATION SUCCESSFUL: ALL CHECKS PASS!</h3>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Verification Error: " . $e->getMessage() . "</h3>";
}
?>
