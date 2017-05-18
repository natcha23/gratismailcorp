@extends('layout/master')

@section('content')

  <style>
    h4 { color: #555 }
  </style>

  <div class="col-sm-4">
    <h4 data-toggle="collapse" data-target="#collapseRemove" aria-expanded="false" aria-controls="collapseRemove">Upload new template</h4>
    <table class="table">
      <tr>
        <td>
          {{ Form::open(array('action' => 'GMTemplateController@addTemplate', 'files' => true)) }}
            <div class="form-group">
              <label for="template-html">File template</label>
              {{ Form::file('template-html') }}
            </div>
            {{ Form::submit('Submit and upload', ['class' => 'btn btn-default btn-sm']) }}
          {{ Form::close() }}
        </td>
      </tr>
    </table>
  </div>

  <div class="col-sm-8">
    <h4>Templates</h4>
    <table class="table table-hover">
      <!-- <thead>
        <tr bgcolor="#EEE">
          <th width="70%">File name</th>
          <th width="30%">Control</th>
        </tr>
      </thead> -->
      <tbody>
        <?php
        $tmpFileNameForSort = array();
        if(is_dir($dir)):
          if($dh = opendir($dir)):
            while ($file = readdir($dh)):
              $tmpFileNameForSort[] = $file;
            endwhile;
            natsort($tmpFileNameForSort);

            foreach($tmpFileNameForSort as $file):
               if($file != '.' AND $file != '..'):
                echo '<tr>
                        <td width="50%">'.$file.'</td>
                        <td class="text-right">' .
                            html_entity_decode(link_to("template/handle/{$file}", '<span class="glyphicon glyphicon-edit"></span> Edit', ['class' => 'btn btn-link btn-sm'])).
                            html_entity_decode(link_to("template/view/{$file}", '<span class="glyphicon glyphicon-search"></span> View', ['class' => 'btn btn-link btn-sm'])).
                            html_entity_decode(link_to("template/download/{$file}", '<span class="glyphicon glyphicon-circle-arrow-down"></span> Download', ['class' => 'btn btn-link btn-sm'])).
                            html_entity_decode(link_to("template/remove/{$file}", '<span class="glyphicon glyphicon-trash"></span> Delete!', ['class' => 'btn btn-link btn-sm', 'onclick' => 'return confirm(\'Do yo want to remove the template? ('.$file.')\');'])).'
                        </td>
                      </tr>';
               endif;
             endforeach;
            closedir($dh);
          endif;
        endif;
        ?>
      </tbody>
    </table>
  </div>

@stop