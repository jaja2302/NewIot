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
        @foreach($data as $record)
        <tr>
            <td>{{ $record['date'] }}</td>
            <td>{{ $record['windspeedkmh'] }}</td>
            <td>{{ $record['winddir'] }}</td>
            <td>{{ $record['rain_rate'] }}</td>
            <td>{{ $record['rain_today'] }}</td>
            <td>{{ $record['temp_in'] }}</td>
            <td>{{ $record['temp_out'] }}</td>
            <td>{{ $record['hum_in'] }}</td>
            <td>{{ $record['hum_out'] }}</td>
            <td>{{ $record['uv'] }}</td>
            <td>{{ $record['wind_gust'] }}</td>
            <td>{{ $record['air_press_rel'] }}</td>
            <td>{{ $record['air_press_abs'] }}</td>
            <td>{{ $record['solar_radiation'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>