<?php

namespace App\Http\Controllers\PDF;

use App\Domain\Lessons\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FileController extends Controller
{
    public function generatePDF(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'data' => 'required|array',
            'data.*.lesson_id' => 'required|exists:lessons,id'
        ]);

        $teacher = User::query()->find($request->teacher_id);

        $lessons = array();
        foreach ($request->data as $data)
        {
            $lesson = Lesson::query()
                ->find($data['lesson_id']);

            $lessons[] = [
//                'subject' => $lesson->subject->name,
                'title' => $lesson->title ?? $lesson->subject_name,
                'url' => $lesson->url,
                'date' => $lesson->date,
                'created_at' => $lesson->created_at,
            ];
        }

        $filename = 'documents/files/pdf/' . Str::uuid() . '.pdf';
        $pdfPath = 'public/' . $filename;
        $pdfUrl = asset('storage/' . $filename);

// Save QR code as PNG to storage
        $qrCodePng = QrCode::size(200)->generate($pdfUrl);
        $qrCodePath = 'public/documents/files/qrcodes/' . Str::uuid() . '.png';
        Storage::put($qrCodePath, $qrCodePng);

// Generate base64 version for embedding into PDF
        $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCodePng);

// Create the PDF using Blade view
        $pdf = PDF::loadView('pdf', [
            'data' => $lessons,
            'qrcode' => $qrCodeBase64,
            'teacher' => $teacher,
            'pdfURL' => $pdfUrl
        ]);

// Save PDF to storage
        Storage::put($pdfPath, $pdf->output());
        // Stream it in browser
//        return $pdf->stream($teacher->full_name.'.pdf');
        return $this->successResponse('Generated successfully.',$pdfUrl);
    }
}
