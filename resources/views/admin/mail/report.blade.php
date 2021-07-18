<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width" name="viewport"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Cabin" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        table,
        td,
        tr {
            vertical-align: top;
            border-collapse: collapse;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors=true] {
            color: inherit !important;
            text-decoration: none !important;
        }
    </style>
    <style id="media-query" type="text/css">
        @media (max-width: 625px) {

            .block-grid,
            .col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }

            .block-grid {
                width: 100% !important;
            }

            .col {
                width: 100% !important;
            }

            .col_cont {
                margin: 0 auto;
            }

            img.fullwidth,
            img.fullwidthOnMobile {
                max-width: 100% !important;
            }

            .no-stack .col {
                min-width: 0 !important;
                display: table-cell !important;
            }

            .no-stack.two-up .col {
                width: 50% !important;
            }

            .no-stack .col.num2 {
                width: 16.6% !important;
            }

            .no-stack .col.num3 {
                width: 25% !important;
            }

            .no-stack .col.num4 {
                width: 33% !important;
            }

            .no-stack .col.num5 {
                width: 41.6% !important;
            }

            .no-stack .col.num6 {
                width: 50% !important;
            }

            .no-stack .col.num7 {
                width: 58.3% !important;
            }

            .no-stack .col.num8 {
                width: 66.6% !important;
            }

            .no-stack .col.num9 {
                width: 75% !important;
            }

            .no-stack .col.num10 {
                width: 83.3% !important;
            }

            .video-block {
                max-width: none !important;
            }

            .mobile_hide {
                min-height: 0px;
                max-height: 0px;
                max-width: 0px;
                display: none;
                overflow: hidden;
                font-size: 0px;
            }
            .desktop_hide {
                display: block !important;
                max-height: none !important;
            }
        }
    </style>
</head>
<body>
<div>
    <div align="center">
        <div style="font:20pt Times New Roman"><b><span class="il">MarketFinexia</span> <span class="il">Pty</span> Limited</b>
        </div>
        <br>
        <table cellspacing="1" cellpadding="3" border="0" style="width: 65%">
            <tbody>
            <tr align="left">
                <td colspan="3" style="text-align: left">A/C No: <b>{{ $logins }}</b></td>
                <td colspan="4" style="text-align: left">Name: <b>{{ $name }}</b></td>
                <td colspan="2">Currency: <b>USD</b></td>
                <td colspan="2"  style="text-align: right"><b>{{ date('Y-m-d H:i:s') }}</b></td>
            </tr>

            <tr align="left">
                <td colspan="11"><b>Orders:</b></td>
            </tr>
            <tr align="center" bgcolor="#C0C0C0">
                <td nowrap="" align="left">&nbsp;&nbsp;&nbsp;Open Time</td>
                <td style="text-align: center;">Ticket</td>
                <td style="text-align: center;">Type</td>
                <td style="text-align: center;">Size</td>
                <td style="text-align: center;">Symbol</td>
                <td style="text-align: center;">Price</td>
                <td style="text-align: center;">S / L</td>
                <td style="text-align: center;">T / P</td>
                <td style="text-align: center;">Close Time</td>
                <td style="text-align: center;" colspan="3">Comment</td>
            </tr>
            @foreach($orders as $trade)
                <tr>
                    <td style="text-align: center;" >{{ $trade->Open_Time }}</td>
                    <td style="text-align: center;">{{ $trade->Ticket }}</td>
                    <td style="text-align: center;">{{ $trade->oBSFlag == 0 ? 'Sell' : 'Buy' }}</td>
                    <td style="text-align: center;">{{ $trade->Lot }}</td>
                    <td style="text-align: center;">{{ $trade->Symbol }}</td>
                    <td style="text-align: center;">{{  $trade->Close_Price }}</td>
                    <td style="text-align: center;">{{ $trade->Stop_Loss }}</td>
                    <td style="text-align: center;">{{ $trade->Target_Price }}</td>
                    <td style="text-align: center;">{{ $trade->Close_Time }}</td>
                    <td style="text-align: center;">{{ $trade->Comment }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="11" style="font:1pt arial">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="11">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="11" align="left">Best Regards<br>Accounts Department</td>
            </tr>
            </tbody>
        </table>
        <font face="tahoma,arial" size="1">
            <div style="font:7pt tahoma,arial">
                Please report to us within 24 hours if this statement is incorrect.
                Otherwise this statement will be considered to be confirmed by you.
            </div>
        </font>
        <div class="yj6qo"></div>
        <div class="adL">
        </div>
    </div>
    <div class="adL">
    </div>
</div>
</body>
</html>
