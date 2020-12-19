<?php
// Function which calculates max available item quantity for give timespan
function checkItemsInTimeSpan(object $mysqli, string $startStr, string $endStr, array $items)
{
    // Select ever item for every order which is affected by given timespan
    $query = "SELECT order_has_item.quantity, item.idItem, item.count FROM order_has_item INNER JOIN tbl_order ON order_has_item.order_idOrder=tbl_order.idOrder RIGHT JOIN item ON order_has_item.item_idItem=item.idItem AND tbl_order.pickUpDatetime<=? AND tbl_order.returnDatetime>=?;";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $endStr, $startStr);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $feedback = array();

    // Calculate max available quantity
    foreach ($items as $item) {
        $id = $item["id"];
        foreach ($result as $order) {
            if ($order["idItem"] == $id) { // Search if order belongs to current $item
                if (!isset($max)) { // Is it first hit? Set max as item-count
                    $max = $order["count"];
                }
                if (isset($order["quantity"])) {
                    $max -= $order["quantity"]; // Subtract quantity
                }
            }
        }
        $feedback[] = array("id" => $id, "max" => $max); // Add new max quantity to feedback
        unset($max);
    }

    return $feedback; // Return max quantities
}
