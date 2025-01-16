<?php

namespace App\Controller;

use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{

    /**
     * @Route("/slack/login", name="slack_login")
     * @throws RandomException
     */
    public function slackLogin(): Response
    {
        $state = bin2hex(random_bytes(16));
        $nonce = bin2hex(random_bytes(16));
        $redirect_uri = urlencode('https://mesa.iwa.web.tr/slack/callback');
        $team_id = $_SERVER['SLACK_TEAM_ID'];
        $client_id = $_SERVER['SLACK_CLIENT_ID'];

        //store state and nonce in cookie
        setcookie('slack_state', $state, time() + 3600, '/', '', false, true);
        setcookie('slack_nonce', $nonce, time() + 3600, '/', '', false, true);

        //redirect to slack login page
        error_log("https://slack.com/openid/connect/authorize?response_type=code&scope=openid%20profile&client_id={$client_id}&state={$state}&team={$team_id}&nonce={$nonce}&redirect_uri={$redirect_uri}");
        return $this->redirect("https://slack.com/openid/connect/authorize?response_type=code&scope=openid%20profile&client_id={$client_id}&state={$state}&team={$team_id}&nonce={$nonce}&redirect_uri={$redirect_uri}");
    }


    /**
     * @Route("/slack/callback", name="slack_callback")
     */
    public function slackCallback(Request $request): Response
    {
        return new Response(print_r($request->query->all(), true));
    }
}
