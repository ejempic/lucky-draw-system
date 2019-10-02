
<h4>Members</h4>
<div class="overflow-auto">
    <table class="table table-bordered" id="members_table">
        <thead>
        <tr>
            <th>Users</th>
            @foreach(range(0,$highiest_number_quantity) as $index)
                <th class="text-center">Winning Number {{$loop->iteration}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr data-info="{{$user}}" class="{{auth()->user()->hasRole('member') && (auth()->user()->id!=$user->id)?'not_yours':''}}">
                <td>
                    {{$user->name}}
                    <span class="badge badge-success">{{optional($user->winners)->prize['name']}}</span>

                </td>
                @foreach(range(0,$highiest_number_quantity) as $index)

                    @if(auth()->user()->hasRole('admin'))
                        @if(isset($user->numbers[$index]))
                            <td class="text-center td_wnumber"><input type="text" class="input_wnumber" data-id="{{$user->numbers[$index]['id']}}" value="{{$user->numbers[$index]['number']}}"></td>
                        @else
                            <td class="text-center bg-gray td_wnumber"><input type="text" class="input_wnumber" data-id="0"></td>
                        @endif
                    @else
                        @if(auth()->user()->id==$user->id)

                            @if(isset($user->numbers[$index]))
                                <td class="text-center td_wnumber"><input type="text" class="input_wnumber" data-id="{{$user->numbers[$index]['id']}}" value="{{$user->numbers[$index]['number']}}"></td>
                            @else
                                <td class="text-center bg-gray td_wnumber"><input type="text" class="input_wnumber" data-id="0"></td>
                            @endif
                        @else
                            @if(isset($user->numbers[$index]))
                                <td class="text-center td_wnumber">{{$user->numbers[$index]['number']}}</td>
                            @else
                                <td class=""></td>

                            @endif

                        @endif
                    @endif
                @endforeach
            </tr>
        @endforeach
        @if(auth()->user()->hasRole('admin'))
            <tr>
                <td colspan="99" class="text-center"><button class="btn btn-sm btn-block btn-primary" onclick="add_user()">Generate Random Member/User</button></td>
            </tr>
        @endif
        </tbody>
    </table>
</div>