<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Review Jawaban {{ $user->username }}</title>
    <style>
        @page { margin: 28px 28px 42px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 9px; line-height: 1.35; }
        h1, h2, p { margin: 0; }
        .header { border-bottom: 3px solid #2454d8; padding-bottom: 12px; margin-bottom: 14px; }
        .header h1 { font-size: 19px; color: #163b9f; }
        .header p { margin-top: 4px; color: #667085; }
        .meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta td { width: 25%; border: 1px solid #d8deea; padding: 7px 8px; vertical-align: top; }
        .label { display: block; color: #697386; font-size: 7px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .value { font-weight: bold; color: #172033; }
        .status { color: #087443; }
        .section-title { font-size: 12px; margin: 12px 0 7px; color: #163b9f; }
        .answers { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .answers thead { display: table-header-group; }
        .answers tr { page-break-inside: avoid; }
        .answers th { background: #2454d8; color: white; border: 1px solid #173ea8; padding: 6px; font-size: 8px; text-align: left; }
        .answers td { border: 1px solid #d8deea; padding: 6px; vertical-align: top; word-wrap: break-word; }
        .answers tbody tr:nth-child(even) { background: #f7f9fc; }
        .no { width: 4%; text-align: center; }
        .form { width: 14%; }
        .question { width: 30%; }
        .object { width: 16%; }
        .answer { width: 28%; }
        .date { width: 8%; }
        .question-number { color: #2454d8; font-weight: bold; margin-right: 4px; }
        .detail { margin-bottom: 4px; }
        .detail:last-child { margin-bottom: 0; }
        .detail-label { color: #667085; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .detail-value { margin-top: 1px; }
        .footer { position: fixed; left: 0; right: 0; bottom: -28px; border-top: 1px solid #d8deea; padding-top: 6px; color: #7b8495; font-size: 7px; text-align: center; }
        .empty { padding: 24px !important; text-align: center; color: #697386; }
    </style>
</head>
<body>
    <div class="footer">Dokumen review jawaban - dibuat {{ $generatedAt->format('d-m-Y H:i') }}</div>

    <div class="header">
        <h1>Review Jawaban Responden</h1>
        <p>Dokumen hasil pengisian survey yang telah selesai.</p>
    </div>

    <table class="meta">
        <tr>
            <td><span class="label">Username</span><span class="value">{{ $user->username }}</span></td>
            <td><span class="label">Nama Lengkap</span><span class="value">{{ $profile?->fullname ?: '-' }}</span></td>
            <td><span class="label">Email</span><span class="value">{{ $profile?->email ?: '-' }}</span></td>
            <td><span class="label">No. Handphone</span><span class="value">{{ $profile?->no_handphone ?: '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Activity</span><span class="value">{{ $profile?->activity?->name ?: '-' }}</span></td>
            <td><span class="label">Group</span><span class="value">{{ $profile?->group?->name ?: '-' }}</span></td>
            <td><span class="label">Unit</span><span class="value">{{ $profile?->unit?->name ?: '-' }}</span></td>
            <td><span class="label">Status Survey</span><span class="value status">Selesai</span></td>
        </tr>
        <tr>
            <td><span class="label">Waktu Mulai</span><span class="value">{{ $session?->started_at?->format('d-m-Y H:i') ?: '-' }}</span></td>
            <td><span class="label">Waktu Selesai</span><span class="value">{{ $session?->finished_at?->format('d-m-Y H:i') ?: '-' }}</span></td>
            <td><span class="label">Jumlah Jawaban</span><span class="value">{{ $answers->count() }}</span></td>
            <td><span class="label">Role</span><span class="value">{{ $user->role?->name ?: '-' }}</span></td>
        </tr>
    </table>

    <h2 class="section-title">Daftar Jawaban</h2>
    <table class="answers">
        <thead>
            <tr>
                <th class="no">No</th>
                <th class="form">Form</th>
                <th class="question">Pertanyaan</th>
                <th class="object">Objek Penilaian</th>
                <th class="answer">Jawaban</th>
                <th class="date">Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($answers as $answer)
                @php($questionNumber = trim(($answer->question?->no_header ?? '').($answer->question?->no ?? '')))
                <tr>
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $answer->form?->name ?: '-' }}</td>
                    <td>
                        @if ($questionNumber)<span class="question-number">{{ $questionNumber }}</span>@endif
                        {{ $answer->question?->name ?: 'Pertanyaan telah dihapus' }}
                    </td>
                    <td>
                        @if ($answer->subunit?->name)
                            Sub Unit: {{ $answer->subunit->name }}
                        @elseif ($answer->respondentCompetitor?->name)
                            Kompetitor Responden: {{ $answer->respondentCompetitor->name }}                        @elseif ($answer->competitor?->name)
                            Kompetitor: {{ $answer->competitor->name }}
                        @else
                            Global
                        @endif
                    </td>
                    <td>
                        @foreach ($answer->review_details as $detail)
                            <div class="detail">
                                <div class="detail-label">{{ $detail['label'] }}</div>
                                <div class="detail-value">{{ $detail['value'] }}</div>
                            </div>
                        @endforeach
                    </td>
                    <td>{{ $answer->updated_at?->format('d-m-Y H:i') ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Responden belum memiliki jawaban.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>