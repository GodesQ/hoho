<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        @media screen {
            @import url('https://fonts.googleapis.com/css2?family=Google+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        }

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: 'Google Sans', sans-serif;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            font-family: 'Google Sans', sans-serif;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* MOBILE STYLES */
        @media screen and (max-width:600px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
    <!-- HIDDEN PREHEADER TEXT -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- LOGO -->
        <tr>
            <td bgcolor="#6f0d00" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#6f0d00" align="center" style="padding: 0px 0px 0px 0px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top"
                            style="padding: 20px 20px 0px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 3px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <div style="width: 100%; padding: 50px 0; marin-right: auto; margin-left: auto; background: linear-gradient(160deg, rgba(67,7,9,1) 6%, rgba(161,30,3,1) 95%); border-radius: 10px;">
                                <img src="{{ URL::asset('assets/img/hoho_text_horizontal_white.png') }}" width="155" style="display: block; border: 0px;" />
                                <div style="font-size: 20px !important; color: #fff;">Thank You for Reservation!</div>
                            </div>
                            <div style="margin-top: 10px;">
                                <div style="font-size: 20px !important; color: #000; font-weight: 600; text-align: left !important; line-height: 25px;">{{ $details['reservation']->room->merchant->name ?? null }}</div>
                                <div style="font-size: 10px !important; color: #929292; text-align: left !important; line-height: 20px;">
                                    {{ date_format(new \DateTime($details['reservation']->checkin_date), 'F d, Y') }} until {{ date_format(new \DateTime($details['reservation']->checkout_date), 'F d, Y') }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center"
                            style="padding: 20px 20px 0px 20px; color: #666666; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;"></p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td bgcolor="#ffffff" align="center" style="padding: 10px 0;">
                                        <table border="0" cellspacing="5" cellpadding="10" width="90%"
                                            style="font-size: 13.5px; font-family: 'Google Sans', Helvetica, Arial, sans-serif;">
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Client Name
                                                </td>
                                                <td align="right"> 
                                                    {{ $details['reservation']->reserved_user->firstname . ' ' . $details['reservation']->reserved_user->lastname ?? null }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Room Name
                                                </td>
                                                <td align="right">
                                                    {{ $details['reservation']->room->room_name ?? null }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Number of Pax
                                                </td>
                                                <td align="right">
                                                    {{ $details['reservation']->number_of_pax ?? null }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Reference Number
                                                </td>
                                                <td align="right">
                                                    {{ $details['reservation']->reference_number ?? '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Payment Method
                                                </td>
                                                <td align="right">{{ $details['reservation']->aqwire_paymentMethodCode ?? 'Cash' }}</td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Sub Amount:
                                                </td>
                                                <td align="right">₱
                                                    {{ isset($details) ? number_format($details['reservation']->transaction->sub_amount, 2) : '0.00' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Discount:
                                                </td>
                                                <td align="right">₱
                                                    {{ isset($details) ? number_format($details['reservation']->transaction->total_discount, 2) : '0.00' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Additional Charges:
                                                </td>
                                                <td align="right">₱
                                                    {{ isset($details) ? number_format($details['reservation']->transaction->total_additional_charges, 2) : '0.00' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="font-weight: 800;">
                                                    Total Amount:
                                                </td>
                                                <td align="right">₱
                                                    {{ isset($details) ? number_format($details['reservation']->transaction->payment_amount, 2) : '0.00' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> <!-- COPY -->

                    <tr>
                        <td bgcolor="#ffffff" align="center"
                            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">If you have questions or concerns, you may visit:
                                https://www.facebook.com/philippineshoponhopoff or contact the HOHO Hotline at
                                0998-9008010 or 0998-9007700. Thank you!</p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center"
                            style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">Regards,<br>Philippine Hop On Hop Off </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px;">
                    <tr>
                        <td bgcolor="#6f0d00" align="center"
                            style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <h2 style="font-size: 20px; font-weight: 400; color: white; margin: 0;">Need more help?
                            </h2>
                            <p style="margin: 0;"><a href="#" target="_blank"
                                    style="color: white;">We&rsquo;re
                                    here
                                    to help you out.</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#f4f4f4" align="center"
                            style="padding: 0px 30px 30px 30px; color: #666666; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;">
                            <br>
                            <p style="margin: 0;">Powered by: <a href="https://philippines-hoho.ph/" target="_blank"
                                    style="color: #6f0d00; font-weight: 700;"><strong>Philippine Hop On Hop
                                        Off</strong></a>.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
