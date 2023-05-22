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
          <th>{{ $v['mk_nama'] }}</th>
          <th class="text-right" style="width: 30%">Total : {{ $v['bobot'] }}<br>Rata-rata : {{ $v['rata2'] }}</th>
        </tr>
        @if (count($v['children']) > 0)
          @foreach ($v['children'] as $v2)
            <tr>
              <td>&nbsp;&nbsp;{{ $v2['msk_nama'] }}</td>
              <td class="text-right" style="width: 30%">{{ $v2['bobot'] }}</td>
            </tr>
          @endforeach
        @endif
      @endforeach
    </tbody>
  </table>
</body>

</html>
