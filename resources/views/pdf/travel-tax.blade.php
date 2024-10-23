<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Travel Tax PDF</title>

    <style>
        @page {
            size: letter;
        }

        body {
            size: letter;
        }
    </style>
</head>

<body>
    <div style="padding:  70px;">
        <div style="border: 1px solid dodgerblue; width: 100%; height: 600px; position: relative;">
            <img src="{{ URL::asset('assets/img/travel_tax_assets/newteccert-01-01-r02.png') }}"
                style="position: absolute; width: 100%; height: 100%; left: 0;top:0;" alt="">
            <img src="{{ URL::asset('assets/img/travel_tax_assets/uplyt.png') }}" alt=""
                style="width: 100%; position: absolute; left: 0; top: 0;">
            <img src="{{ URL::asset('assets/img/travel_tax_assets/bottomlyt.png') }}" alt=""
                style="width: 100%; position:absolute;bottom:0px;left:0px;">

            <div style="position: relative;">
                <h2 style="text-decoration: underline; text-align: center; margin-top: 60px;">ACKNOWLEDGEMENT RECEIPT
                </h2>
                <div></div>
            </div>
        </div>
    </div>
</body>

</html>
