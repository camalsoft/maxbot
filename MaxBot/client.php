<?php
/**
 * MAX Bot Client для Max.ru платформы (PHP 7.4) by camalsoft
 * 
 * Telegram-подобный API клиент для работы с ботами в Max.ru
 * 
 * @author Kamil Mamyshev <info@in-event.ru>
 * @version 1.0
 * @license MIT
 * @link https://platform-api.max.ru
 */

namespace MaxBot\Camalsoft;

use Closure;

/**
 * Основной класс клиента для работы с MAX Bot API
 * 
 * Предоставляет Telegram-подобный интерфейс для обработки сообщений
 * и отправки ответов через MAX платформу
 */
class Client
{
    /** @var string Токен авторизации бота */
    protected $token;

    /** @var string Базовый URL API */
    protected $apiUrl = 'https://platform-api.max.ru';

    /** @var Closure[] Обработчики сообщений */
    protected $messageHandlers = [];

    /** @var Closure[] Обработчики команд */
    protected $commandHandlers = [];

    /**
     * Конструктор клиента
     * 
     * @param string $token Токен бота для авторизации
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /* =======================
     * Telegram-like API
     * ======================= */

    /**
     * Регистрирует обработчик всех входящих сообщений
     * 
     * @param Closure $handler Обработчик: function(Message $message, Client $client)
     */
    public function onMessage(Closure $handler)
    {
        $this->messageHandlers[] = $handler;
    }

    /**
     * Регистрирует обработчик конкретной команды
     * 
     * @param string $command Название команды (без слеша)
     * @param Closure $handler Обработчик: function(Message $message, Client $client)
     */
    public function onCommand($command, Closure $handler)
    {
        $this->commandHandlers[$command] = $handler;
    }

    /**
     * Основной обработчик webhook запросов
     * 
     * Должен вызываться на endpoint webhook'а бота
     */
    public function handle()
    {
        $input = file_get_contents('php://input');
        if (!$input) {
            http_response_code(400);
            return;
        }

        $update = json_decode($input, true);
        if (!is_array($update)) {
            http_response_code(400);
            return;
        }

        $message = Message::fromUpdate($update);
        if (!$message) {
            return;
        }

        // Обработка команд
        if ($message->isCommand()) {
            $command = $message->getCommand();
            if ($command && isset($this->commandHandlers[$command])) {
                call_user_func($this->commandHandlers[$command], $message, $this);
                return;
            }
        }

        // Обработка обычных сообщений
        foreach ($this->messageHandlers as $handler) {
            call_user_func($handler, $message, $this);
        }
    }

    /* =======================
     * MAX API Методы
     * ======================= */

    /**
     * Отправляет сообщение пользователю или в чат
     * 
     * @param Message $message Исходное сообщение
     * @param string $text Текст сообщения
     * @param string|null $format 'html' или 'markdown'
     * @return array|null Ответ API или null при ошибке
     */
    public function sendMessage($message, $text, $format = null)
    {
        $data = ['text' => $text];

        if ($format === 'html' || $format === 'markdown') {
            $data['format'] = $format;
        }

        $url = 'messages?';

        if (!empty($message->chat_id)) {
            $url .= 'chat_id=' . urlencode($message->chat_id);
        } elseif (!empty($message->from)) {
            $url .= 'user_id=' . urlencode($message->from);
        }

        return $this->request($url, $data);
    }

    /**
     * Отправка сообщения в чат (алиас для sendMessage)
     * 
     * @param Message $message Исходное сообщение
     * @param string $text Текст сообщения
     * @return array|null
     */
    public function sendMessageToChat($message, $text)
    {
        return $this->sendMessage($message, $text);
    }

    /**
     * Получает информацию о боте
     * 
     * @return array|null Информация о боте
     */
    public function getMe()
    {
        return $this->request('me');
    }

    /* =======================
     * HTTP Клиент
     * ======================= */

    /**
     * Выполняет HTTP запрос к MAX API
     * 
     * @param string $method API endpoint
     * @param array $data Данные запроса
     * @return array|null Ответ API
     */
    protected function request($method, array $data = [])
    {
        $url = $this->apiUrl . '/' . ltrim($method, '/');

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . $this->token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false || $httpCode !== 200) {
            return null;
        }

        return json_decode($result, true);
    }
}
