<?php
// Allow requests from your HTML/JS server's domain
header("Access-Control-Allow-Origin: https://your-html-server.com");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// Load environment variables
require_once __DIR__ . '/config/env.php';

// Validate HTTP request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'ok' => false,
        'message' => '🚫 Invalid request method. Please ensure you are submitting the form correctly!'
    ]);
    exit;
}

// Retrieve Telegram credentials from environment variables
$telegramBotToken = getenv('TELEGRAM_BOT_TOKEN');
$chatId = getenv('TELEGRAM_CHAT_ID');

if (!$telegramBotToken || !$chatId) {
    echo json_encode([
        'ok' => false,
        'message' => '⚠️ Server configuration error. Please contact the admin to resolve this issue.'
    ]);
    exit;
}

// Collect and sanitize form data
$formData = [];
foreach ($_POST as $key => $value) {
    $cleanKey = filter_var($key, FILTER_SANITIZE_STRING);
    $cleanValue = filter_var($value, FILTER_SANITIZE_STRING);
    $formData[$cleanKey] = $cleanValue;
}

// Validate required fields
if (empty($formData)) {
    echo json_encode([
        'ok' => false,
        'message' => '❗ Form data is missing. Please fill out all required fields.'
    ]);
    exit;
}

// Construct the message
$message = "✨ New Contact Form Submission Received! ✨\n\n";
foreach ($formData as $key => $value) {
    $message .= "	➤ " . ucfirst($key) . ": $value\n";
}

// Send data to Telegram
$data = [
    'chat_id' => $chatId,
    'text' => $message,
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
$response = file_get_contents("https://api.telegram.org/bot$telegramBotToken/sendMessage", false, $context);

// Process the response
if ($response === false) {
    echo json_encode([
        'ok' => false,
        'message' => '❌ Internal Server Error.Please try again later.'
    ]);
    exit;
}

$responseData = json_decode($response, true);

if ($responseData['ok']) {
    echo json_encode([
        'ok' => true,
        'message' => '🎉 Your message was sent successfully! We’ll get back to you soon. Thank you! 🙌'
    ]);
} else {
    echo json_encode([
        'ok' => false,
        'message' => '⚠️ Oops! Something Went wrong. Please contact support.'
    ]);
}
?>
