<!DOCTYPE HTML>
<html style="background-color: #d7d7d7; margin: 0; padding: 0;">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="format-detection" content="telephone=no">
    <title>Payment Status</title>
    <style>
        body {
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            background-color: #d7d7d7;
            margin: 0;
            padding: 0;
        }
        table {
            max-width: 600px;
        }
        td {
            color: #464646;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 16px;
            vertical-align: top;
        }
        table[bgcolor="#d7d7d7"] {
            background-color: #d7d7d7;
        }
        .text-primary {
            color: #F16522;
        }
        .text-success {
            color: #00b017;
        }
        .text-failed {
            color: #d2030a;
        }
        .text-cancel {
            color: #d2030a;
        }
        h1 {
            color: #F16522;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 30px;
            font-weight: 700;
            line-height: 34px;
            margin-bottom: 0;
            margin-top: 0;
        }
    </style>
</head>
<body bgcolor="#d7d7d7" class="generic-template">
<!-- Content Start -->
<table cellpadding="0" cellspacing="0" cols="1" bgcolor="#d7d7d7" align="center">
    <tr bgcolor="#d7d7d7">
        <td height="50"></td>
    </tr>
    <tr bgcolor="#d7d7d7">
        <td>
            <!-- Seperator Start -->
            <table cellpadding="0" cellspacing="0" cols="1" bgcolor="#d7d7d7" align="center" style="max-width: 600px; width: 100%;">
                <tr bgcolor="#d7d7d7">
                    <td height="30"></td>
                </tr>
            </table>
            <!-- Seperator End -->
            <table align="center" cellpadding="0" cellspacing="0" cols="3" bgcolor="white" class="bordered-left-right">
                <tr height="50"><td colspan="3"></td></tr>
                <tr align="center">
                    <td width="36"></td>
                    <td class="text-primary">
                        <img src="http://dgtlmrktng.s3.amazonaws.com/go/emails/generic-email-template/tick.png" alt="GO" width="50">
                    </td>
                    <td width="36"></td>
                </tr>
                <tr height="17"><td colspan="3"></td></tr>
                <tr align="center">
                    <td width="36"></td>
                    @if($pay_status == 'success')
                        <td><h1 class="text-success">Payment received</h1></td>
                    @elseif($pay_status == 'fail')
                        <td><h1 class="text-failed">Payment failed</h1></td>
                    @elseif($pay_status == 'cancel')
                        <td><h1 class="text-cancel">Payment cancel</h1></td>
                    @endif

                    <td width="36"></td>
                </tr>
                <tr align="left">
                    <td width="36"></td>
                    <td>
                        <p>Do not reference this page until redirect to app</p>
                    </td>
                </tr>

                <tr align="center" height="50">
                    <td width="36"></td>
                    <td class="text-primary">
                        <a href="{{route('pay-redirect')}}?payment_status=success">Go to App</a>
                    </td>
                    <td width="36"></td>
                </tr>

            </table>
            <!-- Seperator End -->
        </td>
    </tr>
</table>
<!-- Content End -->


</body>
</html>
