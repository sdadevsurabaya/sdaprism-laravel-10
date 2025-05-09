<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div id="element-to-print">
        <div class="box">
            <style type="text/css">
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

                .table thead th {
                    padding: 0;
                    vertical-align: middle;
                    border-bottom: 0px;
                    border-top: 0px;
                    background-color: #2e2828ad;
                    color: white;
                    border: 1px solid white;
                    text-align: center;
                }

                .table tbody th {
                    border-top: 0px;
                    border-bottom: 1px solid #dee2e6;
                    vertical-align: middle;
                    padding: 0;
                }

                .table tbody td {
                    border-top: 0px;
                    border-bottom: 1px solid #dee2e6;
                    background-color: #ebebeb;
                    border: 1px solid #fcfdff;
                    vertical-align: middle;
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

                td {
                    vertical-align: top;
                    padding: 10px;
                }

                .col-notes {
                    width: 30%;
                    font-size: 10px;
                    color: #444;
                    vertical-align: middle;
                }

                .col-contact {
                    width: 30%;
                    vertical-align: middle;
                }

                .col-logo {
                    width: 10%;
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
            </style>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left">
                <tbody>
                    <tr align="left">
                        <td width="33%" style="text-align: left;">
                            <img src="{{ asset('assets/logo/logo-sda-global-24.svg') }}" class="img-fluid mb-2"
                                style="width: calc(100px + 7vw);">
                        </td>
                        <td width="61%" style="padding-left: 0; text-align: left;">
                            <div class="vl">
                                <p class=" m-0 ms-3" style="font-weight: bold;"> PRICE LIST </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table" border="0">
                {{-- <thead>
                    <tr>
                        <th rowspan="2" colspan="2" width="35%">
                            <div class="border-0 gradient">
                                <p class="p-3 m-0 " style="text-align: left; color: black;">Brand</p>
                            </div>
                        </th>
                        <th rowspan="2">Weight<br>(Gr)</th>
                        <th colspan="2">UOM</th>
                        <th colspan="3">RBP (USD) </th>
                    </tr>
                    <tr>
                        <th>Box</th>
                        <th>Carton</th>
                        <th>Pcs</th>
                        <th>Bag / Box</th>
                        <th>Carton</th>
                    </tr>
                </thead> --}}
                <thead>
                    <th>Brand</th>
                    <th>Description</th>
                    <th>Part NO.</th>
                    <th>Price</th>
                </thead>
                <tbody>
                    <td>Sachio</td>
                    <td>Sachio</td>
                    <td>Sachio</td>
                    <td>Sachio</td>
                </tbody>
            </table>
            <table>
                <tr>
                    <td width="30%" class="col-notes gradient">
                        <strong>Note:</strong><br>
                        1. Prices are subject to change without prior notice<br>
                        2. FOB Surabaya or Jakarta<br>
                        3. Valid until 30 September 2024
                    </td>
                    <td width="30%" class="col-contact p-0 pe-3" >
                        <img src="{{ asset('assets/logo/telpon.png') }}" class="img-fluid"  >
                    </td>
                    <td width="50%" class="col-logo p-0 pe-3">
                        <img src="{{ asset('assets/logo/online-store.png') }}" class="img-fluid" style="max-width: 84px;">
                        <img src="https://beta.sda.co.id/assets/img/toko-logo.png" alt="Toko SDA Logo"
                            style="height: 20px;"><br>
                    </td>
                    <td width="5%" class="col-branding p-0">
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
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
