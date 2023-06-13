<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Laporan Penilaian</title>
  <style>
    table.table {
      border: 1px solid black;
      border-collapse: collapse;
      margin: 0;
      padding: 5 10;
    }

    table.table th,
    table.table td {
      border: 1px solid black;
      text-align: left;
      margin: 0;
      padding: 5 10;
    }

    .text-right {
      text-align: right !important;
    }
  </style>
</head>

<body>
  <h1>Laporan Penilaian User</h1>
  <table class="table" style="width: 100%">
    <tbody>
      @foreach ($kriteria as $v)
        <tr>
          <th>{{ $v['nama'] }}</th>
          <th class="text-right" style="width: 30%">Total : {{ $v['total'] }}<br>Rata-rata : {{ $v['rata2'] }}</th>
        </tr>
        @if (count($v['subKriteria']) > 0)
          @foreach ($v['subKriteria'] as $v2)
            <tr>
              <th>&nbsp;&nbsp;{{ $v2['nama'] }}</th>
              <th class="text-right" style="width: 30%">{{ $v2['total'] }}</th>
            </tr>
            @if (count($v2['radar']) > 0)
              @foreach ($v2['radar'] as $v3)
                <tr>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $v3['nama'] }}</td>
                  <td class="text-right" style="width: 30%">{{ $v3['total'] }}%</td>
                </tr>
              @endforeach
            @endif
          @endforeach
        @endif
      @endforeach
    </tbody>
  </table>
</body>

</html>
