<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function show($filename)
    {
        // Prevent path traversal and hidden file access
        if (str_contains($filename, '..') || str_starts_with($filename, '.') || str_contains($filename, '/.')) {
            abort(404);
        }

        $basePath = realpath(storage_path('app'));
        $path = storage_path('app/' . $filename);
        $realPath = realpath($path);

        if (!$realPath || !$basePath || !str_starts_with($realPath, $basePath) || is_dir($realPath) || !file_exists($realPath)) {
            abort(404);
        }

        $file = \File::get($realPath);
        $type = \File::mimeType($realPath);

        $response = \Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
}
