@extends('layout/master')

@section('sidebar')
  <div class="compose-email text-center">
    {{ Form::button('<i class="fa fa-edit"></i> new compose', array("class" => "btn btn-danger btn-sm")) }}
  </div>
  <ul id="folders">
    <li class="active">{{ html_entity_decode(link_to('/', '<i class="fa fa-envelope"></i> <span class="label-text">inbox</span>')) }}</li>
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-send"></i> <span class="labe-textl">sent</span>')) }}</li>
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-pencil"></i> <span class="label-text">draft</span>')) }}</li>
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-trash"></i> <span class="label-text">trash</span>')) }}</li>
  </ul>
@stop

@section('content')
  <ul id="control" class="clearfix">
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-reply"></i> <span class="labe-textl">reply</span>')) }}</li>
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-reply-all"></i> <span class="label-text">reply all</span>')) }}</li>
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-mail-forward"></i> <span class="label-text">forward</span>')) }}</li>
    <li>{{ html_entity_decode(link_to('/', '<i class="fa fa-trash"></i> <span class="label-text">remove</span>')) }}</li>
  </ul>
  <article id="mail">
    <textarea cols="80" id="txtDetail" name="txtDetail" rows="10">content</textarea>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, voluptates consequuntur hic quibusdam minima eaque nemo ad ducimus provident commodi. Doloremque, commodi dolorum iure quo voluptatibus illum tempora. Animi, vero.
    <br/>
  </article>
@stop