@extends('layouts.master')
@section('title', 'Home')
@section('content')
    <div class="container-fluid px-4 ">
        <h1 class="mt-4">Create Discounts</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Create Discounts</li>
        </ol>
        <input type="hidden" name="collections-data" data-collections="{{json_encode($data)}}" id="collections-data">
        <form id="createDiscounts">
            <div class="row mx-0">
                <div class="input-group mb-3">
                    <input type="number" class="form-control" placeholder="Enter amount" aria-label="Enteramount"
                        aria-describedby="basic-addon1" name="total_amount">
                </div>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" placeholder="Enter Discount's"
                        aria-label="Enter Discount's" aria-describedby="basic-addon2" name="discount_value">
                </div>

                <div class="input-group mb-3">

                    <select name="collection_id[]" id="collection" class="collection-dropdown  form-control" multiple>
                        @foreach($data as $value)
                            <option value="{{$value['id']}}">{{$value['title']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group mb-3">

                    <select name="condition" id="condition" class="form-dropdown form-control">
                        <option value="G">Getter then</option>
                        <option value="L">Less then</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Create</button>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    {{-- <th scope="col">#</th> --}}
                    <th scope="col">Title</th>
                    {{-- <th scope="col">Last</th>
                    <th scope="col">Handle</th> --}}
                    <th scope="col">Action</th> 
                </tr>
            </thead>
            <tbody id="discountsBody">
                <!-- @forelse ($allDiscounts as $item)
                    @if (!empty($item['node']['discount']))
                        @php $discount = $item['node']['discount'];@endphp 
                        @if ($discount['appDiscountType']['functionId'] == env('FUNCTION_ID'))
                            <tr>
                                {{-- <th scope="row"></th> --}}
                                <td>{{$discount['title']}}</td>
                                {{-- <td>Otto</td>
                                <td>@mdo</td> --}}
                                <td>
                                    <button type="button" class="btn btn-primary Edit" id="Edit" data-id="{{$discount['discountId']}}">Edit</button>
                                    <button type="button" class="btn btn-danger deleteButton" data-id="{{$discount['discountId']}}">Delete</button> 
                                </td>
                            </tr>
                        @endif
                    @endif
                @empty  
                @endforelse -->
            </tbody>
        </table>
        <div class="modal fade" id="EditdataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Discounts</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="updateDiscounts">
                            <input type="hidden" name="discountId" id="discountId">
                            <input type="hidden" name="metaId" id="metaId">
                            <div class="form-group">
                                <label for="amount" class="col-form-label">Total Amount</label>
                                <input type="number" id="amount" class="form-control" placeholder="Enter amount" aria-label="Enteramount" aria-describedby="basic-addon1" name="total_amount">
                            </div>
                            <div class="form-group">
                                <label for="discount" class="col-form-label">Discount Value</label>
                                <input type="number" id="discount" class="form-control" placeholder="Enter Discount's" aria-label="Enter Discount's" aria-describedby="basic-addon2" name="discount_value">
                            </div>

                            <div class="form-group">
                                <label for="collections" class="col-form-label">collections</label>
                                <select name="collection_id[]" id="collections" class="collection-dropdown editCollection form-control" multiple>
                                    @foreach($data as $value)
                                        <option value="{{$value['id']}}">{{$value['title']}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="condition" class="col-form-label">Conditions</label>
                                <select name="condition" id="condition" class="form-dropdown form-control">
                                    <option value="G">Getter then</option>
                                    <option value="L">Less then</option>
                                </select>
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="updateForm()">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            getDiscount();
        });
        const basurl =  $('meta[name="baseUrl"]').attr('content');
        const searchParams = new URLSearchParams(window.location.search);
        const shop = searchParams.get('shop');
        let collectionsData = JSON.parse($('#collections-data').attr('data-collections'));
        $('.collection-dropdown').select2({
            placeholder: 'Select an option',
            allowClear: true,
        });
        $('.form-dropdown').select2({
            dropdownParent: $('.container-fluid')
        });
        $(document).on('click','.Edit',function() {
            let id = $(this).attr('data-id');
            console.log("id => ", id);
            let selectedCollections = [];
            let selectedCondition = [];

            $.ajax({
                type: "GET",
                url: basurl+"/get-single-discount", // Replace with your server-side script
                data: {
                    "id": id,
                    "shop": shop
                },
                success: function(response) {
                    // location.reload();
                    console.log("response => ", response);
                    
                    if(response.status) {
                        // console.log("sff => ", response.singleData);
                        if(Object.keys(response.singleData).length > 0) {
                            // console.log("qqqqqq");
                            if(response.singleData.metafields.nodes.length > 0) {
                                // console.log("dggdg= > ", response.singleData,metafields.nodes[0].id);
                                let config = JSON.parse(response.singleData.metafields.nodes[0].value);
                                console.log("config => ", config);
                                $('#EditdataModal').find('#discountId').val(response.singleData.id);
                                $('#EditdataModal').find('#metaId').val(response.singleData.metafields.nodes[0].id);
                                $('#EditdataModal').find('#amount').val(config.total_amount);
                                $('#EditdataModal').find('#discount').val(config.discount_value);
                                $('#EditdataModal').find('#condition').val(config.condition).trigger("change");
                                $('#EditdataModal').find('.editCollection').val(config.collection_id).trigger("change");
                            }
                        }
                        $('#EditdataModal').modal('show');
                        getDiscount();
                    } else {
                        console.log("aaaa");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error("Error:", status, error);
                }
            });

        });


        function submitForm() {
            var formData = $("#createDiscounts").serialize();
            console.log('data',formData);
            // Send AJAX request using jQuery
            $.ajax({
                type: "POST",
                url: basurl+"/Createcollections", // Replace with your server-side script
                data: formData,
                success: function(response) {
                    // Handle the response here
                    console.log(response);
                    var obj = jQuery.parseJSON(response);
                    if(obj.data.discountAutomaticAppCreate.userErrors[0]){
                        if(obj.data.discountAutomaticAppCreate.userErrors[0].code==="MAX_APP_DISCOUNTS"){
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: obj.data.discountAutomaticAppCreate.userErrors[0].message,
                            });
                        }
                    }else{
                        Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Operation was successful!',
                            });
                    }
                    $('#createDiscounts').trigger("reset");
                    $('.collection-dropdown').val([]).trigger('change');
                    getDiscount();

                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error("Error:", status, error);
                }
            });
        }

        function updateForm() {
            var formData = $("#updateDiscounts").serialize() + "&shop="+shop;
            $.ajax({
                type: "POST",
                url: basurl+"/Createcollections",
                data: formData,
                success: function(response) {
                    location.reload();
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                }
            });
        }
     
        $(document).on('click','.deleteButton', function() {
            // Get the data-id attribute from the button
            var itemId = $(this).data('id');

            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, send an Ajax request to delete the item
                    $.ajax({
                        url: basurl+'/delete-item', // Replace with your actual delete endpoint
                        method: 'POST',
                        data: {
                            "id": itemId,
                            "shop": shop
                        },
                        success: function(response) {
                            // Handle the success response
                            Swal.fire(
                                'Deleted!',
                                'Your item has been deleted.',
                                'success'
                            );

                            getDiscount();
                        },
                        error: function(error) {
                            // Handle the error response
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the item.',
                                'error'
                            );
                        }
                    });
                }
            });
        });


        function getDiscount(){
            $.ajax({
            url: basurl+'/get-discount', // Replace with your server endpoint
            method: 'GET',
            data:{
                "shop": shop
            },
            dataType: 'json', // Set the expected data type
            success: function(response) {
                // Handle the success response
                console.log('Data received:', response);

                var allDiscounts = response.allDiscounts;
                var tableBody = $('#discountsBody');

                // Clear existing table rows
                tableBody.empty();

                if (allDiscounts.length > 0) {
                    // Iterate through the discounts and append rows to the table
                    var newRow = '';
                    $.each(allDiscounts, function(index, item) {
                        var discount = item.node.discount;
                        if (discount) {
                                if(discount.status == 'ACTIVE'){

                                    newRow += '<tr>' +
                                        '<td>' + discount.title + '</td>' +
                                        '<td>' +
                                        '<button type="button" class="btn btn-primary Edit" id="Edit" data-id="' + discount.discountId + '">Edit</button>' +
                                        '<button type="button" class="btn btn-danger deleteButton" data-id="' + discount.discountId + '">Delete</button>' +
                                        '</td>' +
                                        '</tr>';
                                }
                            }
                        });
                        tableBody.html(newRow);
                } else {
                    // Display a message if no records are found
                    var noRecordsRow = '<tr><td colspan="2">No records found</td></tr>';
                    tableBody.append(noRecordsRow);
                }
            },
        });
        }
    </script>
@endsection
