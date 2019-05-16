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
                                <div class="top-bar-title padding-bottom">Customers</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total Sales</th>
                                <th>Total Profit</th>
                                <th>Margin %</th>

                                <th width="1%">{{ trans('message.table.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($products as $product){
                                if($product->description !=''){
                                    $profit = $product->total_sales - $product->total_purchase;
                                    if($product->total_purchase !=0){
                                        $margin = ($profit*100)/$product->total_sales;
                                    }
                                    else{
                                        $margin = 100;
                                    }
                                    ?>
                                        <tr>
                                            <td>{{ $product->description }}</td>
                                            <td>{{ $product->total_sales }}</td>
                                            <td>{{ round($profit,2) }}</td>
                                            <td>{{ round($margin,2) }}</td>
                                            <td>
                                                <a class="btn btn-xs btn-info" href='{{ url("edit-item/item-info/$product->stock_id") }}'><span class="fa fa-eye"></span></a> &nbsp;
                                            </td>
                                        </tr>
                                <?php } //end if
                            } // end foreach
                            ?>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.nav-tabs-custom -->
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
            $("#example1").DataTable({
                "order": [],
                "columnDefs": [ {
                    "targets": 4,
                    "orderable": false
                } ],

                "language": '{{Session::get('dflt_lang')}}',
                "pageLength": '{{Session::get('row_per_page')}}'
            });

        });

    </script>
@endsection