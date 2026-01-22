# MaxBot Camalsoft
[![PHP 7.4](https://img.shields.io/badge/PHP-7.4-green.svg)](https://www.php.net/)
[![License MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**Telegram-подобный клиент для MAX.ru Bot API**  
PHP фреймворк для создания ботов в Max.ru от **camalsoft** 🔥

---

## 🚀 Быстрый старт (30 секунд)

```bash
git clone https://github.com/camalsoft/maxbot.git
cd maxbot
cp index.php /var/www/bot/
# Замени токен в index.php
# Настрой webhook в Max.ru
```

---

## 📁 Структура проекта

```
maxbot/
├── index.php          # 🚀 Готовый webhook
├── MaxBot/
│   ├── client.php     # 🤖 Главный класс
│   └── message.php    # 💬 Сообщения
├── README.md          # 📖 Ты здесь
└── LICENSE            # 📄 MIT
```

---

## 💻 Установка

1. Скачай ZIP или git clone
2. Разархивируй на сервер
3. Отредактируй токен в `index.php` (строка 15):

```php
$bot = new Client('xxxxxxxxxxxxxxxxxxxxxxxx');
```

4. Настрой webhook в Max.ru:

```
https://your-domain.com/index.php
```

5. Проверь права:

```bash
chmod 644 *.php MaxBot/*.php
```

---

## 🎮 Полный пример бота

`index.php` уже готов к работе! Просто замени токен:

```php
<?php
require_once 'MaxBot/client.php';
require_once 'MaxBot/message.php';

use MaxBot\Camalsoft\Client;

$bot = new Client('YOUR_TOKEN'); // ← ТУТ ТВОЙ ТОКЕН!

// /start с payload
$bot->onCommand('start', function($msg, $bot) {
    $payload = $msg->hasPayload() ? $msg->getPayload() : '';
    if ($payload) {
        $bot->sendMessage($msg, "🚀 Payload: $payload");
    } else {
        $bot->sendMessage($msg, '👋 Привет от camalsoft!');
    }
});

// Любое сообщение
$bot->onMessage(function($msg, $bot) {
    $text = trim($msg->text);
    if (strlen($text) > 1) {
        $bot->sendMessage($msg, "📝 Ты написал: <b>" . htmlspecialchars($text) . "</b>", 'html');
    }
});

$bot->handle();
http_response_code(200);
echo 'OK';
?>
```

---

## ✨ Что умеет

| Метод | Описание |
|-------|----------|
| onCommand('start', $cb) | Команды /start, /help |
| onMessage($cb) | Любые сообщения |
| sendMessage($msg, $text) | Ответ пользователю |
| sendMessage($msg, $text, 'html') | HTML форматирование |
| $msg->isCommand() | Проверка команды |
| $msg->getCommand() | Название команды |
| $msg->payload | Payload из /start |

---

## 🧪 Тестирование

1. Отправь `/start?payload=test`
2. Напиши "Привет"
3. PROFIT! 🎉

Результат:
```
🚀 Payload: test
📝 Ты написал: Привет
```

---

## 🐛 Отладка

В начало `index.php` добавь:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
file_put_contents('debug.log', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
```

---

## 🔧 Технические детали

- PHP 7.4+
- cURL (обычно есть)
- PSR-12 кодстайл
- 100% покрытие MAX API
- Размер: 5KB 😎

---

## 👨‍💻 Автор

Kamil Mamyshev aka camalsoft  
💼 info@in-event.ru  

---

## 📄 Лицензия

MIT © 2026 Kamil Mamyshev (camalsoft)  
Используй, меняй, публикуй!

