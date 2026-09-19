<?php

// Hanya aturan yang dipakai aplikasi; aturan lain jatuh ke fallback `en`.
return [
    'after_or_equal' => ':Attribute harus tanggal setelah atau sama dengan :date.',
    'array' => ':Attribute tidak valid.',
    'before_or_equal' => ':Attribute harus tanggal sebelum atau sama dengan :date.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password salah.',
    'date' => ':Attribute harus berupa tanggal yang valid.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'lowercase' => ':Attribute harus huruf kecil.',
    'max' => [
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah terdaftar.',

    'custom' => [
        'tanggal' => [
            'before_or_equal' => 'Tanggal tidak boleh di masa depan.',
        ],
        'no_hp' => [
            'regex' => 'Nomor HP hanya boleh berisi angka.',
        ],
        'anak.tanggal_lahir' => [
            'before_or_equal' => 'Tanggal lahir anak tidak boleh di masa depan.',
            'after_or_equal' => 'StimuTumbuh diperuntukkan bagi anak usia 0–36 bulan.',
        ],
    ],

    'attributes' => [
        'nama' => 'nama',
        'email' => 'email',
        'password' => 'password',
        'hubungan_dengan_anak' => 'hubungan dengan anak',
        'no_hp' => 'nomor HP',
        'pendidikan_terakhir' => 'pendidikan terakhir',
        'pekerjaan' => 'pekerjaan',
        'kecamatan' => 'kecamatan',
        'alamat' => 'alamat',
        'anak.nama_inisial' => 'nama anak',
        'anak.tanggal_lahir' => 'tanggal lahir anak',
        'anak.jenis_kelamin' => 'jenis kelamin anak',
        'anak.bb_lahir_gram' => 'berat lahir',
        'anak.pb_lahir_cm' => 'panjang lahir',
        'anak.lingkar_kepala_cm' => 'lingkar kepala',
        'anak.usia_gestasi_minggu' => 'usia kehamilan',
        'anak.jenis_persalinan' => 'jenis persalinan',
        'anak.kondisi_lahir' => 'kondisi saat lahir',
        'jawaban' => 'jawaban',
        'tanggal' => 'tanggal',
        'respons_anak' => 'respons anak',
    ],
];
