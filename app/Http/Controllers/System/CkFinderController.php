<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CkFinderController extends Controller
{
    /**
     * Handle custom file upload from Dropzone, optimize image, and save it.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB Max
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => ['message' => $validator->errors()->first()]], 400);
        }

        $file = $request->file('upload');
        $targetDir = public_path('userfiles/images/');
        $filename = Str::random(12) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $targetPath = $targetDir . $filename;

        // Ensure the directory exists
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        try {
            list($width, $height, $type) = getimagesize($file->getRealPath());

            $maxWidth = 1600;
            $maxHeight = 1600;

            // Calculate new dimensions while maintaining aspect ratio
            $ratio = $width / $height;
            if ($width > $maxWidth || $height > $maxHeight) {
                if ($width / $maxWidth > $height / $maxHeight) {
                    $newWidth = $maxWidth;
                    $newHeight = $maxWidth / $ratio;
                } else {
                    $newHeight = $maxHeight;
                    $newWidth = $maxHeight * $ratio;
                }
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            $sourceImage = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($file->getRealPath());
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($file->getRealPath());
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($file->getRealPath());
                    break;
                case IMAGETYPE_WEBP:
                    $sourceImage = imagecreatefromwebp($file->getRealPath());
                    break;
                default:
                    return response()->json(['error' => ['message' => 'Unsupported image type.']], 400);
            }

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $quality = 80;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($resizedImage, $targetPath, $quality);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($resizedImage, $targetPath, 9); // PNG compression level (0-9)
                    break;
                case IMAGETYPE_GIF:
                    imagegif($resizedImage, $targetPath);
                    break;
                case IMAGETYPE_WEBP:
                    imagewebp($resizedImage, $targetPath, $quality);
                    break;
            }

            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            $publicUrl = '/userfiles/images/' . $filename;

            // Mimic CKFinder's JSON response for Dropzone success event
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $publicUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Image Upload Error: ' . $e->getMessage());
            return response()->json(['error' => ['message' => 'Could not process the uploaded image.']], 500);
        }
    }


    /**
     * Delete a file from the public directory.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filePath' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        $filePath = $request->input('filePath');
        $pathOnly = parse_url($filePath, PHP_URL_PATH);
        $sanitizedPath = '/' . ltrim(str_replace('\\', '/', $pathOnly), '/');
        $fullPath = public_path($sanitizedPath);

        $realBasePath = realpath(public_path());
        $realFilePath = realpath($fullPath);

        if ($realFilePath === false || !Str::startsWith($realFilePath, $realBasePath)) {
            return response()->json(['success' => false, 'message' => 'Invalid file path.'], 400);
        }

        if (File::exists($realFilePath) && File::isFile($realFilePath)) {
            try {
                File::delete($realFilePath);
                return response()->json(['success' => true, 'message' => 'File deleted successfully.']);
            } catch (\Exception $e) {
                Log::error('CKFinder File Deletion Error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error deleting file.'], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
    }
}

