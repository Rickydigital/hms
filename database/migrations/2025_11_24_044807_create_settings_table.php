<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();           // Unique setting name
            $table->text('value')->nullable();         // Value (string, number, JSON)
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true);  // Show in Admin panel
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default settings (will run in seeder)
        $this->insertDefaultSettings();
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    private function insertDefaultSettings()
    {
        $defaults = [
            // Hospital Info
            ['key' => 'hospital_name',          'value' => 'CareWell Hospital', 'type' => 'string',  'description' => 'Hospital Name'],
            ['key' => 'hospital_address',       'value' => '123 Main Road, Mumbai', 'type' => 'string', 'description' => 'Full Address'],
            ['key' => 'hospital_phone',         'value' => '9876543210', 'type' => 'string', 'description' => 'Contact Number'],
            ['key' => 'hospital_email',         'value' => 'info@carewell.com', 'type' => 'string', 'description' => 'Email'],

            // Fees
            ['key' => 'registration_fee',       'value' => '10000',    'type' => 'number', 'description' => 'New Patient Registration Fee'],
            ['key' => 'consultation_fee',       'value' => '10000',    'type' => 'number', 'description' => 'Doctor Consultation Fee'],
            ['key' => 'reactivation_fee',       'value' => '5000',    'type' => 'number', 'description' => 'Old Patient Card Reactivation Fee'],
            ['key' => 'injection_fee',          'value' => '5000',     'type' => 'number', 'description' => 'Injection Administration Fee'],

            // Patient Card System
            ['key' => 'card_validity_months',   'value' => '2',     'type' => 'number', 'description' => 'Patient Card Validity (Months)'],
            ['key' => 'auto_extend_on_visit',   'value' => 'true',   'type' => 'boolean','description' => 'Auto extend card on new visit?'],

            // Receipt
            ['key' => 'receipt_header',         'value' => 'CareWell Hospital\nYour Health, Our Priority', 'type' => 'string', 'description' => 'Receipt Header Text'],
            ['key' => 'receipt_footer',         'value' => 'Thank you for choosing CareWell Hospital\nFor queries: 9876543210', 'type' => 'string', 'description' => 'Receipt Footer'],

            // System
            ['key' => 'date_format',            'value' => 'd-m-Y',  'type' => 'string', 'description' => 'Date Format'],
            ['key' => 'currency_symbol',        'value' => 'Tsh',      'type' => 'string', 'description' => 'Currency Symbol'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->insert([
                'key'         => $setting['key'],
                'value'       => $setting['value'],
                'type'        => $setting['type'],
                'description' => $setting['description'],
                'sort_order'  => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
};