<?php

namespace App\Exports;

use App\Models\ContactGroup;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactGroupsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $contactgroups = ContactGroup::where('organization_id', session()->get('current_organization'))
            ->whereNull('deleted_at')
            ->get();


        // Modify the collection to include formatted phone numbers and group names
        return $contactgroups->map(function ($group) {
            $row = [
                'group_name' => $group->name,
            ];

            return array_map([$this, 'sanitizeCellValue'], $row);
        });
    }

    private function sanitizeCellValue($value)
    {
        if (is_string($value) && preg_match('/^[=\+\-@\t\r]/', $value)) {
            return "'" . $value;
        }
        return $value;
    }

    public function headings(): array
    {
        // Define your headers here
        $headers = [
            'Group name'
        ];

        return $headers;
    }
}
