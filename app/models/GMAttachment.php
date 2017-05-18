<?php

class GMAttachment extends Eloquent {
  protected $table = 'gm_mail_attachments';
  protected $primaryKey = 'fid';

  public function mail() {
    return $this->hasMany('GMMail');
  }  
}