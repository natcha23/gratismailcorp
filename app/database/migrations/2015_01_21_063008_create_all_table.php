<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    // Schema::dropIfExists('accounts');
    // Schema::dropIfExists('mails');
    // Schema::dropIfExists('mail_attachments');

    Schema::create('accounts', function($table) {
      $table->string('email', 100)->unique()->primary('email');
      $table->string('name', 100);
      $table->string('sent_email', 100);
      $table->dateTime('last_login_at');
      $table->string('remember_token')->nullable();
      $table->timestamps();
    });

    Schema::create('mail_folders', function($table) {
      $table->integer('folder_id')->unique()->primary('folder_id');
      $table->string('name', 40);
      $table->string('icon', 30);
      $table->timestamps();
    });

    Schema::create('mails', function($table) {
      $table->bigIncrements('mail_id')->primary('mail_id');
      $table->integer('suid')->default(0);
      $table->integer('folder_id', 3);
      $table->string('email', 50);
      $table->string('subject')->nullable();
      $table->string('from_name', 100)->nullable();
      $table->string('sent_from', 50)->nullable();
      $table->string('sent_to')->nullable();
      $table->string('sent_cc')->nullable();
      $table->string('sent_bcc')->nullable();
      $table->string('reply_to')->nullable();
      $table->longText('text')->nullable();
      $table->integer('udate');
      $table->integer('is_size')->default(0);
      $table->integer('massage_no')->default(0);
      $table->integer('recent', 1)->default(0);
      $table->integer('flagged', 1)->default(0);
      $table->integer('answered', 1)->default(0);
      $table->integer('deleted', 1)->default(0);
      $table->integer('seen', 1)->default(0);
      $table->integer('draft', 1)->default(0);
      $table->timestamps();
    });

    Schema::create('mail_attachments', function($table) {
      $table->integer('mail_id');
      $table->string('fid')->primary('fid');
      $table->string('file_name');
      $table->string('file_name_original');
      $table->timestamps();
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
