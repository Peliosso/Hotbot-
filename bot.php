<?php
require "config.php";

// =====================
// RECEBE UPDATE
// =====================
$update = json_decode(file_get_contents("php://input"), true);

$message = $update["message"] ?? null;
$callback = $update["callback_query"] ?? null;

// =====================
// FUNÇÕES
// =====================
function apiRequest($method, $data)
{
    $url = API_URL . "/" . $method;
    $options = [
        "http" => [
            "header"  => "Content-Type: application/json",
            "method"  => "POST",
            "content" => json_encode($data),
        ],
    ];
    file_get_contents($url, false, stream_context_create($options));
}

function sendMessage($chat_id, $text, $keyboard = null)
{
    $data = [
        "chat_id" => $chat_id,
        "text" => $text,
        "parse_mode" => "HTML"
    ];

    if ($keyboard) {
        $data["reply_markup"] = json_encode($keyboard);
    }

    apiRequest("sendMessage", $data);
}

function sendPhoto($chat_id, $photo, $caption = null, $keyboard = null)
{
    $data = [
        "chat_id" => $chat_id,
        "photo" => $photo
    ];

    if ($caption) $data["caption"] = $caption;
    if ($keyboard) $data["reply_markup"] = json_encode($keyboard);

    apiRequest("sendPhoto", $data);
}

// =====================
// /start
// =====================
if ($message && isset($message["text"]) && $message["text"] === "/start") {

    $chat_id = $message["chat"]["id"];

    global $WELCOME_PHOTOS;

    sendPhoto(
        $chat_id,
        $WELCOME_PHOTOS[0],
        WELCOME_MESSAGE
    );

    $keyboard = [
        "inline_keyboard" => [
            [
                ["text" => "VER MAIS 🔥", "callback_data" => "ver_mais"]
            ]
        ]
    ];

    sendMessage($chat_id, "Olha essas garotas 😈", $keyboard);
}

// =====================
// CALLBACK BUTTONS
// =====================
if ($callback) {

    $chat_id = $callback["message"]["chat"]["id"];
    $data = $callback["data"];

    if ($data === "ver_mais") {

        global $WELCOME_PHOTOS, $ADDITIONAL_PHOTOS;

        foreach ($WELCOME_PHOTOS as $photo) {
            sendPhoto($chat_id, $photo);
        }

        foreach ($ADDITIONAL_PHOTOS as $photo) {
            sendPhoto($chat_id, $photo);
        }

        $keyboard = [
            "inline_keyboard" => [
                [
                    ["text" => "PAGAR AGORA 💳", "url" => PAYMENT_LINK]
                ]
            ]
        ];

        sendMessage($chat_id, PAYMENT_MESSAGE, $keyboard);
    }
}
