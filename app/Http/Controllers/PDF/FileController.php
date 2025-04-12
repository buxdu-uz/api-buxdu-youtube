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
            'data' => 'required|array',
            'data.*.lesson_id' => 'required|exists:lessons,id'
        ]);

        $lessons = array();
        foreach ($request->data as $data)
        {
            $lesson = Lesson::query()
                ->find($data['lesson_id']);

            $lessons[] = [
                'teacher' => $lesson->teacher->full_name,
                'subject' => $lesson->subject->name,
                'title' => $lesson->title,
                'created_at' => $lesson->created_at,
            ];
        }

        $filename = 'documents/' . Str::uuid() . '.pdf';
        $pdfUrl = asset('storage/' . $filename); // Generate URL

        // Step 3: Generate QR Code with the URL (as base64 image)
        $qrCode = QrCode::size(100)->generate($pdfUrl);
        $qrCodeImage = 'data:image/png;base64,' . base64_encode($qrCode);


        // Render a Blade view as PDF
        $pdf = PDF::loadView('pdf', [
            'data' => $lessons,
            'qrcode' => $qrCodeImage
        ]);
        // Stream it in browser
        return $pdf->stream('invoice.pdf');
    }
}
