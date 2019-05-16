@extends('layouts.app')
@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="top-bar-title padding-bottom">
                                    Customer Ledger
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <span class="print-option">
                                    <a href="javascript:;" title="Print" onclick="PrintElem('#print')">
                                        <img src="{{ asset('public/img/printer.png') }}" alt="Print"/>
                                    </a>
                                </span>
                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-success" id="success_message" style="display: none"></div>
                                <div class="alert alert-danger" id="error_message" style="display: none"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-body">
                        <!-- /.box-header -->
                        <div>
                            <form id="search_form" method="post" action="{{ url('report/customer-ledger') }}">
                                {!! csrf_field() !!}
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <label for="start_date">Select Customer</label>
                                        <select class="form-control" name="customer_id" id="customer_id">
                                            <option value="">Select</option>
                                            <?php foreach($customers as $customer){?>
                                            <option value="{{ $customer->debtor_no }}" @if($customer->debtor_no == Session::get('customer_id')) selected @endif>{{ $customer->name }}</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <label for="start_date">Start Date</label>
                                        
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                              <i class="fa fa-calendar"></i>
                                            </div>
                                            <input class="form-control" type="text" name="start_date" id="start_date" value="{{ Session::get('start_date') }}" placeholder="Select Start Date">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <label for="end_date">End Date</label>
                                        
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                              <i class="fa fa-calendar"></i>
                                            </div>
                                            <input class="form-control" type="text" name="end_date" id="end_date" value="{{ Session::get('end_date') }}" placeholder="Select End Date">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2">
                                        <button type="button" class="btn btn-primary m-t-25" id="search_button">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        @if(isset($customer_details))
                        <div class="report_info">
                            <div class="row">
                                <div class="col-sm-4 col-sm-offset-4" style="text-align: center; margin-top: 15px; margin-bottom: 15px;">
                                    <strong>Name: {{ $customer_details->name }}</strong><br>
                                    Address: {{ $customer_details->address }}<br>
                                    Email: {{ $customer_details->email }}<br>
                                    Start Date: {{ date('d/m/Y', strtotime(Session::get('start_date'))) }}<br>
                                    End Date: {{ date('d/m/Y', strtotime(Session::get('end_date'))) }}
                                </div><br>
                            </div>
                        </div>
                        @endif

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Description</th>
                                <th class="text-center">Debit</th>
                                <th class="text-center">Credit</th>
                                <th class="text-center">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            /*Calculation B/F*/
                            $bf_balance = 0.00;
                            foreach($bf_sales as $bf_sale){
                                if($bf_sale->total !=0 && $bf_sale->total !=''){
                                    $bf_balance = $bf_balance+$bf_sale->total;
                                }
                                if($bf_sale->paid_amount !=0 && $bf_sale->paid_amount !=''){
                                    $bf_balance = $bf_balance-$bf_sale->paid_amount;
                                }
                                if($bf_sale->return_item_price !=0 && $bf_sale->return_item_price !=''){
                                    $bf_balance = $bf_balance-$bf_sale->return_item_price;
                                }
                            }
                            /*Calculation B/F ends*/
                            ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>B/F</td>
                                <td></td>
                                <td></td>
                                <td class="text-center">{{ $bf_balance }}</td>
                            </tr>
                            <?php
                            $balance = $bf_balance;
                            $total_debit = 0;
                            $total_credit = 0;
                            foreach($sales as $sale){
                                if($sale->total !=0 && $sale->total !=''){
                                $balance = $balance+$sale->total;
                                $total_debit = $total_debit+$sale->total;
                                    ?>
                                    <tr>
                                        <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                        <td><b><a href="{{URL::to('/')}}/invoice/view-detail-invoice/{{$sale->order_reference_id.'/'.$sale->order_no}}">{{ $sale->reference }}</a></b></td>
                                        <td>Sales.
                                            @foreach($sale->items as $key=>$item)
                                                {{ $item->description }}
                                                @if($key < count($sale->items)-1)
                                                    {{ ',' }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="text-center">{{ $sale->total }}</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">{{ $balance }}</td>
                                    </tr>
                                <?php }

                                if($sale->paid_amount !=0 && $sale->paid_amount !=''){
                                $balance = $balance-$sale->paid_amount;
                                $total_credit = $total_credit+$sale->paid_amount;
                                ?>
                                    <tr>
                                        <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                        <td><b><a href="{{URL::to('/')}}/invoice/view-detail-invoice/{{$sale->order_reference_id.'/'.$sale->order_no}}">{{ $sale->reference }}</a></b></td>
                                        <td>Payment received</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">{{ $sale->paid_amount }}</td>
                                        <td class="text-center">{{ $balance }}</td>
                                    </tr>
                                <?php }

                                if($sale->return_item_price !=0 && $sale->return_item_price !=''){
                                $balance = $balance-$sale->return_item_price;
                                $total_credit = $total_credit+$sale->return_item_price;
                                ?>
                                    <tr>
                                        <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                        <td><b><a href="{{URL::to('/')}}/invoice/view-detail-invoice/{{$sale->order_reference_id.'/'.$sale->order_no}}">{{ $sale->reference }}</a></b></td>
                                        <td>Item returned</td>
                                        <td class="text-center"></td>
                                        <td class="text-center">{{ $sale->return_item_price }}</td>
                                        <td class="text-center">{{ $balance }}</td>
                                    </tr>
                                <?php }
                             } ?>

                            <tr>
                                <td colspan="3"><strong>Total</strong></td>
                                <td class="text-center"><strong>{{ $total_debit }}</strong></td>
                                <td class="text-center"><strong>{{ $total_credit }}</strong></td>
                                <td class="text-center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.nav-tabs-custom -->
                    <div id="print" style="display: none;">
                        <style>
                        #print *{margin:0;padding:0}*{font-family:"Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif}img{max-width:100%}.collapse{margin:0;padding:0}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100% !important;height:100%}a{color:#2ba6cb} .btn{display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;font-weight: normal;line-height: 1.428571429;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;border: 1px solid transparent;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;-o-user-select: none;user-select: none;} p.callout{padding:15px;background-color:#ecf8ff;margin-bottom:15px}.callout a{font-weight:bold;color:#2ba6cb}table.social{background-color:#ebebeb}.social .soc-btn{padding:3px 7px;border-radius:2px; -webkit-border-radius:2px; -moz-border-radius:2px; font-size:12px;margin-bottom:10px;text-decoration:none;color:#FFF;font-weight:bold;display:block;text-align:center}a.fb{background-color:#3b5998 !important}a.tw{background-color:#1daced !important}a.gp{background-color:#db4a39 !important}a.ms{background-color:#000 !important}.sidebar .soc-btn{display:block;width:100%}table.head-wrap{width:100%}.header.container table td.logo{padding:15px}.header.container table td.label{padding:15px;padding-left:0}table.body-wrap{width:100%}table.footer-wrap{width:100%;clear:both !important}.footer-wrap .container td.content p{border-top:1px solid #d7d7d7;padding-top:15px}.footer-wrap .container td.content p{font-size:10px;font-weight:bold}h1,h2,h3,h4,h5,h6{font-family:"HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;line-height:1.1;margin-bottom:15px;color:#000}h1 small,h2 small,h3 small,h4 small,h5 small,h6 small{font-size:60%;color:#6f6f6f;line-height:0;text-transform:none}h1{font-weight:200;font-size:44px}h2{font-weight:200;font-size:37px}h3{font-weight:500;font-size:27px}h4{font-weight:500;font-size:23px}h5{font-weight:900;font-size:17px}h6{font-weight:900;font-size:14px;text-transform:uppercase;color:#444}.collapse{margin:0 !important}p,ul{margin-bottom:10px;font-weight:normal;font-size:14px;line-height:1.6}p.lead{font-size:17px}p.last{margin-bottom:0}ul li{margin-left:5px;list-style-position:inside}ul.sidebar{background:#ebebeb;display:block;list-style-type:none}ul.sidebar li{display:block;margin:0}ul.sidebar li a{text-decoration:none;color:#666;padding:10px 16px;margin-right:10px;cursor:pointer;border-bottom:1px solid #777;border-top:1px solid #fff;display:block;margin:0}ul.sidebar li a.last{border-bottom-width:0}ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p{margin-bottom:0 !important}.container{display:block !important;max-width:700px !important;margin:0 auto !important;clear:both !important; font-size: 15px;}#print .content{padding:0px;max-width:700px;margin:0 auto !important;display:block}#print .content table{width:100%}.column{width:300px;float:left}.column tr td{padding:15px}.column-wrap{padding:0 !important;margin:0 auto;max-width:600px !important}#print table{width:100%}.social .column{width:280px;min-width:279px;float:left}.clear{display:block;clear:both}@media only screen and (max-width:600px){a[class="btn"]{display:block !important;margin-bottom:10px !important;background-image:none !important;margin-right:0 !important}div[class="column"]{width:auto !important;float:none !important}table.social div[class="column"]{width:auto !important}}
                            .border-table{
                                border-collapse: collapse;
                            }
                            .border-table th{
                                text-align: left;
                                font-size: 13px;
                            }
                            .border-table th,.border-table td{
                                border: 1px solid #b9b9b9;
                                box-sizing: content-box;
                                padding: 5px;
                            }
                            i{font-size: 13px;}
                            #print table thead>tr>td, table thead>tr>th{font-weight: 600; font-size: 12px;}
                            table tbody>tr>td{font-size: 12px;}
                            #print .bg_grey, #print .bg_grey td, #print .bg_grey th{
                                background-color: #dedede; 
                            }
                        </style>

                        <table class="head-wrap" background="border.png"  width="100%;">
                            <tr>
                                <td class="header container" >
                                    <div class="content">

                                        @if(isset($companyData))
                                            <h6 class="collapse" style="text-align: center;font-size: 24px;margin-bottom: 10px !important;">{{ $companyData[1]->value }}</h6>
                                            <p class="collapse" style="text-align: center">
                                                {{ $companyData[4]->value }} <br>
                                                Mobile No:{{ $companyData[3]->value }}
                                            </p>
                                            <p class="collapse" style="text-align: center">
                                            Ledger report from {{ date('d/m/Y', strtotime(Session::get('start_date'))) }} To {{ date('d/m/Y', strtotime(Session::get('end_date'))) }}
                                            </p>
                                        @endif

                                        <table  style="width: 100%;">
                                            <tr>
                                                <td>
                                                    @if(isset($customer_details))
                                                        Name: {{ $customer_details->name }}<br>
                                                        Address: {{ $customer_details->address }}<br>
                                                        Email: {{ $customer_details->email }}<br>
                                                    @endif
                                                </td>
                                            
                                                <td align="right">
                                                     Printing date: {{ date('d/m/Y') }}<br>
                                                    Printing time: {{ date('h:i:s') }}<br>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table><!-- /HEADER -->

                        <div class="content container">
                            <table class="table border-table" width="100%;">
                                <thead class="bg_grey">
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>Description</th>
                                    <th style="text-align: center">Debit</th>
                                    <th style="text-align: center">Credit</th>
                                    <th style="text-align: center">Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                /*Calculation B/F*/
                                $bf_balance = 0.00;
                                foreach($bf_sales as $bf_sale){
                                    if($bf_sale->total !=0 && $bf_sale->total !=''){
                                        $bf_balance = $bf_balance+$bf_sale->total;
                                    }
                                    if($bf_sale->paid_amount !=0 && $bf_sale->paid_amount !=''){
                                        $bf_balance = $bf_balance-$bf_sale->paid_amount;
                                    }
                                    if($bf_sale->return_item_price !=0 && $bf_sale->return_item_price !=''){
                                        $bf_balance = $bf_balance-$bf_sale->return_item_price;
                                    }
                                }
                                /*Calculation B/F ends*/
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>B/F</td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: center">{{ $bf_balance }}</td>
                                </tr>
                                <?php
                                $balance = $bf_balance;
                                $total_debit = 0;
                                $total_credit = 0;
                                foreach($sales as $sale){
                                if($sale->total !=0 && $sale->total !=''){
                                $balance = $balance+$sale->total;
                                $total_debit = $total_debit+$sale->total;
                                ?>
                                <tr>
                                    <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                    <td>{{ $sale->reference }}</td>
                                    <td>Sales.
                                        @foreach($sale->items as $key=>$item)
                                            {{ $item->description }}
                                            @if($key < count($sale->items)-1)
                                                {{ ',' }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td style="text-align: center">{{ $sale->total }}</td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: center">{{ $balance }}</td>
                                </tr>
                                <?php }

                                if($sale->paid_amount !=0 && $sale->paid_amount !=''){
                                $balance = $balance-$sale->paid_amount;
                                $total_credit = $total_credit+$sale->paid_amount;
                                ?>
                                <tr>
                                    <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                    <td>{{ $sale->reference }}</td>
                                    <td>Payment received</td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: center">{{ $sale->paid_amount }}</td>
                                    <td style="text-align: center">{{ $balance }}</td>
                                </tr>
                                <?php }

                                if($sale->return_item_price !=0 && $sale->return_item_price !=''){
                                $balance = $balance-$sale->return_item_price;
                                $total_credit = $total_credit+$sale->return_item_price;
                                ?>
                                <tr>
                                    <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                    <td>{{ $sale->reference }}</td>
                                    <td>Item returned</td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: center">{{ $sale->return_item_price }}</td>
                                    <td style="text-align: center">{{ $balance }}</td>
                                </tr>
                                <?php }
                                } ?>

                                <tr class="bg_grey">
                                    <td colspan="3"><strong>Total</strong></td>
                                    <td style="text-align: center"><strong>{{ $total_debit }}</strong></td>
                                    <td style="text-align: center"><strong>{{ $total_credit }}</strong></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>

    </section>
    @include('layouts.includes.message_boxes')

@endsection

@section('js')
    <script type="text/javascript">

        $(function () {
            /*$("#example1").DataTable({
                "order": [],
                "paging": false,
                "searching": false,
                "ordering": false,
                "columnDefs": [ {
                    "targets": 4,
                    "orderable": false,
                } ],

                "language": '{{Session::get('dflt_lang')}}',
                "pageLength": '{{Session::get('row_per_page')}}'
            });*/

            $('#start_date, #end_date').datepicker();

        });

        function PrintElem(elem)
        {
            Popup($(elem).html());
        }

        function Popup(data)
        {
            var mywindow = window.open('', 'new div', 'height=400,width=600');
            mywindow.document.write('<html><head><title>Customer ledger</title>');
            /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
            mywindow.document.write('</head><body >');
            mywindow.document.write(data);
            mywindow.document.write('</body></html>');

            mywindow.print();
            mywindow.close();

            return true;
        }

        $(document).on('click','#search_button', function(){
            var customer_id = $('#customer_id').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var validate = '';

            if(customer_id==''){
                validate = validate+'Select a customer<br>'
            }
            if(start_date.trim()==''){
                validate = validate+'Start date required<br>'
            }
            if(end_date.trim()==''){
                validate = validate+'End date required<br>'
            }
            if(validate==''){
                $('#search_form').submit();
            }
            else{
                $('#success_message').hide();
                $('#error_message').show();
                $('#error_message').html(validate);
            }

        })

    </script>
@endsection