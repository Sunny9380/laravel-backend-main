<?php

namespace App\Exports;

use App\Models\RequestVendorBankDetails;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBankDetails;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllUsersExport implements FromArray, WithHeadings
{

    public function array(): array
    {

        $users = User::orderBy('created_at', 'asc')
            ->where('role', 0)
            ->get();
        $exportData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'role' => "User",
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'phone_number' => $user->phone_number,
                'dob' => $user->dob,
                'gender' => $user->gender ?? 'Not Specified',
                'is_blocked' => ($user->is_blocked == 1 ? 'Yes' : 'No'),
                'is_email_verified' => ($user->is_email_verified == 1 ? 'Yes' : 'No'),
                'created_at' => $user->created_at
            ];
        });

        return [$exportData];
    }

    public function headings(): array
    {
        // Define the column headings
        return [
            'Id',
            'Role',
            'Name',
            'Email',
            'Address',
            'Phone Number',
            'DOB',
            'Gender',
            'Is Blocked',
            'Is Email verified',
            'Created At'
        ];
    }
}

