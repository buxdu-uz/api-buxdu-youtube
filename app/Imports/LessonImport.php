<?php

namespace App\Imports;

use App\Domain\Lessons\Models\Lesson;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;

class LessonImport implements ToCollection, WithValidation
{
    public function collection(Collection $rows)
    {
        $userId = Auth::id();
        $now = now();
        $data = $rows->map(function ($row) use ($userId, $now) {
            return [
                'user_id' => $userId,
                'subject_name' => $row['subject_name'] ?? null,
                'title' => $row['title'] ?? null,
                'url' => $row['url'] ?? null,
                'date' => $row['date'] ?? null ?? $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        // Bulk insert with transaction for safety
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 1000) as $chunk) {
                Lesson::insert($chunk);
            }
        });
    }

    public function rules(): array
    {
        return [
            '*.subject_name' => 'required|string|max:255',
            '*.title' => 'required|string|max:255',
            '*.url' => 'required|url|max:255',
            '*.date' => 'required|date',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.subject_name.required' => 'Har bir qator mavzu nomi ustuni to\'ldirilishi shart',
            '*.title.required' => 'Har bir qator sarlavha ustuni to\'ldirilishi shart',
            '*.url.required' => 'Har bir qator url ustuni to\'ldirilishi shart',
            '*.url.url' => 'URL formati noto‘g‘ri',
        ];
    }
}
