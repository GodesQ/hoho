<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        @media screen {
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        }

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
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
            <td bgcolor="#6f0d00" align="center" style="padding: 0px 10px 0px 0px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top"
                            style="padding: 20px 20px 0px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 3px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <img src="https://philippines-hoho.ph/philippines_hoho.3b7019f3d8ced762.jpg" width="125"
                                height="120" style="display: block; border: 0px;" />
                            <h1 style="font-size: 30px; font-weight: 800; margin-bottom: 0;">SLOT RESERVED <br>
                                {{ $details['name'] }}</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
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
                                    <td bgcolor="#ffffff" align="left" style="padding: 20px 20px 20px 20px;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <div style="margin-bottom: 1rem;">
                                                        <div>What: <span
                                                                style="margin-left: 10px">{{ $details['what'] }}</span>
                                                        </div>
                                                        <div>When: <span
                                                                style="margin-left: 10px">{{ $details['when'] }}</span>
                                                        </div>
                                                        <div>Where: <span
                                                                style="margin-left: 10px">{{ $details['where'] }}</span>
                                                        </div>
                                                        <br>
                                                        @if ($details['type'] == 'Guided')
                                                            <b><u>This serves as your appointment confirmation
                                                                    email.</u></b> However,
                                                            please understand that GUIDED TOURS is non refundable and
                                                            can only be rebooked 5 days prior schedule. Otherwise, it
                                                            will be deemed served in full. In case that you will need to
                                                            reschedule, please go to MANAGE BOOKING or contact our
                                                            customer service hotline.
                                                        @else
                                                            <b><u>This serves as your appointment confirmation
                                                                    email.</u></b> However,
                                                            please understand that day passes are valid for 24 hours
                                                            from first tap. In case that you will need to reschedule,
                                                            please go to MANAGE BOOKING or contact our customer service
                                                            hotline.
                                                        @endif
                                                        <br>
                                                        <b>DISCLAIMER:</b> Please understand that the purpose of this
                                                        appointment confirmation is to secure a passenger slot in
                                                        advance. It DOES NOT GUARANTEE that you will be able to be
                                                        accommodated at the exact time as scheduled because we still
                                                        have to prioritize regulating the number of visitors for
                                                        everyone’s safety. Rest assured, we are doing our best to
                                                        accommodate and serve all our passengers.
                                                        <br>
                                                        <b>Please take note of the following reminders before your
                                                            schedule:</b>
                                                        <br>
                                                        1. Download the Philippines Hop-On Hop-Off app and log in
                                                        details where the day pass was purchased
                                                        <br>
                                                        2. Bring a valid ID for possible additional verification
                                                        <br>
                                                        3. Be at the HOHO stops at the bus schedule found in our
                                                        official Facebook page or download the app for actual
                                                        geolocation of the bus where you can hop-on hop-off anytime at
                                                        any of our designated HOHO stops.
                                                        <br><br>
                                                        <div>
                                                            Sincerely,
                                                            <br>
                                                            Philippine Hop On Hop Off
                                                        </div>
                                                    </div>
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
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#6f0d00" align="center"
                            style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Google Sans', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <h2 style="font-size: 20px; font-weight: 400; color: white; margin: 0;">Need more help?
                            </h2>
                            <p style="margin: 0;"><a href="#" target="_blank" style="color: white;">We&rsquo;re
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
                            <p style="margin: 0;">Powered by: <a href="https://meritaclinic.ph/" target="_blank"
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
