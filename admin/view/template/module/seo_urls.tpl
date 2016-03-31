<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">

  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

<div class="container-fluid">
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
    </div>
    <div class="panel-body">
      <!-- form -->
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-super_seo" class="form-horizontal">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
          <li><a href="#tab-info" data-toggle="tab"><?php echo $tab_info; ?></a></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="tab-general">
            <div class="table-responsive">
              <table id="myUrlTable" class="table table-bordered table-hover" style="table-layout: fixed">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 50px"> # </td>
                    <td class="text-left"><?php echo $entry_route; ?></td>
                    <td class="text-left"><?php echo $entry_url; ?></td>
                    <td style="width: 112px;"></td>
                  </tr>
                </thead>
                <tbody>
                  <?php $i =0 ;?>
                  <?php if (isset($seo_urls)) { ?>

                    <?php foreach ($seo_urls as $seo_url) { ?>
                      <?php $i++;?>
                      <tr data-set="<?php echo $i ?>">
                        <td class="text-center"><?php echo $i;?>.</td>
                        <td class="text-left">
                          <input class="form-control" type="text" name="route[route]" value="<?php echo $seo_url['route']; ?>" />
                        </td>
                        <td class="text-left">
                          <?php foreach ($seo_url['keywords'] as $keyword) { ?>
                            <div class="input-group">
                              <span class="input-group-addon">
                                <img src="view/image/flags/<?php echo $keyword['language']['image'] ?>" alt="<?php echo $keyword['language']['code'] ?>" class="pull-left">
                              </span>
                              <input class="form-control" type="text" value="<?php echo $keyword['keyword'] ?>" name="route[url][<?php echo $keyword['language']['language_id'] ?>]" />
                            </div>
                          <?php } ?>
                        </td>
                        <td>
                          <ul class="list-inline">
                            <li><a href="<?php echo $edit_url; ?>" data-action="edit-row" data-set="<?php echo $i ?>" data-toggle="tooltip" class="btn btn-primary"><i class="fa fa-pencil"></i></a></li>
                            <li><a href="<?php echo $seo_url['delete']; ?>" data-toggle="tooltip" class="btn btn-danger"><i class="fa fa-trash-o"></i></a></li>
                          </ul>
                        </td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>

            <div class="form-group text-right">
              <div class="col-sm-12">
                <button type="button" id="nexturlbutton" data-lastrow="<?php echo $i; ?>" onclick="addOptionValue(this.getAttribute('data-lastrow'));" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_add; ?></button>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="tab-info">
            <div class="form-group">
              <div class="col-sm-12">
                <h4><?php echo $entry_examples_title; ?></h4>
                <h4><?php echo $entry_examples; ?></h4>
              </div>
            </div>
          </div>

        </div>
      </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function addOptionValue(option_row) {
    var datatuchange = parseInt(option_row) + 1;
    html  =  '<tr data-set="' + datatuchange + '">';
    html  += '<td class="text-center">' + datatuchange + '.</td>';
    html  += '<td><input class="form-control" type="text" name="route[route]" /></td>';

    html += '<td>';
    <?php foreach ($languages as $language): ?>
      html += '<div class="input-group">';
      html += '<span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image'] ?>" alt="<?php echo $language['code'] ?>" class="pull-left"></span>';
      html += '<input class="form-control" type="text" name="route[url][<?php echo $language['language_id'] ?>]" />';
      html += '</div>';
    <?php endforeach ?>
    html += '</td>';

    html += '<td>';
    html += '<ul class="list-inline">';
    html += '<li><a href="<?php echo $edit_add; ?>" data-action="edit-row" data-set="' + datatuchange + '" data-toggle="tooltip" class="btn btn-primary"><i class="fa fa-plus"></i></a></li>'
    html += '<li><button type="button" data-set="' + datatuchange + '" data-action="delete-row" data-toggle="tooltip" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></li>'
    html += '</ul>';
    html += '</td>';
    html += '</tr>';

    $('#myUrlTable > tbody:last-child').append(html);

    $('#nexturlbutton').attr('data-lastrow','' + datatuchange + '');
  }

  function deleteRow(rowId) {
    $('tr[data-set="' + rowId + '"]').remove();
  }

  function edit(url, formId) {
    var $row = $('tr[data-set="' + formId + '"]');

    $.ajax({
      url: url,
      type: 'POST',
      data: $row.find('.form-control'),
      success: function(json) {
        var message = JSON.parse(json);

        if (message.success) {
          alert(message.success);
        }

        if (message.error) {
          alert(message.error);
        }
      }
    });
  }

  $('#myUrlTable').on('click', '[data-action="edit-row"]', function(e) {
    e.preventDefault();
    edit($(this).attr('href'), $(this).data('set'));
  });

  $('#myUrlTable').on('click', '[data-action="delete-row"]', function(e) {
    e.preventDefault();
    deleteRow($(this).data('set'));
  });

</script>

<?php echo $footer; ?>
