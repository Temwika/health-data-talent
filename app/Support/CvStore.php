<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * CVs live on the private "local" disk (storage/app/private), outside the web
 * root, under random names. They are only ever served through the admin
 * download route, never by URL.
 */
class CvStore
{
    /** Leading bytes of the file types we accept, keyed by extension. */
    private const SIGNATURES = [
        'pdf' => "%PDF",
        'docx' => "PK\x03\x04",
        'doc' => "\xD0\xCF\x11\xE0",
    ];

    /**
     * @return array{path: string, name: string, size: int, scan: string}
     */
    public function store(UploadedFile $file): array
    {
        $extension = Str::lower($file->getClientOriginalExtension());
        $signature = self::SIGNATURES[$extension] ?? null;
        $head = (string) file_get_contents($file->getRealPath(), false, null, 0, 4);

        if ($signature === null || ! str_starts_with($head, $signature)) {
            throw ValidationException::withMessages(['cv' => 'Upload a PDF or Word document.']);
        }

        $scan = $this->scan($file->getRealPath());
        if ($scan === 'infected') {
            throw ValidationException::withMessages(['cv' => 'This file failed our security check. Please upload a different copy.']);
        }

        return [
            'path' => $file->storeAs('cvs', Str::uuid().'.'.$extension, 'local'),
            'name' => Str::limit(preg_replace('/[^\w.\- ]+/u', '_', $file->getClientOriginalName()), 150, ''),
            'size' => $file->getSize(),
            'scan' => $scan,
        ];
    }

    /** @return 'clean'|'infected'|'unscanned' */
    private function scan(string $path): string
    {
        $binary = config('hdt.clamav_path');
        if (! $binary) {
            return 'unscanned';
        }

        try {
            // clamscan exits 0 when clean, 1 when a virus is found, 2 on error.
            $result = Process::timeout(60)->run([$binary, '--no-summary', $path]);

            return match ($result->exitCode()) {
                0 => 'clean',
                1 => 'infected',
                default => 'unscanned',
            };
        } catch (\Throwable $e) {
            Log::warning('CV malware scan failed', ['error' => $e->getMessage()]);

            return 'unscanned';
        }
    }
}
