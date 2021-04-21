<?php


namespace App\Tests;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Resident;

class ResidentTest extends ApiTestCase
{
    private static $client;
    private array $dataArray;
    const VIOLATION_ARRAY=[
        '@context' => '/contexts/ConstraintViolationList',
        '@type' => 'ConstraintViolationList',
        'hydra:title' => 'An error occurred'
    ];

    const API_URL = '127.0.0.1:8000/api/residents';

    /**
     * @before
     */
    public function Setup(): void
    {
        //Setup an array that contains information to create a resident account.
        $this->dataArray = [
            'email' => 'hello@test.com',
            'phone' => '3333333333',
            'profile' => [],
            'plainPassword' => '#4hs&3j2h',
        ];
    }

    /**
     * @test
     */
    public function TestCreateResidentAccount(): void
    {
        $response = static::createClient()->request('POST', self::API_URL, ['json' => [
            'email' => 'hello@test.com',
            'phone' => '3333333333',
            'profile' => [],
            'plainPassword' => '#4hs&3j2h',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Resident',
            '@type' => 'Resident',
            'email' => 'hello@test.com',
            'phone' => '3333333333',
            'profile' => []
        ]);
        $this->assertMatchesRegularExpression('~^/api/residents/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Resident::class);
    }


    /**
     * @test
     */
    public function TestCreateResidentAccountSuccessNoEmail(): void
    {
        $response = static::createClient()->request('POST', self::API_URL, ['json' => [
            'email' => '',
            'phone' => '3333333333',
            'profile' => [],
            'plainPassword' => '#4hs&3j2h',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Resident',
            '@type' => 'Resident',
            'email' => '',
            'profile' => [],
            'phone' => '3333333333'
        ]);
        $this->assertMatchesRegularExpression('~^/api/residents/\d+$~', $response->toArray()['@id']);

    }

    /**
     * @test
     */
    public function TestCreateResidentAccountSuccessNoPhoneNumber(): void
    {
        $response = static::createClient()->request('POST', self::API_URL, ['json' => [
            'email' => 'hello@test.com',
            'phone' => '',
            'profile' => [],
            'plainPassword' => '#4hs&3j2h',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Resident',
            '@type' => 'Resident',
            'email' => 'hello@test.com',
            'profile' => [],
            'phone' => ''
        ]);
        $this->assertMatchesRegularExpression('~^/api/residents/\d+$~', $response->toArray()['@id']);
    }

    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidEmailFormat(): void
    {
        $this->dataArray['email'] = 'hellotestcom';
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'email: The email is not a valid email.'
        ]);
    }

    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidEmailOver150Characters(): void
    {
        $this->dataArray['email'] = str_repeat('a', 142) . '@test.com';
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'email: Email has more than 150 characters.'
        ]);

        $this->dataArray['email'] = 'hello@test.com';
    }

    /**
     * @test
     */
    public function TestCreateResidentAccountValidEmail150Characters(): void
    {
        $this->dataArray['email'] = str_repeat('a', 141) . '@test.com';
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Resident',
            '@type' => 'Resident',
            'email' => str_repeat('a', 141).'@test.com',
            'profile' => [],
            'phone' => '3333333333'
        ]);
        $this->assertMatchesRegularExpression('~^/api/residents/\d+$~', $response->toArray()['@id']);

        $this->dataArray['email'] = 'hello@test.com';
    }



    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidPhoneUnder10Digits(): void
    {
        $this->dataArray['phone'] = str_repeat('3', 9);
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'phone: Phone needs to be 10 digits.'
        ]);

    }

    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidPhoneOver10Digits(): void
    {
        $this->dataArray['phone'] = str_repeat('3',11);
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'phone: Phone needs to be 10 digits.'
        ]);

        $this->dataArray['phone'] = str_repeat('3',10);

    }

    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidPasswordOver30Characters(): void
    {
        $this->dataArray['plainPassword'] = str_repeat('a', 31);
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'plainPassword: This value is too long. It should have 30 characters or less.'
        ]);

    }

    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidPasswordLessThan7Characters(): void
    {
        $this->dataArray['plainPassword'] = str_repeat('a', 6);
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'plainPassword: This value is too short. It should have 7 characters or more.'
        ]);

    }

    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidEmailPhoneEmpty(): void
    {
        //***Need to create a custom validator for this test***
        unset($this->dataArray['email']);
        unset($this->dataArray['phone']);

        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            "violations" => [
                ["propertyPath" => "email",
                    "message" => "Phone and Email cannot be both left blank. Only one is required."],
                ["propertyPath" => "phone",
                    "message" => "Phone and Email cannot be both left blank. Only one is required."],
            ]
        ]);
    }



    /**
     * @test
     */
    public function TestCreateResidentAccountInvalidPasswordEmpty(): void
    {

        unset($this->dataArray['plainPassword']);
        $response = static::createClient()->request('POST', self::API_URL, ['json' => $this->dataArray ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'plainPassword: Password should not be left blank.',
        ]);
    }

}