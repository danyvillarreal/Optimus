<div id="listLocations">
    <h4>Edit location</h4>
    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th colspan="">Country</th>
                <th colspan="">City location</th>
                <th colspan="">Address location</th>
                <th colspan="">Phone location</th>
                <th colspan="">Actions</th>
            </tr>
        </thead>
    </table>
    @foreach($locations as $location)
    <form method="post" class="form-inline" id="formLocation-{{$location->id}}">
        @csrf
        <table class="table table-hover table-condensed">
            <tbody>
            <tr>
                <td>
                    <select class="form-control" name="country" disabled="">
                        @foreach($countries as $country)
                            <option value="{{$country->id}}" {{($country->id === $location->country_id) ? "selected" : "" }}>{{$country->name}}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="city" value="{{ $location->city }}">
                </td>
                <td>
                    <input type="text" class="form-control" name="address" value="{{ $location->address }}">
                </td>
                <td>
                    <input type="text" class="form-control" name="phone" value="{{ $location->phone }}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLocation(this)" value="{{$location->id}}">Delete</button>
                    <button type="button" class="btn btn-sm btn-success" onclick="updateLocation(this)" value="{{$location->id}}">Update</button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
    @endforeach
</div>