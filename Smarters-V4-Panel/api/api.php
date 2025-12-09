<?php
header("Content-Type: application/json");
$db = new SQLite3("database.db");
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData, true);
$action = $data["action"];
switch ($action) {
    case "check-maintainencemode":
        echo checkMaintenanceMode();
        break;
    case "get_advertisemnt_status":
        echo getAdvertisementStatus();
        break;
    case "add-device":
        echo addDevice($data);
        break;
    case "addreport":
        echo addReport($data);
        break;
    case "addclientfeedback":
        echo addClientFeedback($data);
        break;
    case "get-announcements":
        echo getAnnouncement($data);
        break;
    case "get-ovpnzip":
        echo getovpnzip();
        break;
    default:
        echo json_encode(["result" => "error", "message" => "Invalid action"]);
        echo "\r\n\r\n";
}
function getovpnzip()
{
    if (!empty($_SERVER["HTTPS"])) {
        $proto = "https";
    } else {
        $proto = "http";
    }
    $current = $proto . "://" . $_SERVER["HTTP_HOST"] . substr($_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["REQUEST_URI"], "/"));
    echo "{\"result\":\"success\",\"sc\":\"fa059e4a456aec6e165fbe25085151c4\",\"message\":\"Data retrieved successfully\",\"vpnstatus\":\"on\",\"link\":\"" . $current . "/vpn.php" . "\"}";
}
function checkMaintenanceMode()
{
    global $db;
    $result = $db->querySingle("SELECT title, body, mode FROM maintenance WHERE id=1", true);
    $sc = generateRandomSC();
    $response = ["result" => "success", "sc" => $sc, "maintenancemode" => $result["mode"] ?? "off", "message" => $result["title"] ?? "", "footercontent" => $result["body"] ?? ""];
    return json_encode($response);
}
function getAdvertisementStatus()
{
    global $db;
    $result = $db->querySingle("SELECT status, viewable_rate, message FROM advertisement WHERE id=1", true);
    $sc = generateRandomSC();
    $response = ["result" => "success", "sc" => $sc, "add_status" => $result["status"] ?? "off", "add_viewable_rate" => $result["viewable_rate"] ?? "", "message" => $result["message"] ?? ""];
    return json_encode($response);
}
function addDevice($data)
{
    global $db;
    $deviceid = $data["deviceid"] ?? "";
    $deviceusername = $data["deviceusername"] ?? "";
    $stmt = $db->prepare("INSERT OR REPLACE INTO devices(deviceid, deviceusername) VALUES (?, ?)");
    $stmt->bindParam(1, $deviceid);
    $stmt->bindParam(2, $deviceusername);
    $stmt->execute();
    $sc = generateRandomSC();
    $response = ["result" => "success", "sc" => $sc, "message" => "Details Updated Successfully"];
    return json_encode($response);
}
function addReport($data)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO reports (username, macaddress, section, section_category, report_title, report_sub_title, report_cases, report_custom_message, stream_name, stream_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        logError($db->lastErrorMsg());
        return json_encode(["result" => "error", "message" => "Database error"]);
    }
    $stmt->bindParam(1, $data["username"]);
    $stmt->bindParam(2, $data["macaddress"]);
    $stmt->bindParam(3, $data["section"]);
    $stmt->bindParam(4, $data["section_category"]);
    $stmt->bindParam(5, $data["report_title"]);
    $stmt->bindParam(6, $data["report_sub_title"]);
    $stmt->bindParam(7, $data["report_cases"]);
    $stmt->bindParam(8, $data["report_custom_message"]);
    $stmt->bindParam(9, $data["stream_name"]);
    $stmt->bindParam(10, $data["stream_id"]);
    if (!$stmt->execute()) {
        logError($db->lastErrorMsg());
        return json_encode(["result" => "error", "message" => "Failed to add report"]);
    }
    $sc = generateRandomSC();
    return json_encode(["result" => "success", "sc" => $sc, "message" => "Report added successfully"]);
}
function addClientFeedback($data)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO feedback (username, macaddress, feedback_content) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $data["username"]);
    $stmt->bindParam(2, $data["macaddress"]);
    $stmt->bindParam(3, $data["feedback"]);
    $stmt->execute();
    $response = ["result" => "success", "message" => "Feedback sent successfully!"];
    return json_encode($response);
}
function getAnnouncement($data)
{
    global $db;
    $announcements = [];
    $result = $db->query("SELECT * FROM announcements ORDER BY created_on DESC");
    if (!$result) {
        exit(json_encode(["error" => $db->lastErrorMsg()]));
    }
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $announcements[] = $row;
    }
    $responseData = [];
    foreach ($announcements as $announcement) {
        $stmt = $db->prepare("SELECT 1 FROM announcement_views WHERE announcement_id = ? AND deviceid = ?");
        $stmt->bindParam(1, $announcement["id"]);
        $stmt->bindParam(2, $data["deviceid"]);
        $res = $stmt->execute();
        $seen = $res->fetchArray(SQLITE3_ASSOC) ? 1 : 0;
        if ($seen === 0) {
            $stmt = $db->prepare("INSERT INTO announcement_views (announcement_id, deviceid) VALUES (?, ?)");
            $stmt->bindParam(1, $announcement["id"]);
            $stmt->bindParam(2, $data["deviceid"]);
            $stmt->execute();
        }
        $announcement["seen"] = $seen;
        $responseData[] = $announcement;
    }
    $sc = generateRandomSC();
    return json_encode(["result" => "success", "sc" => $sc, "message" => count($responseData) ? "Announcements fetched" : "No announcements", "totalrecords" => count($responseData), "data" => $responseData]);
}
function generateRandomSC()
{
    $randomBytes = random_bytes(16);
    return bin2hex($randomBytes);
}
function logError($errorMessage)
{
    $date = date("Y-m-d H:i:s");
    $message = "[" . $date . "] " . $errorMessage . "\n";
    file_put_contents("errors.log", $message, FILE_APPEND);
}

?>
