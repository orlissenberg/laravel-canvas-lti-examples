<?php

namespace App\Http\Controllers;

use App\Http\Requests\ToolProxyRegistrationRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\OAuth1\Client\Credentials\ClientCredentials;
use League\OAuth1\Client\Signature\HmacSha1Signature;

class LtiController extends Controller
{
    private $shared_secret = 'this_should_be_a_shared_secret';

    /**
     * Chapter 6.1 Establishing an Integration Contract:
     *
     * [TOOL Consumer] -> [POST] Tool Proxy Registration Request -> [TOOL Provider]
     * [TOOL Consumer] <- [GET] Tool Consumer Profile <- [TOOL Provider] (Chapter 5.2)
     * [TOOL Consumer] <- [POST] Register Tool Proxy <- [TOOL Provider]
     *
     * https://canvas-app.test/lti/register
     */
    public function register(ToolProxyRegistrationRequest $request)
    {
        $options = ['verify' => false];
        $toolConsumerProfileResponse = Http::withOptions($options)->get($request->tc_profile_url);
        $toolConsumerProfile = $toolConsumerProfileResponse->json();
        $service = $this->getToolProxyService($toolConsumerProfile);

        $endpoint = $service['endpoint'];
        $toolProxyConfiguration = $this->getToolProxyConfiguration($service, $request->tool_proxy_guid, $request->tc_profile_url);
        $auth = $this->createOAuthHeader($endpoint, $request->reg_key, $request->reg_password, $toolProxyConfiguration);
        $headers = ['Authorization' => $auth];

        $proxyResponse = Http::withOptions($options)
            ->withHeaders($headers)
            ->withBody($toolProxyConfiguration, 'application/vnd.ims.lti.v2.toolproxy+json')
            ->post($endpoint);

        $proxyData = json_decode($proxyResponse->body(), true);
        $queryParams = http_build_query([
            'tool_proxy_guid' => $proxyData['tool_proxy_guid'],
            'status' => 'success',
        ]);

        return redirect()->to($request->launch_presentation_return_url . '?' . $queryParams);
    }

    public function handleAssignment(): string
    {
        // https://laravel.com/docs/9.x/collections#available-methods
        $params = collect(request()->all())->map(fn($v, $k) => "$k: \"$v\"")->join('; ');

        $credentials = new ClientCredentials();
        $credentials->setSecret($this->shared_secret);

        $url = route('lti-handle-assignment');
        $oauthParams = request()->except('oauth_signature');
        $signature = (new HmacSha1Signature($credentials))->sign($url, $oauthParams);

        // See: app/controllers/lti/message_controller.rb:211
        // Evaluate: message.message_authenticator.base_string

        return 'handle-assignment<br/><br/>'
            . $signature . '<br/><br/>'
            . request('oauth_signature') . '<br/><br/>'
            . $params . '<br/><br/>'
            . (($signature == request('oauth_signature')) ? 'Valid' : 'Invalid');
    }

    protected function createOAuthHeader(string $endpoint, string $key, string $password, string $body): string
    {
        $params = [
            'oauth_version' => '1.0',
            'oauth_nonce' => '12345',
            'oauth_timestamp' => time(),
            'oauth_consumer_key' => $key,
            'oauth_body_hash' => sha1($body),
            'oauth_signature_method' => 'HMAC-SHA1',
        ];

        $credentials = new ClientCredentials();
        $credentials->setSecret($password);
        $signature = (new HmacSha1Signature($credentials))->sign($endpoint, $params);

        $params['oauth_signature'] = $signature;
        foreach ($params as $key => $value) {
            $params[$key] = urlencode($value);
        }

        $serializedParams = collect($params)->map(function ($value, $key) {
            return "$key=\"$value\"";
        })->join(",");

        // Identical validation that the Ruby OAuth lib is performing
        $parts = Str::of($serializedParams)->split('/[,=&]/');
        if (count($parts) % 2 !== 0) {
            throw new InvalidArgumentException();
        }

        return 'OAuth ' . $serializedParams;
    }

    protected function getToolProxyService(array $toolConsumerProfile): array
    {
        return collect($toolConsumerProfile['service_offered'] ?? [])
                ->filter(fn($item) => $item['format'][0] === 'application/vnd.ims.lti.v2.toolproxy+json')
                ->filter(fn($item) => $item['action'][0] === 'POST')
                ->first() ?? [];
    }

    protected function getToolProxyConfiguration(array $service, string $guid, string $ToolConsumerProfileUrl): string
    {
        return json_encode([
            '@context' => ['http://purl.imsglobal.org/ctx/lti/v2/ToolProxy'],
            '@type' => 'ToolProxy',
            '@id' => $service['@id'],
            'lti_version' => 'LTI-2p0',
            'tool_proxy_guid' => $guid,
            'tool_consumer_profile' => $ToolConsumerProfileUrl,
            'security_contract' => [
                // !!
                'shared_secret' => $this->shared_secret,
            ],
            'tool_profile' => [
                'lti_version' => 'LTI-2p0',
                'product_instance' => [
                    'guid' => $guid,
                    'product_info' => [
                        'product_version' => '1.0',
                        'product_name' => [
                            'default_value' => 'Acme Product',
                            'key' => 'tool.name',
                        ],
                        'product_family' => [
                            'code' => 'AcmeProductCode',
                            'vendor' => [
                                'code' => 'AcmeVendorCode',
                                'vendor_name' => [
                                    'default_value' => 'AcmeVendor',
                                    'key' => 'tool.vendor.name',
                                ],
                            ],
                        ],
                    ],
                ],
                'resource_handler' => [
                    [
                        'resource_name' => [
                            'default_value' => 'Acme Assignment',
                            'key' => 'assessment.resource.name',
                        ],
                        'resource_type' => [
                            'code' => 'urn:lti:ResourceType:acme.example.com/canvas-app/assignment',
                        ],
                        'description' => [
                            'default_value' => 'Acme Assignment',
                            'key' => 'assessment.resource.description',
                        ],
                        'message' => [
                            'message_type' => 'basic-lti-launch-request',
                            'path' => route('lti-handle-assignment'),
                            'enabled_capability' => [
                                'Canvas.assignment.id',
                                'Canvas.assignment.title',
                                'Canvas.assignment.pointsPossible',
                                'Canvas.assignment.unlockAt',
                                'Canvas.assignment.lockAt',
                                'Canvas.assignment.dueAt',
                                'Canvas.assignment.unlockAt.iso8601',
                                'Canvas.assignment.lockAt.iso8601',
                                'Canvas.assignment.dueAt.iso8601',
                                'Canvas.assignment.published',
                            ],
                        ],
                    ],
                ],
            ],
        ], true);
    }
}
