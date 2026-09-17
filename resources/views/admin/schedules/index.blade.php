@extends('layouts.admin')

@section('admin_content')
<div class="fade-in">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Manajemen Jadwal</h1>
        <p class="text-slate-500">Atur rotasi shift tim mingguan.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-500/20 border border-green-500/50 text-green-400 p-4 rounded-xl mb-6 font-semibold">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-500/20 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 font-semibold">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-900">Daftar Jadwal Mingguan</h2>
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-brand-500 hover:bg-brand-400 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Jadwal
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-slate-600">
                <thead class="bg-slate-100/50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4">Shift Pagi <br><span class="text-[10px] normal-case">(Senin-Jumat: 08-16 | Sabtu: 08-13)</span></th>
                        <th class="px-6 py-4">Shift Siang <br><span class="text-[10px] normal-case">(Senin-Jumat: 16-00 | Sabtu: 13-18)</span></th>
                        <th class="px-6 py-4">Shift Malam <br><span class="text-[10px] normal-case">(Senin-Jumat: 00-08 | Sabtu: 18-23)</span></th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($schedules as $sched)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($sched->start_date)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($sched->end_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-"{{ $sched->morning_shift_team }}-500/20 text-{{ $sched->morning_shift_team }}-500 border border-{{ $sched->morning_shift_team }}-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider>{{ $sched->morning_shift_team }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-"{{ $sched->afternoon_shift_team }}-500/20 text-{{ $sched->afternoon_shift_team }}-500 border border-{{ $sched->afternoon_shift_team }}-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider>{{ $sched->afternoon_shift_team }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-"{{ $sched->night_shift_team }}-500/20 text-{{ $sched->night_shift_team }}-500 border border-{{ $sched->night_shift_team }}-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider>{{ $sched->night_shift_team }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button onclick="editSchedule({{ $sched }})" class="text-brand-400 hover:text-brand-300 font-semibold text-sm">Edit</button>
                            <form action="{{ route('admin.schedules.destroy', $sched->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada jadwal yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="modal-add" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Tambah Jadwal Baru</h3>
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tanggal Mulai (Senin)</label>
                    <input type="date" name="start_date" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                    <p class="text-xs text-slate-500 mt-1">Sistem otomatis mengambil hari Senin dari tanggal yang dipilih.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim Shift Pagi (08-16 / 08-13)</label>
                    <select name="morning_shift_team" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim Shift Siang (16-00 / 13-18)</label>
                    <select name="afternoon_shift_team" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="yellow">Yellow</option>
                        <option value="red" selected>Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim Shift Malam (00-08 / 18-23)</label>
                    <select name="night_shift_team" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green" selected>Green</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-600 text-slate-900 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-400 text-white rounded-xl font-bold text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="modal-edit" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Edit Jadwal</h3>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim Shift Pagi (08-16 / 08-13)</label>
                    <select name="morning_shift_team" id="edit-morning" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim Shift Siang (16-00 / 13-18)</label>
                    <select name="afternoon_shift_team" id="edit-afternoon" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim Shift Malam (00-08 / 18-23)</label>
                    <select name="night_shift_team" id="edit-night" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-600 text-slate-900 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-400 text-white rounded-xl font-bold text-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editSchedule(sched) {
        document.getElementById('edit-form').action = '/admin/schedules/' + sched.id;
        document.getElementById('edit-morning').value = sched.morning_shift_team;
        document.getElementById('edit-afternoon').value = sched.afternoon_shift_team;
        document.getElementById('edit-night').value = sched.night_shift_team;
        document.getElementById('modal-edit').classList.remove('hidden');
    }
</script>
@endsection
