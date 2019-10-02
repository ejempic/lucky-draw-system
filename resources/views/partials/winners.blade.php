<h4>Winners<button class="btn btn-danger btn-sm float-right" id="truncate_winners">Truncate the winners</button>
</h4>
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
</div>
