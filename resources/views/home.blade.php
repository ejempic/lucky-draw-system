@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Lucky Draw</div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="prize">Prize Type </label>
                            <select class="form-control" id="prize">
                                @foreach($prizes as $prize)
                                    <option value="{{$prize->id}}">{{$prize->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prize">Generate Randomly</label>
                            <select class="form-control" id="random">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group disabled" id="winning_number_div">
                            <label for="prize">Winning Number</label>
                            <input type="text" id="winning_number" class="form-control" disabled >
                        </div>
                        <button class="btn btn-primary btn-block mt-4" onclick="draw()">Draw</button> <span class="draw_result"></span>
                        <br>
                        <br>
                        <h4>Winners</h4>
                        <div class="overflow-auto">

                            <table class="table table-bordered" id="winners_table">
                                <thead>
                                <tr>
                                    <th>Prizes</th>
                                    <th class="text-center">1st Winner</th>
                                    <th class="text-center">2nd Winner</th>
                                    <th class="text-center">3rd Winner</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($prizes as $prize)
                                <tr>
                                    <td>{{$prize->name}}</td>
                                    @foreach(range(0,2) as $index)
                                        @if(isset($prize->winner[$index]))
                                            <td class="text-center">
                                                {{$prize->winner[$index]['user']['name']}}: {{$prize->winner[$index]['number']['number']}}
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        <br>
                        <br>
                        <h4>Members</h4>
                        <div class="overflow-auto">

                            <table class="table table-bordered" id="members_table">
                                <thead>
                                <tr>
                                    <th>Users</th>
                                    @foreach(range(0,$highiest_number_quantity) as $index)
                                        <th class="text-center">Winner Number {{$loop->iteration}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr data-info="{{$user}}">
                                        <td>{{$user->name}}</td>
                                        @foreach(range(0,$highiest_number_quantity) as $index)
                                            @if(isset($user->numbers[$index]))
                                                <td class="text-center td_wnumber"><input type="text" class="input_wnumber"  data-id="{{$user->numbers[$index]['id']}}" value="{{$user->numbers[$index]['number']}}"></td>
                                            @else
                                                <td class="text-center bg-gray td_wnumber"><input type="text" class="input_wnumber" data-id="0"></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="99" class="text-center"><button class="btn btn-sm btn-block btn-primary" onclick="add_user()">Generate Random Member/User</button></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('styles')
    <style>
        .bg-gray{
            background-color: #c7c7c7;
        }
        .input_wnumber{
            background-color: transparent;
            border: none;
            border-bottom: 1px solid transparent;
            outline: none;
            text-align: center;
            width: 40px;
        }
        .input_wnumber:focus{
            border-bottom: 1px solid #ccc;
        }
        .td_wnumber:focus-within {
            background-color: transparent;
        }
    </style>
@endsection
@section('scripts')
    <script src="{{url('/js/jquery.inputmask.min.js')}}"></script>
    <script>
        $(document).on('keydown','.input_wnumber', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                save_number(this);
            }
        });
        $(document).on('click','.td_wnumber', function(e) {
            $(this).find('input').focus();
        });
        $(document).on('keyup','.input_wnumber', function(e) {
            if($(this).val().length==0){
                $(this).closest('td').addClass('bg-gray');
            }else{
                $(this).closest('td').removeClass('bg-gray');
                $(this).removeClass('text-danger');
            }
        });
        $(document).ready(function(){
            $('.input_wnumber').inputmask('9999', { "placeholder": " " });
            $('#winning_number').inputmask('9999', { "placeholder": " " });
        });
        $(document).on('change','#random',function(){
            $('#winning_number').prop('disabled',this.value==="1");
        });

        function save_number(input){
            var id = $(input).data('id'),user_id=$(input).closest('tr').data('info')['id'],number=$(input).val();

            if(number===''){
                $(input).removeClass('text-danger');
                $(input).blur();
            }
            $.ajax({
                url:"{{route('save_number')}}",
                data:{
                    id:id,
                    user_id:user_id,
                    number:number
                },
                success:function(response){
                    if(parseInt(response)===0){
                        $(input).addClass('text-danger');
                    }else if (parseInt(response)===3) {

                        $(input).removeClass('text-danger');
                        $(input).closest('td').addClass('bg-gray');
                        $(input).blur();
                    }else{
                        $(input).removeClass('text-danger');
                        $(input).closest('td').removeClass('bg-gray');
                        check_to_add_column(input);
                        $(input).blur();
                    }

                }
            });
        }
        function check_to_add_column(input){

            var current_number = $('#members_table thead tr th').length - 1;
            var tr = $(input).closest('tr');
            var inputs = tr.find('input');
            var value_count = 0;
            inputs.each(function(index,elem){
                if(elem.value != ''){
                    value_count++
                }
            });
            if(current_number === value_count){
                add_column()
            }


        }

        function add_column(){
            var current_number = $('#members_table thead tr th').length;
            $('#members_table thead tr').append("<th class='text-center'>Winner Number "+current_number+"</th>");
            $('#members_table tbody tr td.td_wnumber:last-child').each(function(td,elem){
                var user_id =$(elem).closest('tr').data('info');
                $(elem).after("<td class='text-center bg-gray td_wnumber'><input type='text' class='input_wnumber' data-id='0' data-user_id='"+user_id['id']+"'></td>");
            });
            $('.input_wnumber').inputmask('9999', { "placeholder": " " });
        }
        function add_user() {
            $.ajax({
                url:"{{route('generate_random_user')}}",
                success:function(response){
                    $('#members_table tbody tr:nth-last-child(2)').after($('#members_table tbody tr:nth-last-child(2)').clone());
                    $('#members_table tbody tr:nth-last-child(2)').attr('data-info',JSON.stringify(response))
                    $('#members_table tbody tr:nth-last-child(2) td:first-child').html(response.name)
                    $('#members_table tbody tr:nth-last-child(2)').find('input').val('')
                    $('#members_table tbody tr:nth-last-child(2)').find('input').data('id',0)
                    $('#members_table tbody tr:nth-last-child(2)').find('td:not(:first-child)').addClass('bg-gray')
                    $('.input_wnumber').inputmask('9999', { "placeholder": " " });
                }
            });

        }
        function draw(){
            $.ajax({
                url:"{{route('draw')}}",
                data:{
                    prize:$('#prize').val(),
                    random:$("#random").val(),
                    number:$("#winning_number").val()
                },
                success:function(response){
                    if(response.status==='error'){
                        $('.draw_result').html("<div class='alert alert-warning mt-3'><strong>Whooops!</strong> "+response.msg+"</div>");
                    }else{
                        console.log(response);
                        $('.draw_result').html("<div class='alert alert-success mt-3'><strong>Congratulations!</strong> "+response.msg+"</div>");
                    }


                }
            });

        }


    </script>
@endsection
