<?php
/**
 * MAX Bot Webhook Handler (PHP 7.4) by camalsoft
 * 
 * Главный файл для обработки webhook запросов от Max.ru
 * 
 * @author Kamil Mamyshev <info@in-event.ru>
 * @version 1.0
 * @license MIT
 */

// Подключение классов
require_once __DIR__ . '/MaxBot/client.php';
require_once __DIR__ . '/MaxBot/message.php';

use MaxBot\Camalsoft\Client;
use MaxBot\Camalsoft\Message;

// Инициализация бота (ЗАМЕНИТЕ НА СВОЙ ТОКЕН!)
$bot = new Client('YOUR_BOT_TOKEN_HERE');

/* =======================
 * ОБРАБОТЧИКИ КОМАНД
 * ======================= */

/**
 * Команда /start с поддержкой payload
 */
$bot->onCommand('start', function (Message $message, Client $bot) {
    $payload = $message->hasPayload() ? $message->getPayload() : '';
    
    if (!empty($payload)) {
        $bot->sendMessage($message, '🚀 Ух ты! Полезная нагрузка! Вот она: ' . $payload);
    } else {
        $bot->sendMessage($message, '👋 Добро пожаловать! Я бот от camalsoft!');
    }
});

/* =======================
 * ОБРАБОТЧИКИ СООБЩЕНИЙ
 * ======================= */

/**
 * Обработчик всех текстовых сообщений
 */
$bot->onMessage(function (Message $message, Client $bot) {
    $text = trim($message->text);
    
    if (strlen($text) > 1) {
        // Экранируем HTML символы для безопасного вывода
        $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $bot->sendMessage($message, "📝 Вы написали: <b>{$safeText}</b>", 'html');
    }
});

/* =======================
 * ОБРАБОТКА WEBHOOK
 * ======================= */

// Обрабатываем входящий webhook
$bot->handle();

/* =======================
 * ОТВЕТ MAX API (200 OK)
 * ======================= */
http_response_code(200);
header('Content-Type: text/plain; charset=utf-8');
echo 'OK';

?>
