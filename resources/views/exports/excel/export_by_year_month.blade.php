<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kecepatan Angin (km/jam)</th>
            <th>Arah Angin (derajat)</th>
            <th>Tingkat Hujan (mm/5 menit)</th>
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
            <th>Hujan Harian (mm)</th>
            <th>Curah Hujan Mingguan (mm)</th>
            <th>Curah Hujan Bulanan (mm)</th>
            <th>Curah Hujan Tahunan (mm)</th>
            <th>Hembusan Angin Harian Maksimal (km/jam)</th>
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
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['dailyrainmm'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['weeklyrainmm'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['monthlyrainmm'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['yearlyrainmm'] }}</td>
            <td style="background-color: {{$item['date'] == 'Avarage' ? 'blue' : ''}}; color:{{$item['date'] == 'Avarage' ? 'white' : 'black'}}">{{ $item['maxdailygust'] }}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>