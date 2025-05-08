<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
<style>
    @page {
        /* margin: 2cm; */
        header: header;
        footer: footer;
    }

    .page-break {
        page-break-after: always;
    }

    .box {
        max-width: 800px;
        height: 100vh;
        margin: auto;
        font-size: 16px;
        line-height: 24px;
        font-family: Arial, sans-serif;
        color: #222;
        padding: 10px 0 10px 0;
    }

    #modal-table-header th {
        padding: 0 0.2em;
        vertical-align: middle;
        border-bottom: 0px;
        border-top: 0px;
        background-color: #2e2828ad;
        color: white;
        border: 1px solid white;
        text-align: center;
        white-space: nowrap;
        font-size: 12px;
    }

    #modal-table-body th {
        border-top: 0px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
        padding: 0;
        /* font-size: 10px; */

    }

    #modal-table-body td {
        border-top: 0px;
        border-bottom: 1px solid #dee2e6;
        background-color: #ebebeb;
        border: 1px solid #fcfdff;
        vertical-align: middle;
        font-size: 12px;
    }

    .item {
        font-size: 10px;
        color: #8a2432;
    }

    .text-red {
        color: #8a2432;
    }

    .gradient {
        background: linear-gradient(90deg, rgb(227 227 227) 20%, rgb(255 255 255) 85%);
    }

    .bt {
        border-bottom: solid 1px lightgray;
    }

    #modal-table-body td {
        vertical-align: top;
        padding: 0;
        white-space: nowrap;
    }

    .col-notes {
        width: 30%;
        font-size: 10px;
        color: #444;
        vertical-align: middle;
    }

    .col-contact {
        width: 28%;
        vertical-align: middle;
    }

    .col-logo {
        width: 12%;
        font-size: 16px;
        font-weight: bold;
        vertical-align: middle;
    }

    .col-branding {
        width: 5%;
        text-align: right;
        vertical-align: middle;
    }

    .contact {
        font-size: 12px;
        color: white;
        background-color: #8a2432;
        padding: 6px 12px;
        margin-bottom: 5px;
        display: inline-block;
        border-radius: 4px;
    }

    .contact-wrapper {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .branding .sda {
        font-size: 24px;
        font-weight: bold;
    }

    .branding .year {
        color: #8a2432;
        font-size: 14px;
    }

    .vl {
        border-left: 2px solid #8a2432;
        height: 50px;
        align-content: center;
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 2cm;
        background: white;
        text-align: center;
        padding: 10px 0;
        /* display: table-header-group; */
    }

    footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2.3cm;
        background: white;
        text-align: center;
        padding: 10px 0;
        /* display: table-footer-group; */
    }

    /* tr { page-break-after: always; } */
</style>

<html>

<body>
    <header name="page-header">
        {{-- <div style="text-align: center;">My Header</div> --}}
        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left">
            <tbody>
                <tr align="left">
                    <td width="33%" style="text-align: left;">
                        <img src="{{ public_path('assets/logo/logo-sda-global-24.svg') }}" width="auto" height="30">
                    </td>
                    <td width="61%" style="padding-left: 0; text-align: left;">
                        <div class="vl">
                            <p class="m-0 ms-3" style="font-weight: bold;"> PRICE LIST </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>
    <main>
        {!! $htmlContent !!}
    </main>
    <footer name="page-footer">
        <table id="print-footer" class="table" border="0">
            <tr>
                <td class="col-notes gradient">
                    <strong>Note:</strong><br>
                    1. Prices are subject to change without prior notice<br>
                    2. FOB Surabaya or Jakarta<br>
                    3. Valid until 30 September 2024
                </td>
                <td class="col-contact p-0 pe-3">
                    <div class="contact-wrapper">
                        <div class="contact">☎ Hotline +62 21 9900 8800</div>
                        <div class="contact">☎ WhatsApp +62 822 0000 8800</div>
                    </div>
                </td>
                <td class="col-logo p-0 pe-3">
                    <p class="m-0" style="font-size: 10px;">Online Store</p>
                    <img src="https://beta.sda.co.id/assets/img/toko-logo.png" alt="Toko SDA Logo" width="auto"
                        height="20"><br>
                </td>
                <td class="col-branding p-0">
                    <div class="branding">
                        <div class="d-flex" style="font-size: 8px;line-height: 1.5;">
                            <h1 class="m-0 me-2">SDA</h1>
                            <span class="vl"></span>
                            <h5 class="m-0 text-red ms-2">YEAR<br><b style="font-size: 22px;">2025</b></h5>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        {{-- <div style="text-align: center;">Page {PAGE_NUM} of {PAGE_COUNT}</div> --}}
    </footer>


</body>

</html>
