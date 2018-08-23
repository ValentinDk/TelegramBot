<?php

namespace AppBundle\Bot;

use React\EventLoop\Factory;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\TgLog;
use unreal4u\TelegramAPI\Telegram\Types\Update;

class BotForTelegram
{
    const BOT_TOKEN = '679771034:AAHInOXSaBWdJA6Gw9_sU1352Lb23A6ojyE';

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

    private function createResponse():void
    {
        $this->response = new SendMessage();
        $this->response->chat_id = $this->chatId;
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
                $this->response->text = "Насяльника всё сделаем";
                break;
        }
    }

    private function sendResponse():void
    {
        $loop = Factory::create();
        $tgLog = new TgLog(self::BOT_TOKEN, new HttpClientRequestHandler($loop));
        $tgLog->performApiRequest($this->response);
        $loop->run();
    }
}