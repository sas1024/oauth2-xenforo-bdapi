<?php
namespace Sas1024\OAuth2\Client\Test\Provider;

use Sas1024\OAuth2\Client\Provider\XenForoBdApi;
use Mockery as m;
use ReflectionClass;

class XenForoBdApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XenForoBdApi
     */
    protected $provider;

    protected static function getMethod($name)
    {
        $class = new ReflectionClass(XenForoBdApi::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function setUp()
    {
        $this->provider = new XenForoBdApi([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'http://none',
            'baseUrl' => 'http://example.com',
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testGetBaseUrl()
    {
        $url = $this->provider->getBaseUrl();
        $this->assertEquals('http://example.com', $url);
    }

    public function testGetAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);

        $uri = parse_url($url);
        $this->assertEquals('oauth/token', $uri['query']);
    }

    public function testGetAccessToken()
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn('{"access_token":"mock_access_token", "token_type":"bearer"}');
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testExceptionThrownWhenErrorObjectReceived()
    {
        $message = uniqid();
        $status = rand(400, 600);
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn(' {"error":"' . $message . '"}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(1)
            ->andReturn($postResponse);
        $this->provider->setHttpClient($client);
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}