<?php

namespace F9Web\ApiResponseHelpers\Tests;

use F9Web\Exceptions\FileNotFoundException;
use F9Web\FileResponseHelper;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ResponseFileTest extends TestCase
{
    protected object $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->getObjectForTrait(FileResponseHelper::class);
        $this->service->disk = 'reportslocal';
        $localDisk = Storage::fake('reportslocal');

        $localDisk->put(
            'dummy.pdf',
            file_get_contents('tests/fixtures/dummy.pdf')
        );

        $localDisk->put(
            'image.jpg',
            file_get_contents('tests/fixtures/image.jpg')
        );
    }

    /**
     * Test that a file response is valid.
     *
     * @dataProvider filesResponsesDataProvider
     */
    public function testValidFilesGenerateValidResponses(string $method, array $args, int $code, array $data): void
    {
        /** @var Response $response */
        $response = call_user_func_array([$this->service, $method], $args);
        self::assertNotNull($response);
        self::assertSame($code, $response->getStatusCode());
        self::assertSame($data['size'], strlen($response->getContent()));
        self::assertEquals($data['type'], $response->headers->get('Content-Type'));
        self::assertEquals($data['file'], $response->getContent());
    }

    /**
     * Test that the public disk will be used if no disk is used in the class that owns the trait.
     */
    public function testPublicDiskIsUsedIfNoDiskIsProvided(): void
    {
        $this->service->disk = null;
        $localDisk = Storage::fake('public');
        $dummyPdf = file_get_contents(__DIR__ . '/fixtures/dummy.pdf');

        $localDisk->put(
            'dummy.pdf',
            file_get_contents('tests/fixtures/dummy.pdf')
        );

            /** @var Response $response */
        $response = call_user_func_array([$this->service, 'respondWithFile'], ['dummy.pdf']);
        self::assertNotNull($response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(13264, strlen($response->getContent()));
        self::assertEquals('application/pdf', $response->headers->get('Content-Type'));
        self::assertEquals($dummyPdf, $response->getContent());
    }

    /**
     * If a file is not in the disk throw a 404 exception.
     *
     * @return void
     */
    public function testExceptionBeingThrownWhenFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);

        try {
            call_user_func_array([$this->service, 'respondWithFile'], ['GhostFile.pdf']);
        } catch (FileNotFoundException $notFoundException) {
            self::assertSame('File not found in the given disk.', $notFoundException->getMessage());
            self::assertSame(404, $notFoundException->getStatusCode());

            throw $notFoundException;
        }
    }

    /**
     * File Responses
     *
     * @return array[]
     */
    public function filesResponsesDataProvider(): array
    {
        $dummyPdf = file_get_contents(__DIR__ . '/fixtures/dummy.pdf');
        $dummyImage = file_get_contents(__DIR__ . '/fixtures/image.jpg');

        return [
            // Dummy PDF
            'testPdf'   => [
                'respondWithFile',
                ['dummy.pdf'],
                Response::HTTP_OK,
                [
                    'file' => $dummyPdf,
                    'size' => 13264,
                    'type' => 'application/pdf',
                ],
            ],
            // Test Image
            'testImage' => [
                'respondWithFile',
                ['image.jpg'],
                Response::HTTP_OK,
                [
                    'file' => $dummyImage,
                    'size' => 184817,
                    'type' => 'image/jpeg',
                ],
            ],
        ];
    }
}
