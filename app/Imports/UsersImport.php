<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use App\Rules\StrongPassword;
use Illuminate\Validation\Rule;
use App\Models\OrganizationType;
use App\Models\OrganizationRoleType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements WithBatchInserts, WithChunkReading, WithValidation, WithHeadingRow, SkipsOnFailure, SkipsOnError, OnEachRow
{
    use Importable, SkipsFailures, SkipsErrors;

    /**
     * @var
     */
    protected $timestamp;
    protected $organizationTypes;
    protected $organizationRoleType;

    /**
     * Keep count of inserted rows
     * @var int
     */
    public $importedCount = 0;
    public $roleTypeId = 0;
    public $roleType = 0;
    /**
     * UsersImport constructor.
     */
    public function __construct()
    {
        $this->timestamp = now();
        $this->organizationTypes = OrganizationType::pluck('label');
        $this->organizationRoleType = OrganizationRoleType::pluck('display_name');
    }

    // /**
    //  * @param array $row
    //  * @return Model|null
    //  */
    // public function model(array $row)
    // {
    //     $this->importedCount++; // increment the inserted rows count
    //     return new User([
    //         'first_name' => $row['first_name'],
    //         'last_name' => $row['last_name'],
    //         'organization_name' => $row['organization_name'] ?? null,
    //         'organization_type' => $row['organization_type'] ?? null,
    //         'job_title' => $row['job_title'] ?? null,
    //         'email' => $row['email'],
    //         'role' => $row['role'] ?? 'course_creator',
    //         'password' => Hash::make($row['password']),
    //         'remember_token' => Str::random(64),
    //         'email_verified_at' => $this->timestamp
    //     ]);
    // }

    public function onRow(Row $row)
    {
        $orgId = Request::segment(4);
        $this->importedCount++; // increment the inserted rows count
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();
        $orgRole = ['display_name' => $row['role'], 'organization_id' => $orgId];
        $this->roleType = OrganizationRoleType::where($orgRole)->first();
        $this->roleTypeId = $this->roleType != null ? $this->roleType->id : 0;

        if ($this->roleTypeId != 0) {
            $user = User::firstOrCreate([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'organization_name' => $row['organization_name'] ?? null,
                'organization_type' => $row['organization_type'] ?? null,
                'job_title' => $row['job_title'] ?? null,
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'remember_token' => Str::random(64),
                'email_verified_at' => $this->timestamp
            ]);

            $user->publisherOrg()->create([
                'organization_id' => $orgId,
                'organization_role_type_id' => $this->roleTypeId
            ]);
        }
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string|max:255',
            '*.last_name' => 'required|string|max:255',
            '*.organization_name' => 'required|string|max:50',
            '*.organization_type' => ['required', 'string', 'max:255', Rule::in($this->organizationTypes),],
            '*.role' => ['required', 'string', 'max:255', Rule::in($this->organizationRoleType),],
            '*.job_title' => 'string|max:255',
            '*.email' => 'required|email|max:255|unique:users,email',
            '*.password' => ['required', 'string', new StrongPassword],
        ];
    }
}