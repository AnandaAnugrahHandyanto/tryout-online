<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('hasil_tryout', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil_tryout','started_at')) {
                $table->dateTime('started_at')->nullable()->after('waktu_submit');
            }
        });
        Schema::table('detail_jawaban', function (Blueprint $table) {
            // make jawaban nullable + add ragu
            // SQLite needs recreate, Laravel handles via nullable modify
            $table->boolean('ragu')->default(false)->after('benar_salah');
        });
        // For SQLite/MySQL make jawaban_siswa nullable via raw
        try {
            \DB::statement("ALTER TABLE detail_jawaban MODIFY jawaban_siswa ENUM('a','b','c','d') NULL");
        } catch (\Throwable $e) {
            // SQLite fallback - recreate not needed, keep as is and handle null at app layer
        }
    }
    public function down(): void {
        Schema::table('hasil_tryout', function (Blueprint $table) {
            if (Schema::hasColumn('hasil_tryout','started_at')) $table->dropColumn('started_at');
        });
        Schema::table('detail_jawaban', function (Blueprint $table) {
            if (Schema::hasColumn('detail_jawaban','ragu')) $table->dropColumn('ragu');
        });
    }
};
