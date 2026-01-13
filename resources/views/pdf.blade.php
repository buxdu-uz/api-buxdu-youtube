{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <title>Lessons PDF</title>--}}
{{--    <style>--}}
{{--        body { font-family: sans-serif; }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--<h1>Video dars hisoboti</h1>--}}

{{--<table border="1" cellpadding="10">--}}
{{--    <thead>--}}
{{--    <tr>--}}
{{--        <th>O`qituvchi</th>--}}
{{--        <th>Fan</th>--}}
{{--        <th>Mavzu</th>--}}
{{--        <th>Yaratgan vaqti</th>--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--    <tbody>--}}
{{--    @foreach ($data as $lesson)--}}
{{--        <tr>--}}
{{--            <td>{{ $lesson['teacher'] }}</td>--}}
{{--            <td>{{ $lesson['subject'] }}</td>--}}
{{--            <td>{{ $lesson['title'] }}</td>--}}
{{--            <td>{{ $lesson['created_at']->format('Y-m-d H:i:s') }}</td>--}}
{{--        </tr>--}}
{{--    @endforeach--}}
{{--    </tbody>--}}
{{--</table>--}}
{{--<h2>Scan QR Code to Open PDF</h2>--}}
{{--<img src="{{ $qrcode }}" alt="QR Code">--}}
{{--</body>--}}
{{--</html>--}}


    <!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma'lumotnoma</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url("{{ asset('fonts/DejaVuSans.ttf') }}") format('truetype');
        }


        body {
            font-family: 'DejaVu Sans', sans-serif;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .date-line {
            margin-bottom: 30px;
        }
        .main-text {
            text-align: justify;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            vertical-align: top;
        }
        .topic-title {
            font-weight: bold;
            font-size:16px;
        }
        .reference-link {
            color: blue;
            text-decoration: underline;
        }
        .reference-date {
            font-weight: 500;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 100px;
        }
        .department {
            max-width: 60%;
        }
        .signature {
            text-align: right;
        }
        .bottom-link {
            text-align: right;
            margin-top: 100px;
        }

        .footer{
            display:flex;
            align-items:center;
        }

        .footer table,
        .footer table tr td{
            border:none;
        }

        .footer table {
            width: 100%;
        }
        .footer td {
            vertical-align: middle;
            text-align: center;
            padding: 10px;
        }
        .footer .topic-title {
            text-align: left;
        }
        .footer .reference-date {
            text-align: right;
        }
        .footer .image-cell {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="title">MA'LUMOTNOMA №.{{ random_int(1,6) }}</div>
    <div class="date-line"><b>{{ date('Y-m-d H:i:s') }}</b></div>
</div>
<div class="main-text">
    Buxoro Davlat Universiteti <b>{{ $teacher->profile->department?->faculty?->name }}</b> fakulteti <b>{{ $teacher->profile->department?->name }}</b> dotsenti <b>{{ $teacher->full_name }}</b> tomonidan o'quv yuklamasida nazarda tutilganidan tashqari
    mustaqil ishlab chiqilgan hamda youtube platformasida quyidagi darslar
    joylashtirilgan.
</div>

<table>
    <tr>
        <th>Mavzu</th>
        <th>Havolasi</th>
        <th>Sana</th>
    </tr>
    @foreach($data as $lesson)
        <tr>
            <td class="topic-title">{{ $lesson['title'] }}</td>
            <td><a href="{{ $lesson['url'] }}" class="reference-link">{{ $lesson['url'] }}</a></td>
            <td class="reference-date">{{ $lesson['date'] }}</td>
        </tr>
    @endforeach
</table>

<div class="footer">
    <table>
        <tr>
            <td class="topic-title"> Raqamli ta'lim texnologiyalar<br>
                departamenti boshlig'i</td>
            <td>
                <img src="{{ $qrcode }}" width="130" height="130" alt="qr code" />
            </td>
            <td class="reference-date">M.Xusenov</td>
        </tr>
    </table>
</div>

{{--<div class="bottom-link">--}}
{{--    <a href="{{ $pdfURL }}" class="reference-link">Yuklab olish</a>--}}
{{--</div>--}}
</body>
</html>
