<!DOCTYPE html>
<html lang="en">

<head>

    <title>Travel Tax PDF</title>

    <style>
        * {
            font-size: 14px;
        }

        @page {
            size: letter;
        }

        body {
            size: letter;
        }

        @media print {
            body {
                padding: 70px 20px;
            }
        }
    </style>
</head>

<body>
    <div style="padding: 0px;">
        <div style="border: 1px solid dodgerblue; width: 100%; height: 750px; position: relative;">
            <img src="https://dashboard.philippines-hoho.ph/public/assets/img/travel_tax_assets/newteccert-01-01-r02.png"
                style="position: absolute; width: 100%; height: 100%; left: 0; top: 0; opacity: 0.6;" alt="">
            <img src="https://dashboard.philippines-hoho.ph/public/assets/img/travel_tax_assets/map1.png" alt=""
                style="position: absolute; bottom: 30px; right: 10px; width: 290px; z-index: 1; transform: rotate(0deg);">
            <img src="https://dashboard.philippines-hoho.ph/public/assets/img/travel_tax_assets/uplyt.png" alt=""
                style="width: 100%; position: absolute; left: 0; top: 0; z-index: 2;">
            <img src="https://dashboard.philippines-hoho.ph/public/assets/img/travel_tax_assets/bottomlyt.png"
                alt="" style="width: 100%; position:absolute; bottom: 0px; left: 0px; z-index: 3;">
            <img src="https://dashboard.philippines-hoho.ph/public/assets/img/travel_tax_assets/love.png"
                style="width: 120px; position:absolute; bottom: 60px; left: 40px;" alt="">

            <div style="position: relative; padding: 0px 40px;">
                {{-- AR NUMBER DIV --}}
                <h2 style="text-decoration: underline; text-align: center; margin-top: 60px;">ACKNOWLEDGEMENT RECEIPT
                </h2>
                <div style="margin-top: 40px;">
                    <div style="font-weight: bold;">AR NUMBER: <span>{{ $data['ar_number'] }}</span></div>
                    <div style="margin-top: 10px;">Hello <span
                            style="font-weight: bold;">{{ $data['passengers'][0]['firstname'] . ' ' . $data['passengers'][0]['lastname'] }}</span>
                    </div>
                    <div>Here is your complete payment details for <span style="color: rgb(105, 188, 255);">TIEZA Online
                            Payment</span></div>
                </div>

                {{-- CONTENT --}}
                <table width="100%" cellspacing="0px" cellpadding="0"
                    style="margin-top: 15px; z-index: 10; position:relative;">
                    <tbody>
                        <tr>
                            {{-- SUB CONTENT --}}
                            <td width="70%">
                                <table width="100%" style="width: 100%;" cellpadding="2">
                                    <thead>
                                        <tr>
                                            <th align="left" style="text-decoration: underline;">PASSENGER NAME</th>
                                            <th align="left" style="text-decoration: underline;">TICKET NUMBER</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['passengers'] as $passenger)
                                            <tr>
                                                <td>{{ $passenger['firstname'] . ' ' . $passenger['lastname'] }}</td>
                                                <td>{{ $passenger['ticket_number'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div style="margin-top: 10px; margin-left: 5px;">
                                    <div style="margin-top: 5px; font-size: 15px;">EMAIL ADDRESS:
                                        <span
                                            style="font-weight: 500;">{{ $data['passengers'][0]['email_address'] }}</span>
                                    </div>
                                    <div style="margin-top: 5px; font-size: 14px;">PAYMENT CHANNEL:
                                        <span
                                            style="font-weight: 500;">{{ $data['transaction']['aqwire_paymentMethodCode'] }}</span>
                                    </div>
                                    <div style="margin-top: 5px; font-size: 14px;">TRANSACTION REFERENCE NUMBER:
                                        <span style="font-weight: 500;">{{ $data['transaction_number'] }}</span>
                                    </div>
                                    <div style="margin-top: 5px; font-size: 14px;">DATE AND TIME:
                                        <span
                                            style="font-weight: 500;">{{ Carbon::parse($data['transaction_time'])->format('F d, Y h:i A') }}</span>
                                    </div>
                                    <div style="margin-top: 5px; font-size: 14px;">PAYMENT REFERENCE NUMBER:
                                        <span style="font-weight: 500;">{{ $data['reference_number'] }}</span>
                                    </div>
                                </div>
                                <table width="100%" style="width: 100%; margin-top: 14px;" cellpadding="2">
                                    <thead>
                                        <tr>
                                            <th align="left" style="text-decoration: underline;">PAYMENT SUMMARY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div style="margin-left: 20px;">
                                                    Processing/Convenience Fee:
                                                    <span>P {{ number_format($data['processing_fee'], 2) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div style="margin-left: 20px; margin-top: 5px;">
                                                    TOTAL AMOUNT DUE: <span>P
                                                        {{ number_format($data['total_amount'], 2) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>

                            {{-- QR CODE AND NAME WITH SIGNATURE OF MANAGER --}}
                            <td width="30%" align="center" style="z-index: 10; position: relative;">
                                <div style="margin-top: 40px;">
                                    <img src="data:image/png;base64, {{ $qrcode }} ">
                                    <div style="margin-bottom: 30px;"></div>
                                    <div style="font-weight: bold;">ATTY. BUMBO S. CRUZ</div>
                                    <div style="font-style: italic;">Manager</div>
                                    <div style="font-style: italic;">Travel Tax Department</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="position: relative; z-index: 10;">
                    <p style="font-size: 14px;">For question or concerns, you may email as at <a
                            href="traveltax.helpdesk@tieza.gov.ph">traveltax.helpdesk@tieza.gov.ph</a></p>
                    <p style="font-size: 14px;">Please print two (2) copies of this AR and present them at the airline
                        check-in counter before
                        boarding.</p>
                    <p>Sincerely, </p>
                    <p>TIEZA</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
