<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function index()
    {
        $user = (object) [
            'name' => 'Naura Ayunda',
            'nim_nip' => '233150601111055',
            'email' => 'naurayunda@student.ub.ac.id',
            'gender' => 'Perempuan',
            'domicile' => 'Malang, Jawa Timur',
            'phone' => '+62 8123451234567',
            'birth_date' => '12 Maret 2005',
            'university' => 'Universitas Brawijaya',
            'faculty' => 'Fakultas Ilmu Komputer',
            'major' => 'Pendidikan Teknologi Informasi',
            'year' => '2023',
            'semester' => '8',
            'gpa' => '3.99'
        ];

        return view('profile', compact('user'));
    }
}