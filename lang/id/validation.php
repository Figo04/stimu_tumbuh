<?php

// Hanya aturan yang dipakai aplikasi; aturan lain jatuh ke fallback `en`.
return [
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password salah.',
    'date' => ':Attribute harus berupa tanggal yang valid.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'lowercase' => ':Attribute harus huruf kecil.',
    'max' => [
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah terdaftar.',

    'custom' => [
        'no_hp' => [
            'regex' => 'Nomor HP hanya boleh berisi angka.',
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
    ],
];
