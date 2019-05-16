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
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Total Sales</th>
                                <th>Total Due</th>
                                <th>Total Profit</th>

                                <th width="1%">{{ trans('message.table.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->total_sales }}</td>
                                    <td>{{ $customer->total_sales - $customer->total_paid_amount }}</td>
                                    <td>{{ $customer->total_sales - $customer->total_purchase }}</td>
                                    <td>
                                        <a class="btn btn-xs btn-info" href='{{ url("customer/edit/$customer->debtor_no") }}'><span class="fa fa-eye"></span></a> &nbsp;
                                    </td>
                                </tr>
                            @endforeach
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