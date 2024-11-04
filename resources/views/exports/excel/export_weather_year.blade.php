<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kecepatan Angin (km/h)</th>
            <th>Arah Angin (derajat)</th>
            <th>Tingkat Hujan (mm/h)</th>
            <th>Curah Hujan Hari Ini (mm)</th>
            <th>Suhu Dalam Ruangan (°C)</th>
            <th>Suhu Luar Ruangan (°C)</th>
            <th>Kelembaban Dalam Ruangan (%)</th>
            <th>Kelembaban Luar Ruangan (%)</th>
            <th>UV (indeks)</th>
            <th>Hembusan Angin (km/h)</th>
            <th>Tekanan Udara Relatif (hPa)</th>
            <th>Tekanan Udara Absolut (hPa)</th>
            <th>Radiasi Matahari (W/m²)</th>
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