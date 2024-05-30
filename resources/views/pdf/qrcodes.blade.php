<!DOCTYPE html>
<html>

<head>
    <title>QR Codes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container">
        @foreach (collect($qrCodes)->chunk(2) as $chunk)
            <table width="100%" style="margin: 20px 0;">
                <tbody>
                    <tr>
                        @foreach ($chunk as $qrCode)
                            <td class="qr-code">
                                <img src="data:image/png;base64, {{ $qrCode }} ">
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>

</html>
