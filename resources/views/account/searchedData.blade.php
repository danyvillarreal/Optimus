<table class="table table-hover table-condensed">
    <thead>
        <tr>
            <th colspan="3">Nombre</th>
            <th colspan="1">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($accounts as $account)
            <tr>
                <td colspan="3">{{$account->name}} - {{$account->documentTypeName}}-{{$account->document_number}}</td>
                <td colspan="1">
                    <table>
                    @foreach($locations as $location)
                        @if ($location->account_id == $account->id)
                            <tr>
                                <td colspan="">{{$location->city}} - {{$location->address}}</td>
                                <td colspan="">
                                    <button class="btn btn-sm btn-success" onclick="choiceAccount(this)" value="{{$location->id}}" type="submit">Select</button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>