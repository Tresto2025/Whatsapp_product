<!DOCTYPE html>
<html>
<head>
    <title>{{ $doctor->title }} {{ $doctor->first_name }} {{ $doctor->last_name }} QR CODE</title>
    <style>
        .qr-logo.our-menu h2 {
            color: #fff;
            text-transform: uppercase;
            margin: 0;
            font-size: 18px;
        }
        .qr-logo.our-menu {
            background-color: #28bf96;
            padding: 20px 10px;
        }
        .div_bottom_line {
            border-top: 2px solid #ffffff94;
            margin: 0 auto;
            width: 65%;
            text-align: center;
        }
        .qr-code-h4 {
            font-weight: bold;
            color: #000;
            font-size: 15px;
            line-height: 22px;
            margin-bottom: 0;
            padding: 15px 35px;
        }
        .carrot-icon-up {
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 7px solid #28bf96;
            margin-top: 10px;
            width: 1px;
            position: relative;
            top: 6px;
            left: 46%;
        }
        .carrot-icon-bottom {
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 15px solid #28bf96;
            margin-bottom: 20px;
            width: 1px;
            position: relative;
            top: -3px;
            left: 44%;
        }
        .resto-name-qr {
            font-weight: bold;
            color: #28bf96;
            font-size: 18px;
            line-height: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .div_bottom_grey_line {
            border-top: 2px solid #000;
            margin: 0;
            width: 65%;
            text-align: center;
            display: inline-block;
        }
        .cusom_link_color {
            color: #000;
            margin-top: 5px;
        }
        .qr-code-image {
            border: 2px solid #28bf96;
            padding: 10px;
            display: inline-block;
            margin-left: 0px;
            margin-top: 0px;
        }
        .profession_type {
  font-weight: 400;
  font-size: 15px;
  color: #000;
}
    </style>
</head>

<body>
    <div class="qr-table" style="background-color:#fff; text-align: center; max-width: 288px; margin: 0 auto;border: 1px solid #28bf96;">

        <table style="text-align: center; border-collapse: collapse;" width="100%">
            <tbody>
                <tr>
                    <td align="center" colspan="2" class="qr-logo our-menu">
                        <h2>Book Appointment</h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <div class="carrot-icon-bottom"></div>
                    </td>
                </tr>
                @php
                $created = \Carbon\Carbon::parse($doctor->created_at);
                $dateCode = $created->format('dm');
                $doctorCode = $dateCode . $doctor->id;
            @endphp
                <tr align="center">
                    <td colspan="2" align="center">
                        <div class="qr-code-image">
<img src="data:image/png;base64, {!! base64_encode(
    QrCode::format('png')
        ->size(140)
        ->generate(
            'https://wa.me/919217443758?text=' . urlencode(
                "Hi, Dr. {$doctor->first_name} {$doctor->last_name} here.\n" .
                "To book an appointment with me just send this code: {$doctorCode}"
            )
        )
) !!}">


                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <h4 class="qr-code-h4" style="text-transform: uppercase; margin-top: 5px;margin-bottom: 20px;">
                            SCAN QR CODE TO BOOK YOUR APPOINTMENT!
                        </h4>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        @if($doctor->profile_image && $doctor->profile_image != 'users/default.png')
                            <!--<img src="{{ public_path($doctor->profile_image) }}" height="60px" style="margin-top: 5px; margin-bottom: 0;" />-->
                        @endif
                        <h3 class="resto-name-qr" style="margin: 0;">{{ ucwords(strtolower($doctor->title)) }} {{ ucwords(strtolower($doctor->first_name)) }} {{ ucwords(strtolower($doctor->last_name)) }}</h3>
                        <div class="div_bottom_grey_line" style="margin: 5px auto;"></div>
                        <h4 class="profession_type" style="margin: 0;position: relative;top: -5px;">{{ strtoupper($doctor->profession_type) }}</h4>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <div class="" style="margin: 5px auto;"></div>
                        <p style="font-size: 10px; margin-top: 0px; color: #515151;margin-right: 10px;margin-bottom: 5px;">
                            <span style="position: relative;top: 3px;bottom: 0px;margin-right: 5px;margin-bottom: 0px;">Powered by </span><img src="{{ public_path('uploads/logo/logo.png') }}" alt="logo" style="width:15%;position: relative;top: 5px;bottom: -5px;margin-bottom: 0px;">
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
