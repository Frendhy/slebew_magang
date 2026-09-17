@extends('layouts.admin')

@section('admin_content')
<div class="fade-in">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-1">Manajemen Operator</h1>
        <p class="text-slate-500">Kelola data karyawan (Foreman & Worker) serta assign tim.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-500/20 border border-green-500/50 text-green-400 p-4 rounded-xl mb-6 font-semibold">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-900">Daftar Operator</h2>
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="bg-brand-500 hover:bg-brand-400 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Karyawan
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-slate-600">
                <thead class="bg-slate-100/50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Tim</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @foreach($operators as $op)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $op->name }}</td>
                        <td class="px-6 py-4">{{ $op->email }}</td>
                        <td class="px-6 py-4">
                            @if($op->role === 'foreman')
                                <span class="bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider">Foreman</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 border border-slate-300 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider">Worker</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($op->team === 'yellow')
                                <span class="bg-yellow-500/20 text-yellow-500 border border-yellow-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider">Yellow</span>
                            @elseif($op->team === 'red')
                                <span class="bg-red-500/20 text-red-500 border border-red-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider">Red</span>
                            @elseif($op->team === 'green')
                                <span class="bg-green-500/20 text-green-500 border border-green-500/50 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider">Green</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button onclick="editOperator({{ $op }})" class="text-brand-400 hover:text-brand-300 font-semibold text-sm">Edit</button>
                            <form action="{{ route('admin.operators.destroy', $op->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus karyawan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="modal-add" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-slate-900 mb-4">Tambah Karyawan Baru</h3>
        <form action="{{ route('admin.operators.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Nama</label>
                    <input type="text" name="name" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Role</label>
                    <select name="role" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="worker">Worker</option>
                        <option value="foreman">Foreman</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim</label>
                    <select name="team" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="">- Tidak Ada -</option>
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">PIN (Opsional)</label>
                    <input type="text" name="pin" maxlength="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
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
        <h3 class="text-xl font-bold text-slate-900 mb-4">Edit Karyawan</h3>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Nama</label>
                    <input type="text" name="name" id="edit-name" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Email</label>
                    <input type="email" name="email" id="edit-email" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Password Baru (Biarkan kosong jika tidak diganti)</label>
                    <input type="password" name="password" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Role</label>
                    <select name="role" id="edit-role" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="worker">Worker</option>
                        <option value="foreman">Foreman</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">Tim</label>
                    <select name="team" id="edit-team" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="">- Tidak Ada -</option>
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="green">Green</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-500 mb-1">PIN</label>
                    <input type="text" name="pin" id="edit-pin" maxlength="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-900 focus:ring-2 focus:ring-brand-500 outline-none">
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
    function editOperator(op) {
        document.getElementById('edit-form').action = '/admin/operators/' + op.id;
        document.getElementById('edit-name').value = op.name;
        document.getElementById('edit-email').value = op.email;
        document.getElementById('edit-role').value = op.role;
        document.getElementById('edit-team').value = op.team || '';
        document.getElementById('edit-pin').value = op.pin || '';
        document.getElementById('modal-edit').classList.remove('hidden');
    }
</script>
@endsection
