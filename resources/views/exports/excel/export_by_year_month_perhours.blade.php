<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kecepatan Angin (km/jam)</th>
            <th>Arah Angin (derajat)</th>
            <th>Tingkat Hujan (mm/jam)</th>
            <th>Curah Hujan Hari Ini (mm)</th>
            <th>Suhu Dalam Ruangan (°C)</th>
            <th>Suhu Luar Ruangan (°C)</th>
            <th>Kelembaban Dalam Ruangan (%)</th>
            <th>Kelembaban Luar Ruangan (%)</th>
            <th>UV (indeks)</th>
            <th>Hembusan Angin (km/jam)</th>
            <th>Tekanan Udara Relatif (hPa)</th>
            <th>Tekanan Udara Absolut (hPa)</th>
            <th>Radiasi Matahari (W/m²)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr>
            <td>{{ $key }}</td>
            <td>{{ $item['windspeedkmh'] }}</td>
            <td>{{ $item['winddir'] }}</td>
            <td>{{ $item['rain_rate'] }}</td>
            <td>{{ $item['rain_today'] }}</td>
            <td>{{ $item['temp_in'] }}</td>
            <td>{{ $item['temp_out'] }}</td>
            <td>{{ $item['hum_in'] }}</td>
            <td>{{ $item['hum_out'] }}</td>
            <td>{{ $item['uv'] }}</td>
            <td>{{ $item['wind_gust'] }}</td>
            <td>{{ $item['air_press_rel'] }}</td>
            <td>{{ $item['air_press_abs'] }}</td>
            <td>{{ $item['solar_radiation'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>