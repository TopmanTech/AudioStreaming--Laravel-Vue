<?php

namespace App\Services;

use App\Exceptions\MediaPathNotSetException;
use App\Exceptions\SongUploadFailedException;
use App\Models\Setting;
use App\Models\Song;
use Illuminate\Http\UploadedFile;

use function Functional\memoize;

class UploadService
{
    private const UPLOAD_DIRECTORY = '__AudioStreaming_UPLOADS__';

    public function __construct(private FileSynchronizer $fileSynchronizer)
    {
    }

    public function handleUploadedFile(UploadedFile $file): Song
    {
        $targetFileName = $this->getTargetFileName($file);
        $file->move($this->getUploadDirectory(), $targetFileName);

        $targetPathName = $this->getUploadDirectory() . $targetFileName;
        $result = $this->fileSynchronizer->setFile($targetPathName)->sync();

        if ($result->isError()) {
            @unlink($targetPathName);
            throw new SongUploadFailedException($result->error);
        }

        return $this->fileSynchronizer->getSong();
    }

    private function getUploadDirectory(): string
    {
        return memoize(static function (): string {
            $mediaPath = Setting::get('media_path');

            if (!$mediaPath) {
                throw new MediaPathNotSetException();
            }

            return $mediaPath . DIRECTORY_SEPARATOR . self::UPLOAD_DIRECTORY . DIRECTORY_SEPARATOR;
        });
    }

    private function getTargetFileName(UploadedFile $file): string
    {
        // If there's no existing file with the same name in the upload directory, use the original name.
        // Otherwise, prefix the original name with a hash.
        // The whole point is to keep a readable file name when we can.
        if (!file_exists($this->getUploadDirectory() . $file->getClientOriginalName())) {
            return $file->getClientOriginalName();
        }

        return $this->getUniqueHash() . '_' . $file->getClientOriginalName();
    }

    private function getUniqueHash(): string
    {
        return substr(sha1(uniqid()), 0, 6);
    }
}
