<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Wind Speed (km/h)</th>
            <th>Wind Direction</th>
            <th>Rain Rate</th>
            <th>Rain Today</th>
            <th>Indoor Temp</th>
            <th>Outdoor Temp</th>
            <th>Indoor Humidity</th>
            <th>Outdoor Humidity</th>
            <th>UV</th>
            <th>Wind Gust</th>
            <th>Relative Air Pressure</th>
            <th>Absolute Air Pressure</th>
            <th>Solar Radiation</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $record)
        @foreach($record as $key2 => $item)
        <tr>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['date'] == 'Avarage' ? $key : $item['date'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}} ">{{ $item['windspeedkmh'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['winddir'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['rain_rate'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['rain_today'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['temp_in'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['temp_out'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['hum_in'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['hum_out'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['uv'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['wind_gust'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['air_press_rel'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['air_press_abs'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['solar_radiation'] }}</td>

        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>