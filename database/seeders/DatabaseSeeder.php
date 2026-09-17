<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Spk;
use App\Models\SpkMaterial;
use App\Models\SpkMachineSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. USERS ────────────────────────────────────────────────────────────
        // ─── 1. USERS ────────────────────────────────────────────────────────────
        // Team Yellow
        User::create(['name' => 'Foreman Yellow',   'email' => 'foreman1@dkj.com', 'password' => Hash::make('123'), 'role' => 'foreman', 'pin' => '1111', 'team' => 'yellow']);
        for ($i=1; $i<=5; $i++) {
            User::create(['name' => "Worker Yellow $i", 'email' => "worker1_$i@dkj.com", 'password' => Hash::make('123'), 'role' => 'worker', 'pin' => '0000', 'team' => 'yellow']);
        }
        
        // Team Red
        User::create(['name' => 'Foreman Red',      'email' => 'foreman2@dkj.com', 'password' => Hash::make('123'), 'role' => 'foreman', 'pin' => '1112', 'team' => 'red']);
        for ($i=1; $i<=5; $i++) {
            User::create(['name' => "Worker Red $i",    'email' => "worker2_$i@dkj.com", 'password' => Hash::make('123'), 'role' => 'worker', 'pin' => '0000', 'team' => 'red']);
        }
        
        // Team Green
        User::create(['name' => 'Foreman Green',    'email' => 'foreman3@dkj.com', 'password' => Hash::make('123'), 'role' => 'foreman', 'pin' => '1113', 'team' => 'green']);
        for ($i=1; $i<=5; $i++) {
            User::create(['name' => "Worker Green $i",  'email' => "worker3_$i@dkj.com", 'password' => Hash::make('123'), 'role' => 'worker', 'pin' => '0000', 'team' => 'green']);
        }

        User::create(['name' => 'QC 1',             'email' => 'qc@dkj.com',         'password' => Hash::make('123'), 'role' => 'qc',               'pin' => '2222']);
        User::create(['name' => 'Admin Manufaktur', 'email' => 'admin@dkj.com',      'password' => Hash::make('123'), 'role' => 'admin_manufactur', 'pin' => '9999']);
        User::create(['name' => 'R&D Officer',      'email' => 'rnd@dkj.com',        'password' => Hash::make('123'), 'role' => 'rnd',              'pin' => '3333']);
        User::create(['name' => 'Supervisor',       'email' => 'supervisor@dkj.com', 'password' => Hash::make('123'), 'role' => 'supervisor',       'pin' => '4444']);
        User::create(['name' => 'Manager',          'email' => 'manager@dkj.com',    'password' => Hash::make('123'), 'role' => 'manager',           'pin' => '5555']);

        // ─── 2. SPK-001 : PP Compound 50W30 ──────────────────────────────────────
        // Status: upcoming (bisa langsung demo dari awal)
        $spk1 = Spk::create([
            'spk_number'     => 'SPK-2026-08-001',
            'product_name'   => 'PP Compound 50W30',
            'status'         => 'upcoming',
            'current_step'   => 'cleaning',
            'start_date'     => now(),
            'due_date'       => now()->addDays(2),
            'man_allocation' => 3,
            'notes'          => 'Prioritas tinggi. Pastikan mesin bersih sebelum memulai.',
            'customer'       => 'PT. Indoplastika Makmur',
            'ship_date'      => now()->addDays(3),
            'target_op'      => 150,
            'working_days'   => 2.5,
            'working_minutes'=> 3600,
            'machine'        => 'E01',
            'delay_hour'     => 0,
            'keterangan'     => 'Produksi reguler, warna hitam pekat',
            'remarks'        => 'Urgent Delivery',
        ]);

        // Bahan baku SPK-001
        // target_weight_per_batch = kg per 1 batch (formula weighing)
        // total_batches_required  = jumlah batch total
        // total_material_kg       = total kebutuhan = per_batch × batches
        SpkMaterial::create([
            'spk_id'                 => $spk1->id,
            'material_name'          => 'PP Resin (Grade HD)',
            'target_weight_per_batch'=> 75.00,
            'total_batches_required' => 10,
            'total_material_kg'      => 750.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk1->id,
            'material_name'          => 'Stearic Acid',
            'target_weight_per_batch'=> 1.50,
            'total_batches_required' => 10,
            'total_material_kg'      => 15.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk1->id,
            'material_name'          => 'Parawax',
            'target_weight_per_batch'=> 0.50,
            'total_batches_required' => 10,
            'total_material_kg'      => 5.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk1->id,
            'material_name'          => 'Carbon Black',
            'target_weight_per_batch'=> 2.00,
            'total_batches_required' => 10,
            'total_material_kg'      => 20.00,
        ]);

        // Settingan mesin SPK-001
        SpkMachineSettings::create([
            'spk_id'     => $spk1->id,
            'temp_zone1' => 185.0,
            'temp_zone2' => 190.0,
            'temp_zone3' => 195.0,
            'pressure_bar'=> 8.5,
            'rpm_speed'  => 45,
            'notes'      => 'Gunakan dies berdiameter 3mm. Jalankan pre-heat 15 menit sebelum produksi.',
        ]);

        // ─── 3. SPK-002 : PE Film Masterbatch ────────────────────────────────────
        // Status: upcoming
        $spk2 = Spk::create([
            'spk_number'     => 'SPK-2026-08-002',
            'product_name'   => 'PE Film Masterbatch',
            'status'         => 'upcoming',
            'current_step'   => 'cleaning',
            'start_date'     => now()->addDays(1),
            'due_date'       => now()->addDays(3),
            'man_allocation' => 2,
            'notes'          => 'Perhatikan kelembaban material sebelum proses.',
            'customer'       => 'CV. Maju Jaya Plastik',
            'ship_date'      => now()->addDays(4),
            'target_op'      => 120,
            'working_days'   => 1.5,
            'working_minutes'=> 2160,
            'machine'        => 'E06',
            'delay_hour'     => 1.5,
            'keterangan'     => 'Pembuatan masterbatch putih',
            'remarks'        => 'Packing 25kg/sak',
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk2->id,
            'material_name'          => 'LLDPE Resin',
            'target_weight_per_batch'=> 60.00,
            'total_batches_required' => 8,
            'total_material_kg'      => 480.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk2->id,
            'material_name'          => 'Titanium Dioxide (TiO2)',
            'target_weight_per_batch'=> 15.00,
            'total_batches_required' => 8,
            'total_material_kg'      => 120.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk2->id,
            'material_name'          => 'Dispersing Agent',
            'target_weight_per_batch'=> 2.00,
            'total_batches_required' => 8,
            'total_material_kg'      => 16.00,
        ]);
        SpkMachineSettings::create([
            'spk_id'      => $spk2->id,
            'temp_zone1'  => 170.0,
            'temp_zone2'  => 175.0,
            'temp_zone3'  => 180.0,
            'pressure_bar'=> 7.0,
            'rpm_speed'   => 40,
            'notes'       => 'Kecepatan screw tidak boleh melebihi 45 RPM.',
        ]);

        // ─── 4. SPK-003 : ABS Black Compound ─────────────────────────────────────
        // Status: upcoming (deadline lebih jauh)
        $spk3 = Spk::create([
            'spk_number'     => 'SPK-2026-08-003',
            'product_name'   => 'ABS Black Compound',
            'status'         => 'upcoming',
            'current_step'   => 'cleaning',
            'start_date'     => now()->addDays(1),
            'due_date'       => now()->addDays(5),
            'man_allocation' => 4,
            'notes'          => 'Material ABS sensitif terhadap suhu. Jangan melebihi batas.',
            'customer'       => 'PT. Otomotif Komponen',
            'ship_date'      => now()->addDays(7),
            'target_op'      => 200,
            'working_days'   => 3,
            'working_minutes'=> 4320,
            'machine'        => 'E03',
            'delay_hour'     => 0,
            'keterangan'     => 'Komponen bodi motor',
            'remarks'        => 'Kualitas ekspor',
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk3->id,
            'material_name'          => 'ABS Resin',
            'target_weight_per_batch'=> 80.00,
            'total_batches_required' => 6,
            'total_material_kg'      => 480.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk3->id,
            'material_name'          => 'Carbon Black N330',
            'target_weight_per_batch'=> 4.00,
            'total_batches_required' => 6,
            'total_material_kg'      => 24.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk3->id,
            'material_name'          => 'Lubricant EBS',
            'target_weight_per_batch'=> 1.00,
            'total_batches_required' => 6,
            'total_material_kg'      => 6.00,
        ]);
        SpkMachineSettings::create([
            'spk_id'      => $spk3->id,
            'temp_zone1'  => 200.0,
            'temp_zone2'  => 210.0,
            'temp_zone3'  => 215.0,
            'pressure_bar'=> 9.5,
            'rpm_speed'   => 38,
            'notes'       => 'ABS — jangan melebihi 220°C. Lakukan vacuum venting.',
        ]);

        // ─── 5. SPK-004 : Nylon 66 GF30 ──────────────────────────────────────────
        // Status: upcoming
        $spk4 = Spk::create([
            'spk_number'     => 'SPK-2026-08-004',
            'product_name'   => 'Nylon 66 GF30',
            'status'         => 'upcoming',
            'current_step'   => 'cleaning',
            'start_date'     => now()->addDays(2),
            'due_date'       => now()->addDays(4),
            'man_allocation' => 3,
            'notes'          => 'OVERDUE. Tambah shift jika perlu.',
            'customer'       => 'PT. Prima Nylon',
            'ship_date'      => now()->addDays(5),
            'target_op'      => 100,
            'working_days'   => 2,
            'working_minutes'=> 2880,
            'machine'        => 'E05',
            'delay_hour'     => 4.5,
            'keterangan'     => 'Tambahan glass fiber 30%',
            'remarks'        => 'Late production',
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk4->id,
            'material_name'          => 'Nylon 66 Base Resin',
            'target_weight_per_batch'=> 70.00,
            'total_batches_required' => 12,
            'total_material_kg'      => 840.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk4->id,
            'material_name'          => 'Glass Fiber (GF)',
            'target_weight_per_batch'=> 30.00,
            'total_batches_required' => 12,
            'total_material_kg'      => 360.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk4->id,
            'material_name'          => 'Heat Stabilizer',
            'target_weight_per_batch'=> 0.50,
            'total_batches_required' => 12,
            'total_material_kg'      => 6.00,
        ]);
        SpkMachineSettings::create([
            'spk_id'      => $spk4->id,
            'temp_zone1'  => 265.0,
            'temp_zone2'  => 270.0,
            'temp_zone3'  => 275.0,
            'pressure_bar'=> 11.0,
            'rpm_speed'   => 30,
            'notes'       => 'GF Side feeder aktif di Zone 3. Gunakan screen pack 200 mesh.',
        ]);

        // ─── 6. SPK-005 : HDPE Water Pipe Grade ──────────────────────────────────
        // Status: upcoming
        $spk5 = Spk::create([
            'spk_number'     => 'SPK-2026-08-005',
            'product_name'   => 'HDPE Water Pipe Grade',
            'status'         => 'upcoming',
            'current_step'   => 'cleaning',
            'start_date'     => now()->addDays(3),
            'due_date'       => now()->addDays(6),
            'man_allocation' => 2,
            'notes'          => null,
            'customer'       => 'Pemerintah Daerah (Proyek Air)',
            'ship_date'      => now()->addDays(10),
            'target_op'      => 180,
            'working_days'   => 1,
            'working_minutes'=> 1440,
            'machine'        => 'E01',
            'delay_hour'     => 0,
            'keterangan'     => 'Pipa air standar SNI',
            'remarks'        => 'Gunakan material PE100',
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk5->id,
            'material_name'          => 'HDPE Resin PE100',
            'target_weight_per_batch'=> 90.00,
            'total_batches_required' => 5,
            'total_material_kg'      => 450.00,
        ]);
        SpkMaterial::create([
            'spk_id'                 => $spk5->id,
            'material_name'          => 'Carbon Black Masterbatch',
            'target_weight_per_batch'=> 2.50,
            'total_batches_required' => 5,
            'total_material_kg'      => 12.50,
        ]);
        SpkMachineSettings::create([
            'spk_id'      => $spk5->id,
            'temp_zone1'  => 210.0,
            'temp_zone2'  => 215.0,
            'temp_zone3'  => 220.0,
            'pressure_bar'=> 10.0,
            'rpm_speed'   => 35,
            'notes'       => null,
        ]);

        // ─── 7. Dummy Assignments (Dihapus untuk keperluan demo) ────────────────────────


        // ─── 8. WEEKLY SCHEDULES ────────────────────────────────────────────────
        $startOfWeek = now()->startOfWeek();
        \App\Models\WeeklySchedule::create([
            'start_date' => $startOfWeek->format('Y-m-d'),
            'end_date' => $startOfWeek->copy()->endOfWeek()->format('Y-m-d'),
            'morning_shift_team' => 'yellow',
            'afternoon_shift_team' => 'red',
            'night_shift_team' => 'green',
        ]);

        $nextWeek = $startOfWeek->copy()->addWeek();
        \App\Models\WeeklySchedule::create([
            'start_date' => $nextWeek->format('Y-m-d'),
            'end_date' => $nextWeek->copy()->endOfWeek()->format('Y-m-d'),
            'morning_shift_team' => 'red',
            'afternoon_shift_team' => 'green',
            'night_shift_team' => 'yellow',
        ]);
    }
}
