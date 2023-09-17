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
            margin: 5rem 4rem;
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
        .body-container table tbody td {
            border-top: 1px solid gray !important;
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
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <h3 style="font-size: 40px; line-height: 15px;">Payment Summary</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h4>User Account Number: </h4>
                            </td>
                            <td style="text-align: right;">
                                {{ optional($transaction->user)->account_uid }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h4>Paymemt Method: </h4>
                            </td>
                            <td style="text-align: right;">
                                {{ $transaction->aqwire_paymentMethodCode }}
                            </td>
                        </tr>
                        <?php $additional_charges = json_decode($transaction->additional_charges) ?>
                        @foreach ($additional_charges as $propertyName => $propertyValue)
                            <tr>
                                <td>
                                    <h4>{{ $propertyName }}: </h4>
                                </td>
                                <td style="text-align: right;">
                                    ₱ {{ number_format($propertyValue, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>
                                <h4>Total Amount: </h4>
                            </td>
                            <td style="text-align: right;">
                                ₱ {{ number_format($transaction->payment_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>Total Amount Paid to Payment Gateway: </h3>
                            </td>
                            <td style="text-align: right;">
                                <h3>{{ $transaction->aqwire_totalAmount }}</h3>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </center>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.print();
        })
    </script>
</body>
</html>