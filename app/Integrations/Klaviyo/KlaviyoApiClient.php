<?php

namespace App\Integrations\Klaviyo;

use App\Integrations\Klaviyo\Model\Event;
use App\Integrations\Klaviyo\Model\Profile;
use Illuminate\Http\Client\Factory;

/**
 * SuCw6W
 * Klaviyo HTTP API client.
 * @package App\Klaviyo
 */
class KlaviyoApiClient
{
    private const BASE_URL = 'https://a.klaviyo.com/api/';

    private $httpClient;
    private $token;

    public function __construct(Factory $httpClient)
    {
        $this->token = env('KLAVIYO_TOKEN');

        if (!$this->token) {
            throw new \RuntimeException('KLAVIYO_TOKEN doesn\'t provided. Check your .env file');
        }

        $this->httpClient = $httpClient;
    }

    /**
     * @param Event $event
     */
    public function track(Event $event)
    {
        $data = base64_encode(json_encode($this->mapEvent($event)));

        $this->httpClient
            ->get(self::BASE_URL . 'track', ['data' => $data]);
    }

    /**
     * @param Profile $profile
     */
    public function identify(Profile $profile)
    {
        $data = base64_encode(json_encode($this->mapProfile($profile)));

        $this->httpClient
            ->get(self::BASE_URL . 'identify', ['data' => $data]);
    }

    /**
     * @param Event $event
     * @return array
     */
    private function mapEvent(Event $event): array
    {
        return [
            'token' => $this->token,
            'event' => $event->getName(),
            'customer_properties' => $event->getCustomerProperties(),
            'properties' => $event->getProperties(),
            'time' => $event->getTime()
        ];
    }

    /**
     * @param Profile $profile
     * @return array
     */
    private function mapProfile(Profile $profile): array
    {
        return [
            'token' => $this->token,
            'properties' => [
                '$email' => $profile->getEmail(),
                '$first_name' => $profile->getFirstName(),
                '$last_name' => $profile->getLastName(),
                '$phone_number' => $profile->getPhoneNumber()
            ]
        ];
    }
}
