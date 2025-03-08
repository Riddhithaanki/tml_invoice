<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Invoice</title>
<head>

    <link rel="stylesheet" href="fontaw/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="fontaw/css/font-awesome.min.css">
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <script src="assets/vendor/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/jspdf.min.js"></script>
    <script src="assets/js//html2pdf.bundle.js"></script>
    <script type="text/javascript" src="assets/js/html2canvas.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function createPDF() {
            // Get the element.
            var element = document.getElementById('content');

            // Generate the PDF.
            html2pdf().from(element).set({
                margin: 0.2,
                filename: 'invoice-4.pdf',
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    orientation: 'portrait',
                    unit: 'in',
                    format: 'a4',
                    compressPDF: true
                }
            }).save();
        }
    </script>
    <style>
        @media print {

            .no-print,
            .no-print * {
                display: none !important;
            }
        }

    </style>
</head>

<body style="background-color: #fff;">

    <div>
        <div>
            <table id="content" width="100%"
                style="background-color: #fff;border:0px solid; max-width:900px;margin: 0 auto;height: 100%;"
                cellspacing="0" cellpadding="0">

                <tr>
                    <td style="height:30px; border-bottom:0px solid;" valign="top">
                        <h1 style="text-align:left; font-weight:bolder; margin-top:0px; margin-bottom:0px;">
                            <b>Invoice</b></h1>
                        <span style="text-align:left; margin-top:0px; margin-bottom:3px;">Tax Invoice</span>
                    </td>
                </tr>



                <tr>
                    <td width="100%">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="50%"
                                    style="border-top:0px solid; border-right:0px solid; padding-left:10px;"
                                    valign="top">
                                    <!-- Invoice Details -->

                                    <!-- Invoice Details -->
                                </td>
                                <td width="50%" style="text-align:right;border-top:0px solid; padding-left:0px;"
                                    valign="top">
                                    <!-- Invoice Other Details -->
                                    {{-- <!--{{ $vendor->vendor_logo_url }}--> --}}
                                    <img style="width: 140px;" src="data:image/png;base64,{{ base64_encode(file_get_contents($vendor->vendor_logo_url)) }}" alt="Logo"/>

                                    <!-- Invoice Other Details -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="100%"
                                    style="border-top:0px solid; height:25px; border-right:0px solid; color:#242424; text-align:left;">
                                    <b>{{$vendor->company_name ?? "" }}</b>, {{$vendor->address ?? "" }}
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="50%" style="border-top:0px solid; border-right:0px solid;" valign="top">

                                    <table>
                                        <tr>
                                            <td colspan="2" style="height:20px;"><b>Bill To</b></td>

                                        </tr>
                                        <tr>

                                            <td colspan="2">{{$invoiceData->name}}</td>
                                        </tr>
                                        <tr>
                                            <td style="height:20px;">ABN: </td>
                                            <td><b>{{$invoiceData->abnumber}}</b></td>
                                        </tr>
                                    </table>

                                </td>
                                <td width="50%" style="border-top:0px solid;text-align:right;" valign="bottom">

                                    <!--Shipped To Details -->

                                    <table width="100%">
                                        <tr>

                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td style="height:20px;">Invoice No:</td>
                                            <td>{{$invoiceData->invoice_id}}</td>
                                        </tr>
                                        <tr>
                                            <td style="height:20px;">Issue Date:</td>
                                            <td>{{$invoiceData->invoice_date}}</td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="35%"
                                    style="border-right: 1px solid #fff;height: 55px;color: #fff;text-align: left;background: #1EC4DA;padding-left: 10px;">
                                    <b>Account No:</b><br><span style="font-size: 100%;">10814265</span></td>
                                <td width="30%"
                                    style="border-right: 1px solid #fff;height: 55px;color: #fff;text-align: left;background: #1EC4DA;padding-left: 10px;">
                                    <b>BSB</b><br><span style="font-size: 100%;">063154</span></td>
                                <td width="35%"
                                    style="border-right: 0px solid #fff;height: 55px;color: #fff;text-align: left;background: #3a3a3a;padding-left: 10px;">
                                    <b>Total Due</b><br><span style="font-size: 100%;">${{$invoiceData->total}}</span></td>

                            </tr>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td width="100%">
                        <table class="table-heading" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #a9a8a8; border-top:0px solid; border-right:0px solid; height:24px; text-align:left;"
                                    width="3%"><b>No</b></td>
                                <td width="90%"
                                    style="border-bottom:1px solid #a9a8a8; border-top:0px solid; border-right:0px solid; text-align:left; padding-left:10px;height:20px;">
                                    <b>Description</b></td>
                                <td width="7%"
                                    style="border-bottom:1px solid #a9a8a8; border-top:0px solid; border-right:0px solid; text-align:center; height:20px;">
                                    <b>Amount($)</b></td>

                            </tr>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            @foreach ($invoice_details as $key=>$detail)
                            <tr>
                                <td style="border-bottom:1px solid #a9a8a8; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">{{ $key + 1 }}</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #a9a8a8; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; {{$detail->description}}</td>
                                <td width="7%"
                                    style="border-bottom:1px solid #a9a8a8; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                &nbsp; {{$detail->amount}}</td>

                            </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; height:20px; text-align:center;"
                                    width="3%">&nbsp;</td>
                                <td width="90%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:left; margin-left:10px; height:20px;">
                                    &nbsp; </td>
                                <td width="7%"
                                    style="border-bottom:1px solid #fff; border-top:0px solid; border-right:0px solid; text-align:right; margin-right:10px; height:20px;">
                                    &nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table class="" cellspacing="0" cellpadding="0" width="100%">
                            <tr>

                                <td width="90%"
                                    style="border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:left; height:30px;">
                                    <b>Subtotal</b></td>
                                <td width="10%"
                                    style="border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:right; height:30px;">
                                    <b>${{$invoiceData->sub_total}}</b></td>
                            </tr>
                            <tr>

                                <td width="90%"
                                    style="border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:left; height:30px;">
                                    <b>GST $</b></td>
                                <td width="10%"
                                    style="border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:right; height:30px;">
                                    <b>{{$invoiceData->gst_amount}}</b></td>
                            </tr>
                            <tr>


                                <td width="90%"
                                    style="border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:left; height:30px;">
                                    <span style="font-size:135%"><b>Total (AUD)</b></span></td>
                                <td width="10%"
                                    style="border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:right; height:30px;">
                                    <span style="font-size:135%"><b>${{$invoiceData->total}}</b></span></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table style="padding-top: 30px;" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td style="border-bottom:1px solid; border-top:0px solid; border-right:0px solid; height:20px; text-align:left;"
                                    width="33%"><b><i class="fa fa-user" aria-hidden="true"></i> Gaurav Soodan</b>
                                </td>
                                <td width="33%"
                                    style="border-bottom:1px solid; border-top:0px solid; border-right:0px solid; text-align:center; height:20px;">
                                    <b><i class="fa fa-phone" aria-hidden="true"></i> 042407525</b></td>
                                <td width="34%"
                                    style="border-bottom:1px solid; border-top:0px solid; border-right:0px solid; text-align:right; height:20px;">
                                    <b><i class="fa fa-paper-plane" aria-hidden="true"></i> {{$vendor->email ?? "" }}</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%">
                        <table style="padding-top: 3px;font-size: 12px;" cellspacing="0" cellpadding="0"
                            width="100%">

                            <tr>
                                <td style="vertical-align: top;border-bottom:0px solid; border-top:0px solid; border-right:0px solid; height:20px; text-align:left;"
                                    width="25%">{{$vendor->company_name ?? "" }} <br> {{$vendor->address ?? "" }} </td>
                                <td width="30%"
                                    style="vertical-align: top;border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:left; height:20px;">
                                    ACN: 609068797 <br>Account holder: <b>{{$vendor->company_name ?? "" }}</b></td>
                                <td width="45%"
                                    style="vertical-align: top;border-bottom:0px solid; border-top:0px solid; border-right:0px solid; text-align:right; height:20px;">
                                    Bank: <b>CommBank Australia</b> BSB <b>063154</b> Account No.: <b>10814265</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
</body>

</html>
