<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    @include('pdf.styles')
</head>

<body>

    <div class="header">
        <h1>Твой Отчёт</h1>
        <p>{{ $user->first_name }} {{ $user->last_name }} &nbsp;·&nbsp; За {{ $days }} дней &nbsp;·&nbsp;
            {{ now()->format('d.m.Y') }}</p>
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
            @foreach ($measures as $m)
                <tr>
                    <td>{{ $m->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if ($m->systolic && $m->diastolic)
                            {{ $m->systolic }}/{{ $m->diastolic }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $m->pulse ?? '—' }}</td>
                    <td class="status-{{ $m->getStatus() }}">{{ $m->getStatusLabel() }}</td>
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
