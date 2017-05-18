<?php

class GMMail extends Eloquent {
  protected $table = 'gm_mails';
  protected $primaryKey = 'uid';

  public function mailAttachment() {
    return $this->belongsTo('GMAttachment');
  }
}