<?php

namespace Mark\PlisioPayment;

use XF\AddOn\AbstractSetup;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
    public function install(array $stepParams = [])
    {
        // Create a table to store payment transactions
        $sm = $this->schemaManager();
        $sm->createTable('xf_plisio_payment', function(Create $table) {
            $table->addColumn('payment_id', 'int')->autoIncrement();
            $table->addColumn('user_id', 'int');
            $table->addColumn('coin', 'varchar', 50);
            $table->addColumn('network', 'varchar', 50);
            $table->addColumn('amount', 'decimal', [10, 2]);
            $table->addColumn('payment_address', 'varchar', 255);
            $table->addColumn('qr_code', 'varchar', 255);
            $table->addColumn('status', 'varchar', 50);
            $table->addColumn('expires_at', 'int');
            $table->addColumn('created_at', 'int');
            $table->addKey('user_id');
        });
    }

    public function uninstall()
    {
        $sm = $this->schemaManager();
        $sm->dropTable('xf_plisio_payment');
    }
}
