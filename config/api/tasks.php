<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();
$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        $query = "SELECT id, title, description, status FROM tasks ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode(array("status" => "success", "data" => $tasks));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->title)) {
            $query = "INSERT INTO tasks (title, description) VALUES (:title, :description)";
            $stmt = $db->prepare($query);
            $title = htmlspecialchars(strip_tags($data->title));
            $description = htmlspecialchars(strip_tags($data->description ?? ''));
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            if($stmt->execute()) {
                http_response_code(201);
                echo json_encode(array("status" => "success", "message" => "Task created."));
            } else {
                http_response_code(500);
                echo json_encode(array("status" => "error", "message" => "Database error."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "Title required."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id) && !empty($data->status)) {
            $query = "UPDATE tasks SET status = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $id = filter_var($data->id, FILTER_VALIDATE_INT);
            $status = htmlspecialchars(strip_tags($data->status));
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', $status);
            if($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Task updated."));
            } else {
                http_response_code(500);
                echo json_encode(array("status" => "error", "message" => "Update failed."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "Incomplete parameters."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->id)) {
            $query = "DELETE FROM tasks WHERE id = :id";
            $stmt = $db->prepare($query);
            $id = filter_var($data->id, FILTER_VALIDATE_INT);
            $stmt->bindParam(':id', $id);
            if($stmt->execute()) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "Task deleted."));
            } else {
                http_response_code(500);
                echo json_encode(array("status" => "error", "message" => "Delete failed."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "ID required."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("status" => "error", "message" => "Method Not Allowed"));
        break;
}
?>
