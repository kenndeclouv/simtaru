<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    private $oldConstraintName = 'permohonans_enum_status_check';

    private $newConstraintName = 'permohonans_enum_status_check_v2';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        try {
            if ($driver === 'mysql') {

                DB::statement("ALTER TABLE permohonans DROP CHECK {$this->oldConstraintName}");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE permohonans DROP CONSTRAINT IF EXISTS {$this->oldConstraintName}");
            }
        } catch (\Exception $e) {

        }

        Schema::table('permohonans', function (Blueprint $table) {
            $table->string('enum_status')->default('pending')->notNull()->change();
        });


        DB::statement("ALTER TABLE permohonans ADD CONSTRAINT {$this->newConstraintName} CHECK (enum_status IN ('pending', 'approved', 'rejected', 'request_tte'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        DB::table('permohonans')
            ->where('enum_status', 'request_tte')
            ->update(['enum_status' => 'pending']);

        try {
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE permohonans DROP CHECK {$this->newConstraintName}");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE permohonans DROP CONSTRAINT IF EXISTS {$this->newConstraintName}");
            }
        } catch (\Exception $e) {

        }

        Schema::table('permohonans', function (Blueprint $table) {

            $table->string('enum_status', 255)->default('pending')->change();
        });

        DB::statement("ALTER TABLE permohonans ADD CONSTRAINT {$this->oldConstraintName} CHECK (enum_status IN ('pending', 'approved', 'rejected'))");
    }
};
