<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <style>
        @page {
            margin: 100px 30px 100px 30px;
            /* header: page-header;
            footer: page-footer; */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            line-height: 20px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        /* .content {
            font-family: sans-serif;
            font-size: 14px;
        } */

        .page-number:after {
            content: counter(page);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        th,
        td {
            padding: 4px;
            vertical-align: top;
        }

        #modal-table-header th {
            background-color: #2e2828ad;
            color: white;
            border: 1px solid white;
            text-align: center;
            font-size: 12px;
            white-space: nowrap;
        }

        #modal-table-body td {
            border: 1px solid #ccc;
            background-color: #ebebeb;
            font-size: 12px;
            white-space: nowrap;
        }

        .contact {
            background-color: #8a2432;
            color: white;
            padding: 5px;
            border-radius: 4px;
            font-size: 12px;
        }

        .contact-wrapper {
            /* display: flex;
            flex-direction: column;
            gap: 5px; */
            vertical-align: justify;
        }

        .gradient {
            background: linear-gradient(90deg, #e3e3e3 20%, #fff 85%);
        }

        .text-red {
            color: #8a2432;
        }

        .vl {
            border-left: 2px solid #8a2432;
            height: 40px;
            display: inline-block;
            margin-left: 10px;
            margin-right: 10px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* Utility classes */
        .m-0 {
            margin: 0;
        }

        .ms-2 {
            margin-left: 0.5rem;
        }

        .ms-3 {
            margin-left: 1rem;
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .p-0 {
            padding: 0;
        }

        .pe-3 {
            padding-right: 1rem;
        }

        .pb-2 {
            padding-bottom: 0.6rem;
        }

        .pb-3 {
            padding-bottom: 0.9rem;
        }

        .d-flex {
            display: flex !important;
            /* align-items: center; */
        }
    </style>
</head>

<body>
    <header name="page-header">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left">
            <tbody>
                <tr align="left">
                    <td width="30%" style="text-align: left;">
                        <img src="{{ public_path('assets/logo/logo-sda-global-24.svg') }}" class="img-fluid mb-2"
                            style="width: calc(100px + 10vw);">
                    </td>
                    <td width="61%" style="padding-left: 0; text-align: left;">
                        <div class="vl">
                            <div class="pb-2"></div>
                            <p class=" m-0 ms-3" style="font-weight: bold;"> PRICE LIST </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>
    <footer name="page-footer">
        <table>
            <tr>
                <td class="col-notes gradient ">
                    <strong>Note:</strong><br>
                    1. Prices are subject to change without prior notice<br>
                    2. FOB Surabaya or Jakarta<br>
                    3. Valid until 30 September 2024
                </td>
                <td class="col-contact p-0 pe-3" style="vertical-align: center;">
                    {{-- <div class="contact-wrapper"> --}}
                        <div class="contact">☎ Hotline +62 21 9900 8800</div>
                        <div class="pb-2"></div>
                        <div class="contact">☎ WhatsApp +62 822 0000 8800</div>
                    {{-- </div> --}}
                </td>
                <td class="col-logo p-0 pe-3" style="vertical-align: center;">
                    <div class="pb-3"></div>
                    <p class="m-0" style="font-size: 10px;">Online Store</p>
                    <img src="{{ public_path('assets/logo/toko-logo.png') }}" alt="Toko SDA Logo"
                        style="height: 20px;"><br>
                </td>
                <td class="col-branding p-0">
                    <div class="branding">
                        <div class="d-flex" style="flex-direction: row; font-size: 8px;line-height: 1.5;">
                            <h1 class="m-0">SDA</h1>
                            <div class="vl">
                                <h5 class="m-0 text-red ms-2" style="font-size: 20px;">
                                    YEAR<br><b>2025</b>
                                </h5>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </footer>

    <div class="content">
        {!! $htmlContent !!}
    </div>
</body>

</html>
