<div class="container">
    <h3>Tambah Pertanyaan - {{ $activity->name }}</h3>

    <form method="POST" action="{{ url('/admin/activity/'.$activity->id.'/questions') }}">
        @csrf

        <div class="mb-3">
            <label>Label Pertanyaan</label>
            <input type="text" name="label" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Key (field name)</label>
            <input type="text" name="key" class="form-control" placeholder="contoh: group_id" required>
        </div>

        <div class="mb-3">
            <label>Tipe Input</label>
            <select name="type" class="form-control" id="type">
                <option value="text">Text</option>
                <option value="select">Select / Dropdown</option>
                <option value="number">Number</option>
                <option value="email">Email</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Wajib diisi?</label>
            <select name="is_required" class="form-control">
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
            </select>
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>

@endsection