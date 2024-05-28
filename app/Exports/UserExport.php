<?php

namespace App\Exports;

use App\Models\RequestVendorBankDetails;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBankDetails;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromArray, WithHeadings
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function array(): array
    {

        $user = User::where('id', $this->userId)->first();
        $exportData = [
            'id' => $user->id,
            'role' => ($user->role == 1 ? 'Vendor' : ($user->role == 2 ? 'Admin' : 'User')),
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

