<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table
                ->string('username', 100)
                ->nullable()
                ->after('name');
        });

        /*
         * Generate temporary unique usernames for existing users.
         */
        DB::table('users')
            ->orderBy('user_id')
            ->get([
                'user_id',
                'name',
            ])
            ->each(function (object $user): void {
                $baseUsername = strtolower(
                    preg_replace(
                        '/[^a-zA-Z0-9_]/',
                        '',
                        str_replace(' ', '_', $user->name)
                    ) ?: 'user'
                );

                DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update([
                        'username' =>
                            $baseUsername . '_' . $user->user_id,
                    ]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table
                ->string('username', 100)
                ->nullable(false)
                ->change();

            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique([
                'username',
            ]);

            $table->dropColumn('username');
        });
    }
};
