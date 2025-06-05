<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Print Preview</title>
    <style>
        @page {
            margin: 100px 30px 100px 30px;
        }

        body {
            font-family: Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        header,
        footer {
            position: fixed;
            left: 0;
            right: 0;
            text-align: center;
        }

        header {
            top: -80px;
            height: 60px;
            line-height: 20px;
        }

        footer {
            bottom: -60px;
            height: 50px;
            font-size: 12px;
            color: #888;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* page-break-inside: auto; */
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
            background-color: #ffffff00;
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
            vertical-align: justify;
        }

        .gradient {
            background-image: url('{{ public_path('assets/img/bg-gradient.png') }}');
            background-repeat: no-repeat;
            background-size: cover;
            color: #222;
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

        p {
            margin: 0;
            padding: 0;
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
                            <p class=" m-0 ms-3" style="font-weight: bold; font-size:25px;"> PRICE LIST </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>
    <footer name="page-footer">
        <table border="0">
            <tr>
                <td width="45%" class="gradient">
                    <div class="gradient">
                        <strong>Note:</strong>
                        <br>
                        {!! $footer !!}

                        {{-- 1. Prices are subject to change without prior notice<br>
                        2. FOB Surabaya or Jakarta<br>
                        3. Valid until 30 September 2024 --}}
                    </div>
                </td>
                <td width="30%" style="vertical-align: middle;">
                    <img src="{{ public_path('assets/logo/telpon.png') }}" class="img-fluid" style="max-width:180px;">
                </td>
                <td width="23%" style="vertical-align: middle;">
                    <img src="{{ public_path('assets/logo/online-store.png') }}" class="img-fluid"
                        style="max-width: 74px;">
                    <img src="{{ public_path('assets/logo/toko-logo.png') }}" alt="Toko SDA Logo"
                        style="height: 20px;"><br>
                </td>
                <td width="20%" style="vertical-align: middle; text-align:right;">
                    <table>
                        <tr>
                            <td style="vertical-align: middle;">
                                <h5 class="m-0" style=" font-size: 25px; color:#222;">SDA</h5>
                            </td>
                            <td>
                                <h5 class="m-0 text-red"
                                    style=" padding-left: 10px; font-size: 20px; border-left: 2px solid #8a2432;">
                                    YEAR<br><b>2025</b>
                            </td>
                        </tr>
                    </table>
                </td>

            </tr>
        </table>
    </footer>

    <main>
        {{-- <div style="text-align: center; margin-bottom: 10px;">
            <img src="{{ public_path($logo) }}" width="50%">
        </div> --}}
        <div style="text-align: center; margin-bottom: 10px;">
            <img src="{{ public_path($logo) }}" class="img-fluid">
        </div>

        <table class="table" border="0">
            <thead id="modal-table-header">
                <tr>
                    <th>No</th>
                    @foreach ($header as $h)
                        @if ($h->id === 'price')
                            <th>{{ $h->label }} ({{ $currency }})</th>
                        @else
                            <th>{{ $h->label }}</th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody id="modal-table-body">
                @php
                    function formatCurrency($amount, $curr = 'Rp')
                    {
                        $amount = $amount ?? 0;
                        $decimals = $curr === 'Rp' ? 0 : 2;
                        $formatted =
                            $curr === 'Rp'
                                ? number_format($amount, $decimals, ',', '.')
                                : number_format($amount, $decimals, '.', ',');

                        return [
                            'symbol' => $curr,
                            'value' => $formatted,
                        ];
                    }
                @endphp
                @foreach ($body as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        @foreach ($header as $col)
                            @if ($col->id === 'price')
                                @php
                                    $formatted = formatCurrency($row[$col->id] ?? 0, $currency);
                                @endphp
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                        style="border: none !important;">
                                        <tr>
                                            <td
                                                style="text-align: left; width: 30%;border: none !important;margin: 0;padding: 0;">
                                                {{ $formatted['symbol'] }}</td>
                                            <td
                                                style="text-align: right; width: 70%;border: none !important;margin: 0;padding: 0;">
                                                {{ $formatted['value'] }}</td>
                                        </tr>
                                    </table>
                                </td>
                            @else
                                <td>{{ $row[$col->id] ?? '' }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>

</html>
