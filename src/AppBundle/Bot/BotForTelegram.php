<?php

namespace AppBundle\Bot;

use React\EventLoop\Factory;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\TgLog;
use unreal4u\TelegramAPI\Telegram\Types\Update;
use unreal4u\TelegramAPI\Telegram\Types\KeyboardButton;
use unreal4u\TelegramAPI\Telegram\Types\ReplyKeyboardMarkup;
use unreal4u\TelegramAPI\Telegram\Methods\SendPhoto;
use unreal4u\TelegramAPI\Telegram\Types\Custom\InputFile;
use unreal4u\TelegramAPI\Telegram\Methods\SendSticker;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class BotForTelegram
{
    const BOT_TOKEN = '679771034:AAHInOXSaBWdJA6Gw9_sU1352Lb23A6ojyE';
    const PATH_PHOTO = 'D:\XAMPP\htdocs\Telegram_bot\src\AppBundle\Resources\file.jpg';
    const STICKER = 'CAADAgAD0QYAApb6EgXgMCLwDif1mQI';
    const BUTTON = 'Вот твои кнопки';

    private $chatId;
    private $response;

    /**
     * @param Update $update
     */
    public function run(Update $update):void
    {
        $this->chatId = $update->message->chat->id;
        $this->handler($update);
        $this->sendResponse();
    }

    /**
     * @param Update $update
     */
    private function handler(Update $update):void
    {
        $this->createResponse();

        switch ($update->message->text) {
            case "/start":
                $this->response->text = "Hallo";
                break;
            case "/help":
                $this->response->text = "Поможет только радикальное истребление";
                break;
            case "/setting":
                $this->response->text = "Тут нечего настраивать";
                break;
            case "/buttons":
                $this->createKeyboardButtons();
                break;
            case "Пришли фотографию":
                $this->createPhoto();
                break;
            case "Кинь стикер":
                $this->createSticker();
                break;
            case "/inline_keyboard":
                $this->createInlineKeyboardButtons();
                break;
            default:
                $this->response->text = "Не понимаю о чём вы";
        }
    }

    private function createResponse():void
    {
        $this->response = new SendMessage();
        $this->response->chat_id = $this->chatId;
    }

    private function createPhoto():void
    {
        $this->response = new SendPhoto();
        $this->response->chat_id = $this->chatId;
        $this->response->photo = new InputFile(self::PATH_PHOTO);
        $this->response->caption = 'Хотел - получи';
    }

    private function createSticker():void
    {
        $this->response = new SendSticker();
        $this->response->chat_id = $this->chatId;
        $this->response->sticker = self::STICKER;
    }

    private function createKeyboardButtons():void
    {
        $this->createResponse();
        $this->response->text = self::BUTTON;
        $this->response->reply_markup = new ReplyKeyboardMarkup();
        $this->response->reply_markup->one_time_keyboard = true;

        $button = new KeyboardButton();
        $button->text = 'Вперёд';
        $this->response->reply_markup->keyboard[0][] = $button;

        $button = new KeyboardButton();
        $button->text = 'Налево';
        $this->response->reply_markup->keyboard[1][] = $button;

        $button = new KeyboardButton();
        $button->text = 'Направо';
        $this->response->reply_markup->keyboard[1][] = $button;

        $button = new KeyboardButton();
        $button->text = 'Назад';
        $this->response->reply_markup->keyboard[2][] = $button;
    }

    private function createInlineKeyboardButtons():void
    {
        $this->createResponse();
        $this->response->text = self::BUTTON;

        $inlineKeyboard = new Markup(
            [
                'inline_keyboard' => [
                    [
                        ['text' => '1', 'callback_data' => 'k=1'],
                        ['text' => '2', 'callback_data' => 'k=2'],
                        ['text' => '3', 'callback_data' => 'k=3'],
                    ],
                    [
                        ['text' => '4', 'callback_data' => 'k=4'],
                        ['text' => '5', 'callback_data' => 'k=5'],
                        ['text' => '6', 'callback_data' => 'k=6'],
                    ],
                    [
                        ['text' => '7', 'callback_data' => 'k=7'],
                        ['text' => '8', 'callback_data' => 'k=8'],
                        ['text' => '9', 'callback_data' => 'k=9'],
                    ],
                    [
                        ['text' => '0', 'callback_data' => 'k=0'],
                    ],
                ]
            ]
        );
        $this->response->parse_mode = 'Markdown';
        $this->response->reply_markup = $inlineKeyboard;
    }

    private function sendResponse():void
    {
        $loop = Factory::create();
        $tgLog = new TgLog(self::BOT_TOKEN, new HttpClientRequestHandler($loop));
        $tgLog->performApiRequest($this->response);
        $loop->run();
    }
}