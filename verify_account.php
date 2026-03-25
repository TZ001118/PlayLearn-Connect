<?php
header('Content-Type: application/json');

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "playlearn_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier'] ?? '';

    if(empty($identifier)){
        echo json_encode(["status" => "error", "message" => "Please enter username or email."]);
        exit();
    }

    // 1. 查询所有匹配的账号
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $accounts = [];
        $foundEmail = "";

        // 2. 收集数据
        while ($row = $result->fetch_assoc()) {
            $accounts[] = [
                "username" => $row['username']
            ];
            $foundEmail = $row['email']; // 记录关联的真实邮箱
        }

        // 3. ✅ 核心魔法：对邮箱进行“打码”处理 (Masking)
        $maskedEmail = ""; 
        if (!empty($foundEmail)) {
            $parts = explode("@", $foundEmail);
            $name = $parts[0];
            $domain = $parts[1];
            
            // 如果名字太短（比如只有1-2位），处理方式要温和一点
            if (strlen($name) <= 2) {
                $maskedName = $name[0] . "*";
            } else {
                // 显示第1位和最后1位，中间全部变星号
                $maskedName = substr($name, 0, 1) . str_repeat('*', 4) . substr($name, -1);
            }
            $maskedEmail = $maskedName . "@" . $domain;
        }

        // 4. 把数据发给前端
        echo json_encode([
            "status" => "success", 
            "accounts" => $accounts,
            "email" => $foundEmail,       // 供你后端逻辑比对（隐藏在 JS 内存里）
            "masked_email" => $maskedEmail // 供前端界面显示（例如：j****g@gmail.com）
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Account not found. Please check your spelling."]);
    }

    $stmt->close();
}
$conn->close();
?>