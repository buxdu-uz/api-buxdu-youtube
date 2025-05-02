<?php

namespace App\Imports;

use App\Domain\Lessons\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class LessonImport implements ToCollection,WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $userId = Auth::id();
        $now = now();

        $data = $rows->map(function ($row) use ($userId, $now) {
            // Access fields using snake_case version of camelCase headers
            $teacher = User::query()->where('employee_id_number', $row['hemis_id'])->first();
            if (!$teacher) return null;

            if (is_numeric($row['youtube_date'])) {
                try {
                    $youtubeDate = ExcelDate::excelToDateTimeObject($row['youtube_date'])->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $youtubeDate = $now;
                }
            }

            return [
                'user_id' => $userId,
                'teacher_id' => $teacher->id,
                'subject_name' => $row['subject'] ?? null,
                'title' => $row['title'] ?? null,
                'url' => $row['youtube_url'] ?? null,
                'date' => $youtubeDate ?? $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->filter()->toArray();

        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 1000) as $chunk) {
                Lesson::insert($chunk);
            }
        });
    }

    public function rules(): array
    {
        return [
            '*.hemis_id' => 'required|numeric|exists:users,employee_id_number',
            '*.subject' => 'required|string|max:255',
            '*.title' => 'required|string|max:255',
            '*.youtube_url' => 'required|string|url|max:255',
            '*.youtube_date' => 'required|date',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.title.required' => 'Har bir qator sarlavha ustuni to\'ldirilishi shart',
            '*.subject.required' => 'Har bir qator mavzu nomi ustuni to\'ldirilishi shart',
            '*.hemis_id.required' => 'Har bir qator hemis id ustuni to\'ldirilishi shart',
            '*.hemis_id.exists' => 'Ushbu hemis id bazada mavjud emas',
            '*.youtube_url.required' => 'Har bir qator url ustuni to\'ldirilishi shart',
            '*.youtube_url.url' => 'URL formati noto‘g‘ri',
            '*.youtube_date.required' => 'Har bir qator youtube date ustuni to\'ldirilishi shart',
            '*.youtube_date.date' => 'Sana formati noto‘g‘ri',
        ];
    }
}
