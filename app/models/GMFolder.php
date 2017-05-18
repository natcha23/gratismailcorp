<?php

class GMFolder extends Eloquent {
  protected $table = 'gm_mail_folders';
  protected $primaryKey = 'folder_id';

  public function mail() {
    return $this->hasMany('GMMail');
  }
}