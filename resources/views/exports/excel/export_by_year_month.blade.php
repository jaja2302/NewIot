@if($data['title'] !== 'Bulanan' && $data['title'] !== 'Mingguan')

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

        @foreach($data['data'] as $key => $item)
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

    </tbody>
</table>

@elseif($data['title'] !== 'Mingguan')


<table style="border-collapse: collapse;">
    <tr>
        <td style="border: 1px solid black; text-align: left;">Parameter</td>
        <td style="border: 1px solid black; text-align: left;">Satuan</td>
        <td style="border: 1px solid black; text-align: left;">Nilai</td>
    </tr>

    <tr>
        <td style="border: 1px solid black; text-align: left;">Kecepatan Angin</td>
        <td style="border: 1px solid black; text-align: left;">(km/jam)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['windspeedkmh']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Arah Angin</td>
        <td style="border: 1px solid black; text-align: left;">(derajat)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['winddir']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Tingkat Hujan</td>
        <td style="border: 1px solid black; text-align: left;">(mm/5 menit)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['rain_rate']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Suhu Dalam Ruangan</td>
        <td style="border: 1px solid black; text-align: left;">(°C)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['temp_in']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Suhu Luar Ruangan</td>
        <td style="border: 1px solid black; text-align: left;">(°C)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['temp_out']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Kelembaban Dalam Ruangan</td>
        <td style="border: 1px solid black; text-align: left;">(%)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['hum_in']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Kelembaban Luar Ruangan</td>
        <td style="border: 1px solid black; text-align: left;">(%)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['hum_out']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">UV</td>
        <td style="border: 1px solid black; text-align: left;">(indeks)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['uv']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Hembusan Angin</td>
        <td style="border: 1px solid black; text-align: left;">(km/jam)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['wind_gust']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Tekanan Udara Relatif</td>
        <td style="border: 1px solid black; text-align: left;">(hPa)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['air_press_rel']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Tekanan Udara Absolut</td>
        <td style="border: 1px solid black; text-align: left;">(hPa)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['air_press_abs']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Radiasi Matahari</td>
        <td style="border: 1px solid black; text-align: left;">(W/m²)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['solar_radiation']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Curah Hujan Bulanan</td>
        <td style="border: 1px solid black; text-align: left;">(mm)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['monthlyrainmm']}}</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; text-align: left;">Hembusan Angin Harian Maksimal</td>
        <td style="border: 1px solid black; text-align: left;">(km/jam)</td>
        <td style="border: 1px solid black; text-align: left;">{{$data['data']['bulanan']['maxdailygust']}}</td>
    </tr>
</table>

@else
<table style="border-collapse: collapse;">
    <thead>
        <tr>
            <td rowspan="2" style="border: 1px solid black; text-align: center;">Parameter</td>
            <td rowspan="2" style="border: 1px solid black; text-align: center;">Satuan</td>
            <td colspan="{{count($data['data'])}}" style="border: 1px solid black; text-align: center;">Nilai</td>
        </tr>
        <tr>

            @foreach($data['data'] as $key => $item)
            <td style="border: 1px solid black; text-align: center;">{{$key}}</td>
            @endforeach
        </tr>
    </thead>

    @php
    $parameters = [
    ['name' => 'Kecepatan Angin', 'unit' => '(km/jam)', 'key' => 'windspeedkmh'],
    ['name' => 'Arah Angin', 'unit' => '(derajat)', 'key' => 'winddir'],
    ['name' => 'Tingkat Hujan', 'unit' => '(mm/5 menit)', 'key' => 'rain_rate'],
    ['name' => 'Suhu Dalam Ruangan', 'unit' => '(°C)', 'key' => 'temp_in'],
    ['name' => 'Suhu Luar Ruangan', 'unit' => '(°C)', 'key' => 'temp_out'],
    ['name' => 'Kelembaban Dalam Ruangan', 'unit' => '(%)', 'key' => 'hum_in'],
    ['name' => 'Kelembaban Luar Ruangan', 'unit' => '(%)', 'key' => 'hum_out'],
    ['name' => 'UV', 'unit' => '(indeks)', 'key' => 'uv'],
    ['name' => 'Hembusan Angin', 'unit' => '(km/jam)', 'key' => 'wind_gust'],
    ['name' => 'Tekanan Udara Relatif', 'unit' => '(hPa)', 'key' => 'air_press_rel'],
    ['name' => 'Tekanan Udara Absolut', 'unit' => '(hPa)', 'key' => 'air_press_abs'],
    ['name' => 'Radiasi Matahari', 'unit' => '(W/m²)', 'key' => 'solar_radiation'],
    ['name' => 'Curah Hujan Bulanan', 'unit' => '(mm)', 'key' => 'monthlyrainmm'],
    ['name' => 'Hembusan Angin Harian Maksimal', 'unit' => '(km/jam)', 'key' => 'maxdailygust']
    ];
    @endphp

    @foreach($parameters as $param)
    <tr>
        <td style="border: 1px solid black; text-align: left;">{{$param['name']}}</td>
        <td style="border: 1px solid black; text-align: left;">{{$param['unit']}}</td>
        @foreach($data['data'] as $item)
        <td style="border: 1px solid black; text-align: left;">{{$item[$param['key']]}}</td>
        @endforeach
    </tr>
    @endforeach

</table>
@endif