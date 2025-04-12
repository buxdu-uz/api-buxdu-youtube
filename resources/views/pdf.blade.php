<!DOCTYPE html>
<html>
<head>
    <title>Lessons PDF</title>
    <style>
        body { font-family: sans-serif; }
    </style>
</head>
<body>
<h1>Video dars hisoboti</h1>

<table border="1" cellpadding="10">
    <thead>
    <tr>
        <th>O`qituvchi</th>
        <th>Fan</th>
        <th>Mavzu</th>
        <th>Yaratgan vaqti</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($data as $lesson)
        <tr>
            <td>{{ $lesson['teacher'] }}</td>
            <td>{{ $lesson['subject'] }}</td>
            <td>{{ $lesson['title'] }}</td>
            <td>{{ $lesson['created_at']->format('Y-m-d H:i:s') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<h2>Scan QR Code to Open PDF</h2>
<img src="{{ $qrcode }}" alt="QR Code">
</body>
</html>
