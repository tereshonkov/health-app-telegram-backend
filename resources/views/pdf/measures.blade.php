<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @font-face {
      font-family: 'DejaVu';
      src: url('{{ base_path("vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf") }}') format('truetype');
      font-weight: normal;
      font-style: normal;
    }

    @font-face {
      font-family: 'DejaVu';
      src: url('{{ base_path("vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf") }}') format('truetype');
      font-weight: bold;
      font-style: normal;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'DejaVu', sans-serif;
      font-size: 12px;
      color: #1A1C22;
      padding: 30px;
    }

    .header {
      text-align: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid #2C8FCC;
    }

    .header h1 {
      font-size: 22px;
      font-weight: bold;
      color: #2C8FCC;
      margin-bottom: 4px;
    }

    .header p {
      font-size: 11px;
      color: #6B7280;
    }

    .stats-table {
      width: 100%;
      margin-bottom: 24px;
      border-collapse: collapse;
    }

    .stats-table td {
      width: 33%;
      background: #F4F5F8;
      padding: 12px;
      text-align: center;
    }

    .stat-num {
      font-size: 20px;
      font-weight: bold;
      color: #1A1C22;
      display: block;
    }

    .stat-label {
      font-size: 10px;
      color: #6B7280;
      margin-top: 2px;
      display: block;
    }

    table.measures {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }

    table.measures thead tr {
      background: #2C8FCC;
      color: #fff;
    }

    table.measures thead th {
      padding: 9px 10px;
      text-align: left;
      font-size: 11px;
      font-weight: bold;
    }

    table.measures tbody tr:nth-child(even) {
      background: #F4F5F8;
    }

    table.measures tbody td {
      padding: 8px 10px;
      font-size: 11px;
      border-bottom: 1px solid #E5E7EB;
    }

    .status-ok       { color: #5FC4A6; font-weight: bold; }
    .status-warn     { color: #E3A857; font-weight: bold; }
    .status-high     { color: #E05050; font-weight: bold; }
    .status-critical { color: #E05050; font-weight: bold; }

    .footer {
      margin-top: 24px;
      padding-top: 12px;
      border-top: 1px solid #E5E7EB;
      font-size: 10px;
      color: #9AA1AE;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Health Report</h1>
    <p>{{ $user->first_name }} {{ $user->last_name }} &nbsp;·&nbsp; За {{ $days }} дней &nbsp;·&nbsp; {{ now()->format('d.m.Y') }}</p>
  </div>

  <table class="stats-table">
    <tr>
      <td>
        <span class="stat-num">{{ $total }}</span>
        <span class="stat-label">замеров</span>
      </td>
      <td>
        <span class="stat-num">{{ $avgSystolic }}/{{ $avgDiastolic }}</span>
        <span class="stat-label">среднее давление</span>
      </td>
      <td>
        <span class="stat-num">{{ $avgPulse }}</span>
        <span class="stat-label">средний пульс</span>
      </td>
    </tr>
  </table>

  <table class="measures">
    <thead>
      <tr>
        <th>Дата и время</th>
        <th>Давление</th>
        <th>Пульс</th>
        <th>Статус</th>
        <th>Заметка</th>
      </tr>
    </thead>
    <tbody>
      @foreach($measures as $m)
        @php
          $status = 'ok';
          if ($m->systolic >= 180 || $m->diastolic >= 110 || $m->pulse >= 130) $status = 'critical';
          elseif ($m->systolic >= 140 || $m->diastolic >= 90 || $m->pulse >= 100) $status = 'high';
          elseif ($m->systolic >= 130 || $m->diastolic >= 85 || $m->pulse >= 90) $status = 'warn';

          $statusLabel = [
            'ok'       => 'Норма',
            'warn'     => 'Повышено',
            'high'     => 'Высокое',
            'critical' => 'Критично!',
          ][$status];
        @endphp
        <tr>
          <td>{{ \Carbon\Carbon::parse($m->created_at)->format('d.m.Y H:i') }}</td>
          <td>
            @if($m->systolic && $m->diastolic)
              {{ $m->systolic }}/{{ $m->diastolic }}
            @else
              —
            @endif
          </td>
          <td>{{ $m->pulse ?? '—' }}</td>
          <td class="status-{{ $status }}">{{ $statusLabel }}</td>
          <td>{{ $m->note ?? '' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="footer">
    Этот отчёт создан автоматически и не заменяет консультацию врача.
  </div>

</body>
</html>