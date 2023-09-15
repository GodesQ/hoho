<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Transaction - {{ $transaction->reference_no }}</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
        @page {
            margin: 5rem 0;
        }
        .main-container {
            width: 760px;
        }
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .header-container div h4 {
            line-height: 5px;
        }
        .body-container {

        }
    </style>
</head>
<body>
    <center>
        <div class="main-container">
            <div class="header-container">
                <img src="https://philippines-hoho.ph/wp-content/uploads/2023/09/philippines_hoho_footer-768x769.jpg"
                                style="width:105px; border-radius: 5px;" alt="">
                <div style="text-align: center;">
                    <h2 style="font-size: 35px; color: blue; line-height: 10px;">Thank You!</h2>
                    <h4 style="line-height: 5px;">Payment Date: {{ date_format(new DateTime($transaction->payment_date), 'm/d/Y') }}</h4>
                    <h4 style="line-height: 5px;">Reference Number: {{ $transaction->reference_no }}</h4>
                </div>
            </div>
            <div class="body-container">
                <table width="100%"></table>
            </div>
        </div>
    </center>
</body>
</html>