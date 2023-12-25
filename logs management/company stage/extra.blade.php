@extends('layout/app')

@section('content')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css"> --}}


<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Logs records
                </h3>
                
            </div>
          
        </div>
        <div class="clearfix"></div>


<div class="row">
  
  <!-- form color picker -->
  <div class="col-md-12 col-sm-12 ">
    <div class="x_panel">
      <div class="x_title">


      <?php
if(isset($_GET["str1"]))
{
?>

<div class="alert alert-success" role="alert">
Course Updated successfully.
</div>
<?php
}
?>

<?php
if(isset($_GET["str2"]))
{
?>

<div class="alert alert-danger" role="alert">
Course Deleted successfully.
</div>
<?php
}
?>


        <h2>All Logs are here<small></small>

        </h2>
        <a href="{{route('laravel.log')}}" class="btn btn-danger btn-sm">Error and Exceptions</a>

      
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
          <div class="row">
              <div class="col-sm-12">
            <!--<a href="{{route('laravel.log')}}" class="btn btn-danger float-right">Error & Exeption Logs</a>-->

                <div class="card-box table-responsive">
           
                    <table id="example" >
                        <thead class="bg-dark text-light ">
                            <tr>
                                <th class="text-center">User</th>
                                <th class="text-center">Name/Email</th>
                                <th class="text-center">IP Address</th>
                                <th class="text-center">Type</th>
                                <th class="text-center" style="width:25%;">Section</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Timestamp</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody class="">
                            @foreach ($customerLogs as $logData)
                            <tr>
                                <td class="text-center " style="background-color: gray; color:white; font-weight:bold;">{{ $logData['user'] ?? '' }}</td>
                                <td class="text-center">{{ $logData['name'] ?? '' }}</td>
                                <td class="text-center" style="color: blue">{{ $logData['ipaddress'] ?? '' }}</td>
                                <td class="text-center">{{ $logData['type'] ?? '' }}</td>
                                <td class="text-center" >{{ $logData['section'] ?? '' }}</td>
                                <td class="text-center">{{ $logData['date'] ?? '' }}</td>
                                <td class="text-center">{{ $logData['time'] ?? '' }}</td>
                                <td>
                                    @if(is_array($logData['description'] ?? null))
                                    {{ json_encode($logData['description']) }}
                                @else
                                    {{ $logData['description'] ?? '' }}
                                @endif    
                                    {{-- {{ $logData['description'] ?? '' }} --}}
                                </td>
                               
                            </tr>
                        @endforeach
                    
                            {{-- @foreach ($otherUsersLogs as $log)
                                @php
                                    $logData = json_decode($log, true);
                                @endphp
                                <tr>
                                    <td>{{ $logData['user'] }}</td>
                                    <td>{{ $logData['name_email'] }}</td>
                                    <td>{{ $logData['type'] }}</td>
                                    <td>{{ $logData['section'] }}</td>
                                    <td>{{ $logData['description'] }}</td>
                                    <td>{{ $logData['date'] }}</td>
                                    <td>{{ $logData['timestamp'] }}</td>
                                </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                    
      
      </div>
      </div>
  </div>
</div>
    </div>
  </div>
    </div>
  </div>
  <!-- /form color picker -->

 

</div>
</div>
</div>
<!-- /page content -->

@endsection

@section("page-script")



          <script src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>

           <script src="https://cdn.datatables.net/1.11.1/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>

 
 <link href="https://cdn.datatables.net/1.11.1/css/dataTables.bootstrap4.min.css" rel="stylesheet">

 <script>
    $(document).ready(function(){
        $("#example").dataTable({
            "order": [[5, "desc"]] // Sort by the 6th column (timestamp) in descending order
        });
    });
    </script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script> --}}


<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            "ajax": "/get-data", // URL to fetch data
            "processing": true,
            "serverSide": true, // Enable server-side processing
            "columns": [
                { "data": "User" }, // Replace with actual column names
                { "data": "Name/Email" },
                { "data": "Name/Email" },
                { "data": "IP Address" },
                { "data": "Type" },
                { "data": "Section" },
                { "data": "Date" },
                { "data": "Timestamp" },
                { "data": "Description" },
                // { "data": "Name/Email" },
                // Add more columns as needed
            ]
        });
    });
    </script>
{{-- <script>

 $('.logstable').DataTable({
    ajax: '{{route('logs.data')}}',
    "order": [
      [0, 'desc']
  ],
  "displayLength": 25,
  fixedHeader: true
});    
    
</script> --}}
@endsection