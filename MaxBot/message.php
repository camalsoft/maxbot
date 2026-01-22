<?php
/**
 * MAX Bot Message для Max.ru платформы (PHP 7.4) by camalsoft
 * 
 * Класс для представления входящих сообщений от MAX Bot API
 * 
 * @author Kamil Mamyshev <info@in-event.ru>
 * @version 1.0
 * @license MIT
 * @link https://platform-api.max.ru
 */

namespace MaxBot\Camalsoft;

/**
 * Класс для работы с входящими сообщениями MAX Bot API
 * 
 * Парсит webhook обновления и предоставляет удобный интерфейс
 * для работы с данными сообщения
 */
class Message
{
    /** @var string ID отправителя */
    public $from = '';

    /** @var string ID чата */
    public $chat_id = '';

    /** @var string Тип чата */
    public $chat_type = '';

    /** @var string Текст сообщения */
    public $text = '';

    /** @var array Исходные данные обновления */
    public $raw = [];

    /** @var mixed|null Payload данных (для /start команды) */
    public $payload;

    /**
     * Создает Message объект из webhook обновления
     * 
     * @param array $update Данные webhook запроса
     * @return Message|null Объект сообщения или null если невалидно
     */
    public static function fromUpdate(array $update)
    {
        $msg = new self();
        $msg->raw = $update;

        // Обработка команды bot_started
        if (isset($update['update_type']) && $update['update_type'] === 'bot_started') {
            $msg->chat_id = $update['chat_id'];
            $msg->from = isset($update['user']['user_id']) ? (string)$update['user']['user_id'] : '';
            $msg->text = '/start';
            $msg->payload = isset($update['payload']) ? $update['payload'] : null;
            return $msg;
        }

        // Обработка обычного сообщения
        if (!isset($update['message'])) {
            return null;
        }

        $message = $update['message'];

        // Парсинг текста сообщения
        $msg->text = isset($message['body']['text']) ? (string)$message['body']['text'] : '';

        // Парсинг отправителя
        $msg->from = isset($message['sender']['user_id']) ? (string)$message['sender']['user_id'] : '';

        // Парсинг получателя (чат)
        $msg->chat_id = isset($message['recipient']['chat_id']) ? $message['recipient']['chat_id'] : '';
        $msg->chat_type = isset($message['recipient']['chat_type']) ? $message['recipient']['chat_type'] : '';

        return $msg;
    }

    /**
     * Проверяет, является ли сообщение командой
     * 
     * @return bool true если сообщение начинается с "/"
     */
    public function isCommand()
    {
        $text = trim($this->text);
        return $text !== '' && substr($text, 0, 1) === '/';
    }

    /**
     * Извлекает название команды из сообщения
     * 
     * @return string|null Название команды (без слеша и аргументов) или null
     */
    public function getCommand()
    {
        if (!$this->isCommand()) {
            return null;
        }

        $text = trim($this->text);
        $spacePos = strpos($text, ' ');

        return $spacePos === false
            ? $text
            : substr($text, 0, $spacePos);
    }

    /**
     * Получает аргументы команды (все после первого пробела)
     * 
     * @return string Аргументы команды
     */
    public function getCommandArgs()
    {
        if (!$this->isCommand()) {
            return '';
        }

        $text = trim($this->text);
        $spacePos = strpos($text, ' ');

        return $spacePos === false
            ? ''
            : trim(substr($text, $spacePos + 1));
    }

    /**
     * Проверяет наличие payload (для /start команды)
     * 
     * @return bool
     */
    public function hasPayload()
    {
        return $this->payload !== null;
    }

    /**
     * Получает payload данные
     * 
     * @return mixed|null
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
