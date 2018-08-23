<?php

namespace AppBundle\Controller;

use AppBundle\Bot\BotForTelegram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use unreal4u\TelegramAPI\Telegram\Types\Update;

class BotController
{
    private $bot;

    public function __construct(BotForTelegram $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request):Response
    {
        $data = json_decode($request->getContent(), true);
        $update = new Update($data);

        $this->bot->run($update);

        return new Response();
    }
}