<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Item;
use App\Models\Borrow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AttachmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // Maksimal 10MB
            'attachable_type' => 'required|string|in:' . Item::class . ',' . Borrow::class,
            'attachable_id' => 'required|integer',
        ]);

        // Validasi bahwa model yang direferensikan ada
        $modelClass = $validated['attachable_type'];
        $model = $modelClass::find($validated['attachable_id']);

        if (!$model) {
            throw ValidationException::withMessages([
                'attachable_id' => ['Data yang direferensikan tidak ditemukan'],
            ]);
        }

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = new Attachment([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $model->attachments()->save($attachment);

        return response()->json($attachment, 201);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        // Hapus file dari storage
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json(null, 204);
    }
} 