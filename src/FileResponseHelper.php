<?php

declare(strict_types=1);

namespace F9Web;

use F9Web\Exceptions\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait helper to handle file responses.
 */
trait FileResponseHelper
{
    /** @var string|null $disk The disk to be used to get the files from */
    public ?string $disk;

    /**
     * Responds with a binary file to be displayed by the browser or downloaded.
     *
     * @param string $fileWithPath
     * @return Response
     * @throws FileNotFoundException
     */
    public function respondWithFile(string $fileWithPath): Response
    {
        $fileDisk = Storage::disk($this->disk ?? 'public');
        if (!$fileDisk->exists($fileWithPath)) {
            throw new FileNotFoundException();
        }
        $file = $fileDisk->get($fileWithPath);

        return new Response($file, Response::HTTP_OK, [
            'Content-Type'        => (new ExtensionMimeTypeDetector())->detectMimeType($fileWithPath, $file),
            'Content-Disposition' => sprintf("attachment; filename='%s'", basename($file)),
            'Content-Length'      => strlen($file),
        ]);
    }
}
