<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Support\Database\Migration;

use App\Setting\Setting;

class CreateSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $group = $this->addSettingGroup('DNS');

        $this->addSetting($group, Setting::TYPE_TEXT, 'pkg.rdns.api.host');
        $this->addSetting($group, Setting::TYPE_TEXT, 'pkg.rdns.api.key');
        $this->addSetting($group, Setting::TYPE_SELECT, 'pkg.rdns.api.type', [
            'value' => '0',
            'options' => 'SynergyCP API,PowerDNS v3',
        ]);
        $this->addSetting($group, Setting::TYPE_TEXT, 'pkg.rdns.nameservers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->deleteSettingGroup('DNS');
    }
}
