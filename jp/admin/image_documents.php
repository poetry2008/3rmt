<?php
/*
  $Id$
*/


  require('includes/application_top.php');

// Get and sanitize the document id, if set
  $doc_id = 0;
  if (isset ($_GET['did']) && $_GET['did'] != '0') {
    $doc_id = (int) $_GET['did'];
  }

// Get and sanitize the document type, if set
  $doc_type = 0;
  if (isset ($_GET['dt']) && $_GET['dt'] != '0') {
    $doc_type = (int) $_GET['dt'];
  }

// Document type path to show documents list
  $doc_path = 0;
  if (isset ($_GET['dtpath']) && $_GET['dtpath'] != '0') {
    $doc_path = (int) $_GET['dtpath'];
  }
  
  
// Start the processing by $action
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if (tep_not_null($action)) {
    switch ($action) {
// Actions for the second pass
////

      case 'file_move':
        $documents_id = $_POST['document_id'];
        $move_to = $_POST['move_to'];

        $old_file = tep_get_image_document_path($documents_id);
        $new_file = tep_get_new_file_path($documents_id, $move_to);


        if (@rename(DIR_FS_CATALOG . $old_file, DIR_FS_CATALOG . $new_file))
        {
          tep_db_query("update " . TABLE_IMAGE_DOCUMENTS . " set document_types_id = " . $move_to . " where documents_id = " . $documents_id);
        }

        tep_redirect(tep_href_link(FILENAME_IMAGE_DOCUMENTS, 'dtpath=' . $move_to));
        break;
// Set a document type Visible
      case 'process_doc_type_visible':
        if ( ($_GET['flag'] == 'False') || ($_GET['flag'] == 'True') ) {
          if (isset ($_GET['dt']) ) {
            $query = "update " . TABLE_IMAGE_DOCUMENT_TYPES . "
                      set type_visible = '" . $_GET['flag'] . "'
                      where document_types_id ='" . (int) $_GET['dt'] . "'
                   ";
            tep_db_query ($query);
          }
        } // if ( ($_POST['flag']

        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','flag','dt') ) ) );
        break;
        
// Add anew document type
      case 'process_new_type':
        if (is_writeable(DIR_FS_CATALOG_IMAGE_DOCUMENTS)) {
          if (file_exists(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name'])) {
            //$messageStack->add_session('directory exists', 'error');
          } else {
            mkdir(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name']);
            @chmod(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name'], 0777);
            if(file_exists(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name'])){
              $messageStack->add_session(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name'] . ' created!', 'success');
            } else {
              $messageStack->add_session(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name'] . ' create failed!', 'error');
              tep_redirect(FILENAME_IMAGE_DOCUMENTS);
            }
          }
        } else {
          $messageStack->add_session(DIR_FS_CATALOG_IMAGE_DOCUMENTS . ' is not writeable!', 'error');
          tep_redirect(FILENAME_IMAGE_DOCUMENTS);
        }
        if (isset ($_POST['type_name']) ) {
          $documents_type = tep_db_input ($_POST['type_name']);
          $type_query_raw = "
            select 
              count(*) as total
            from 
              " . TABLE_IMAGE_DOCUMENT_TYPES . "
            where 
              type_name = '" . $documents_type . "'
          ";
//          print $type_query_raw . "<br>\n";
          $type_query = tep_db_query ($type_query_raw);
          $type = tep_db_fetch_array ($type_query);
          
          if ($type['total'] < 1) {
            $sql_data_array = array ('type_name' => tep_db_prepare_input ($_POST['type_name']),
                                     'type_description' => tep_db_prepare_input ($_POST['type_description']),
                                     'sort_order' => (int) $_POST['sort_order'],
                                     'type_visible' => tep_db_prepare_input ($_POST['type_visible']) );

            tep_db_perform (TABLE_IMAGE_DOCUMENT_TYPES, $sql_data_array);
            $new_document_types_id = tep_db_insert_id();
          }
        } //if

        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','dt') ) . '&dt=' . $new_document_types_id) );
        break;

// Edit a document type
      case 'process_edit_type':
        if (isset ($_POST['document_types_id']) && $_POST['document_types_id'] > 0) {
          $sql_data_array = array ('type_description' => tep_db_prepare_input ($_POST['type_description']),
                                   'sort_order' => (int) $_POST['sort_order'],
                                   'type_visible' => tep_db_prepare_input ($_POST['type_visible']) );

          tep_db_perform (TABLE_IMAGE_DOCUMENT_TYPES, $sql_data_array, 'update', "document_types_id ='" . (int) $_POST['document_types_id'] . "'");
        } // if (isset
          
        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','dt') ) . 'dt=' . $doc_type) );
        break;

// Delete a document type
      case 'process_delete_type':
        if (isset ($_POST['type_name']) && $_POST['type_name'] != '') {
          tep_remove (DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type_name']);
        }
        
        if (isset ($_POST['document_types_id']) && $_POST['document_types_id'] > 0) {
          $document_types_id = (int) $_POST['document_types_id'];
          $products_query_raw = "
            select 
              documents_id
            from 
              " . TABLE_IMAGE_DOCUMENTS . "
            where 
              document_types_id = " . $document_types_id . "
          ";
//          print $products_query_raw . "<br>\n";
          $products_query = tep_db_query ($products_query_raw);
          while ($products_data = tep_db_fetch_array ($products_query)) {
            tep_db_query ("delete from " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . "
                           where documents_id = '" . (int) $products_query['documents_id'] . "'
                         ");
          }

          tep_db_query("delete from " . TABLE_IMAGE_DOCUMENTS . "
                        where document_types_id = '" . $document_types_id . "'
                      ");
          tep_db_query("delete from " . TABLE_IMAGE_DOCUMENT_TYPES . "
                        where document_types_id = '" . $document_types_id . "'
                      ");
        }   
                    
        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','dt') ) ) );
        break;

// Set documents Visible
      case 'doc_visible_confirm':
        if ( ($_GET['flag'] == 'False') || ($_GET['flag'] == 'True') ) {
          if (isset ($_GET['did']) ) {
            $query = "update " . TABLE_IMAGE_DOCUMENTS . "
                      set documents_visible = '" . tep_db_input ($_GET['flag']) . "',
                          last_updated = now()
                      where documents_id ='" . (int) $_GET['did'] . "'
                     ";
            tep_db_query ($query);
          }
        }
        
        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action') ) ) );
        break;

//Complete the delete process. Remove the file and all database entries.
      case 'doc_delete_confirm':
        tep_remove(DIR_FS_CATALOG_IMAGE_DOCUMENTS . $_POST['type'] . '/' . $_POST['name']);
        tep_db_query("delete from " . TABLE_IMAGE_DOCUMENTS . "
                      where documents_id = '" . (int)$_GET['did'] . "'
                    ");
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . "
                      where documents_id = '" . (int)$_GET['did'] . "'
                    ");

        $error = '';
        if ($tep_remove_error) {
          $error = 'error=' . $tep_remove_error;
        }
        
        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . $error) );
        break;

//Find all of the files in the directory that do not have database entries and add them.
      case 'process_doc_update':
        if ($doc_path != 0) {
          $type_query_raw = "
            select 
              type_name
            from 
              " . TABLE_IMAGE_DOCUMENT_TYPES . "
            where 
              document_types_id = " . $doc_path . "
          ";
          // print $type_query_raw . "<br>\n";
          $type_query = tep_db_query ($type_query_raw);

          $type = tep_db_fetch_array ($type_query);
          $dir_path = (string) (DIR_FS_CATALOG_IMAGE_DOCUMENTS . $type['type_name']);

          if (is_dir ($dir_path) ) {
            $add_files = get_image_directory_list ($dir_path);
          }

          $documents_query_raw = "
            select 
              documents_name
            from 
              " . TABLE_IMAGE_DOCUMENTS . "
            where 
             document_types_id = '" . $doc_path . "'
          ";
//          print $documents_query_raw . "<br>\n";
          $documents_query = tep_db_query ($documents_query_raw);

          if (tep_db_num_rows ($documents_query) > 0) {
            $database_files = array();
            while ($documents = tep_db_fetch_array($documents_query)) {
              $database_files[] = $documents['documents_name'];
            }

            $add_files = array_diff ( (array) $add_files, (array) $database_files); //Array of all files that are in the directory but not in the database
          } // if (tep_db_num_rows
          
          if (count ($add_files) > 0) {
            foreach ($add_files as $filename) {
              $documents_size = (@filesize ($dir_path . '/' . $filename)) / 1000;

              $sql_data_array = array ('documents_name' => tep_db_prepare_input ($filename),
                                       'documents_size' => tep_db_prepare_input( $documents_size),
                                       'document_types_id' => (int) $doc_path,
                                       'last_updated' => date ('Y-m-d') );

              tep_db_perform (TABLE_IMAGE_DOCUMENTS, $sql_data_array);
            } // foreach
          } // if (count
        } // if ($doc_path
        
        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) ) );
        break;

//Save data to the database after an Edit operation
      case 'doc_edit_confirm':
        if (isset ($_POST['type']) && isset ($_POST['name']) ) {
          
          $document_types_id = (int) $_POST['type'];
          $documents_name = tep_db_input ($_POST['name']);
          $documents_title = tep_db_input ($_POST['title']);

          $type_query_raw = "
            select 
              type_name
            from 
              " . TABLE_IMAGE_DOCUMENT_TYPES . "
            where 
              document_types_id = " . $document_types_id . "
          ";
          $type_query = tep_db_query ($type_query_raw);
          $type = tep_db_fetch_array ($type_query);

          $file_path = DIR_FS_CATALOG_IMAGE_DOCUMENTS . $type['type_name'] . '/' . $documents_name;

          if (file_exists ($file_path) && filesize ($file_path) > 0) {
            $sql_data_array = array ('documents_title' => $documents_title,
                                     'documents_visible' => tep_db_prepare_input ($_POST['visible']),
                                     'sort_order' => (int) $_POST['sort_order'],
                                     'last_updated' => date ('Y-m-d') 
                                    );

            tep_db_perform (TABLE_IMAGE_DOCUMENTS, $sql_data_array, 'update', "documents_id ='" . (int) $_GET['did'] . "'");
          } // if (file_exists
        } // if (isset ($_POST['type']
        
        tep_redirect(tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . $_GET['did']));
        break;

//Upload a file and update the database
      case 'process_doc_upload':
        if (tep_not_null ($_POST['document_types_id']) ) {
          $type_name = tep_db_input ($_POST['type_name']);

          require(DIR_WS_INCLUDES . 'classes/upload.php');
          $exts = array('jpeg', 'jpg', 'gif', 'png');
          $document_upload = new upload ('documents_name','','','777',$exts);
          $document_upload->set_destination (DIR_FS_CATALOG_IMAGE_DOCUMENTS . $type_name);

          if ($document_upload->parse() && $document_upload->save() ) {
            $documents_name = $document_upload->filename;
            $documents_path = DIR_FS_CATALOG_IMAGE_DOCUMENTS . $type_name . '/' . $documents_name;
            
            $documents_size = (@filesize ($documents_path) ) / 1000;
            $sql_data_array = array ('document_types_id' => tep_db_input ($_POST['document_types_id']),
                                     'documents_name' => tep_db_input ($document_upload->filename),
                                     'documents_size' => $documents_size,
                                     'documents_title' => tep_db_input ($_POST['documents_title']),
                                     'sort_order' => tep_db_input ($_POST['sort_order']),
                                     'documents_visible' => tep_db_prepare_input ($_POST['documents_visible']),
                                     'last_updated' => date ('Y-m-d') 
                                    );

            tep_db_perform (TABLE_IMAGE_DOCUMENTS, $sql_data_array);
            $insert_id = tep_db_insert_id();
          }
        }

        tep_redirect(tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did')) . 'did=' . $insert_id));
        break;

// Associate a document with a product
      case 'doc_associate_confirm':
        if (tep_not_null($_GET['did']) && tep_not_null($_POST['product']) && ($_POST['product'] != '-')) {
          tep_db_query("insert into " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . " (
                                     products_id,
                                     documents_id,
                                     last_updated                      )
                        values ('" . tep_db_input($_POST['product']) . "',
                                '" . (int)($_GET['did']) . "',
                                now()   )
                               ");
        }

        foreach ($_POST as $name => $value) {
          if (substr($name, 0, 4) == 'pid_' && $value == 'on') {
            tep_db_query("delete from " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . "
                          where documents_id = '" . (int) $_GET['did'] . "'
                            and products_id = '" . (int) substr ($name, 4) . "'");
          }
        }

        tep_redirect (tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action') ) ) );
        break;

    case 'new_type':
    case 'edit_type':
    case 'delete_type':
    case 'upload':
    case 'update':
    case 'edit':
    case 'associate':
    case 'delete':
    default:
        break;
    }
  }

  $in_directory = substr (substr (DIR_FS_DOCUMENT_ROOT, strrpos (DIR_FS_DOCUMENT_ROOT, '/') ), 1);
  $current_path_array = explode ('/', isset($current_path)?$current_path:'');
  $document_root_array = explode ('/', DIR_FS_DOCUMENT_ROOT);
  $goto_array = array (array ('id' => DIR_FS_DOCUMENT_ROOT, 'text' => $in_directory) );
  for ($i=0, $n=sizeof($current_path_array); $i<$n; $i++) {
    if ((isset($document_root_array[$i]) && ($current_path_array[$i] != $document_root_array[$i])) || !isset($document_root_array[$i])) {
      $goto_array[] = array('id' => implode('/', array_slice ($current_path_array, 0, $i+1) ), 'text' => $current_path_array[$i]);
    }
  }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo STORE_NAME; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/clip.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
// Listings in the center section of the page
//   There are two possible lists:
//   * Document types list
//   * Documents list
    if ($doc_path == 0) {
      // Top level -- Show Document types
?>
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_TYPES; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DIRECTORY; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_VISIBLE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SORT_ORDER; ?>&nbsp;&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;
    $document_types_query_raw = "select document_types_id, 
                                    type_name,
                                    type_description, 
                                    type_visible, 
                                    sort_order
                             from " . TABLE_IMAGE_DOCUMENT_TYPES . "
                             order by sort_order
                            ";
    // print $cztegories_query_raw . "<br>\n";
    $document_types_query = tep_db_query ($document_types_query_raw);
    while ($document_types = tep_db_fetch_array ($document_types_query) ) {
      $rows++;
      
      // Set the selected Specification Category
      if ( (!isset($_GET['dt']) || (isset($_GET['dt']) && ($_GET['dt'] == $document_types['document_types_id']) ) ) && !isset ($dtInfo) ) {
        $dtInfo = new objectInfo ($document_types);
      }

      if (isset ($dtInfo) && is_object ($dtInfo) && ($document_types['document_types_id'] == $dtInfo->document_types_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'dt=' . $document_types['document_types_id']) . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'dt=' . $document_types['document_types_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'dtpath=' . $document_types['document_types_id']) . '">' . tep_image (DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $document_types['type_description'] . '</b>'; ?></td>
                <td class="dataTableContent"><?php echo $document_types['type_name']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($document_types['type_visible'] == 'True') {
        echo tep_image (DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'action=process_doc_type_visible&flag=False&dt=' . $document_types['document_types_id']) . '">' . tep_image (DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'action=process_doc_type_visible&flag=True&dt=' . $document_types['document_types_id']) . '">' . tep_image (DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image (DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>
                </td>
                <td class="dataTableContent" align="right"><?php echo $document_types['sort_order']; ?>&nbsp;&nbsp;</td>
                <td class="dataTableContent" align="right">
<?php 
        if (isset ($cInfo) && is_object ($cInfo) && ($document_types['document_types_id'] == $cInfo->document_types_id) ) { 
          echo tep_image (DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
        } else { 
          echo '<a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'dt=' . $doc_type) . '">' . tep_image (DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
        } 
?>
                &nbsp;</td>
              </tr>
<?php
      } // while ($document_types
      $types_count = $rows;
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo TEXT_TYPES_TOTAL . '&nbsp;' . $types_count; ?></td>
                    <td align="right" class="smallText"><?php echo '<a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'action=new_type') . '">' . tep_image_button ('button_new_document_type.gif', IMAGE_NEW_DOCUMENT_TYPE) . '</a>'; ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    } else { 
  // In a Document Type -- Show Documents
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOCUMENTS_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOCUMENTS_THUMB; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DOCUMENTS_SIZE; ?>&nbsp;&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOCUMENTS_TITLE; ?></td>
                <td class="dataTableHeadingContent" align=right><?php echo TABLE_HEADING_DOCUMENTS_SORT_ORDER; ?>&nbsp;&nbsp;</td>
                <td class="dataTableHeadingContent" align=center><?php echo TABLE_HEADING_DOCUMENTS_VISIBLE; ?></td>
                <td class="dataTableHeadingContent" align=center><?php echo TABLE_HEADING_DOCUMENTS_MOVE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $documents_query_raw = "select documents_id,
                                 documents_name,
                                 documents_size,
                                 documents_title,
                                 document_types_id,
                                 documents_visible,
                                 sort_order
                          from " . TABLE_IMAGE_DOCUMENTS . "
                          where document_types_id = " . $doc_path . "
                          order by document_types_id, 
                                   documents_id
                        ";
    $documents_split = new splitPageResults ($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $documents_query_raw, $documents_query_numrows);
    $documents_query = tep_db_query ($documents_query_raw);

    //get directory for move
    $directory_array = image_document_types(); 
    while ($documents = tep_db_fetch_array($documents_query)) {
    
    if ((!isset($dInfo) || !is_object ($dInfo)) && !isset ($_GET['did']) || (isset ($_GET['did']) && ($_GET['did'] == $documents['documents_id'] ) ) ) {
      $dInfo = new objectInfo ($documents);
    }
      
    if (isset ($dInfo) && is_object($dInfo) && $documents['documents_id'] == $dInfo->documents_id) {
      echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" >' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" >' . "\n";
    }

    if (!isset ($dInfo->documents_visible) ) {
      $dInfo->documents_visible = 'True';
    }
    
    switch ($dInfo->documents_visible) {
      case 'False':
        $in_status = false;
        $out_status = true;
        break;
      case 'True':
      default:
        $in_status = true;
        $out_status = false;
    }
?>
  <?php
      echo '                  <td class="dataTableContent"
      onclick="document.location.href=\'' . tep_href_link (FILENAME_IMAGE_DOCUMENTS,
  tep_get_all_get_params (array ('action','did') ) . 'did=' .
    $documents['documents_id'] ) . '\'">' . "\n";
?>
                <?php echo $documents['documents_name']; ?></td>
                <td class="dataTableContent">
                <?php
                $image_path = tep_get_image_document_path($documents['documents_id']);
                echo tep_image('../' . $image_path, '', '60', '50'); ?>
                </td>
                <td class="dataTableContent" align=right><?php echo $documents['documents_size']; ?>&nbsp;&nbsp;</td>
                <td class="dataTableContent"><?php echo $documents['documents_title']; ?></td>
                <td class="dataTableContent" align=right><?php echo $documents['sort_order']; ?>&nbsp;&nbsp;</td>
                <td class="dataTableContent" align=center>
<?php
      if ($documents['documents_visible'] == 'True') {
        echo tep_image (DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','flag','did') ) . 'action=doc_visible_confirm&flag=False&did=' . $documents['documents_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','flag','did') ) . 'action=doc_visible_confirm&flag=True&did=' . $documents['documents_id']) . '">' . tep_image (DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }

?>
                </td>
                <td class="dataTableContent" aiign="center">

                <?php
                echo tep_draw_form('file_move', FILENAME_IMAGE_DOCUMENTS .
                    '?action=file_move'); 
                echo tep_draw_hidden_field('document_id', $documents['documents_id']);
                echo tep_draw_pull_down_menu('move_to', $directory_array, $doc_path, 'onchange="this.form.submit()"');
?>
  </form>
                </td>
                <td class="dataTableContent" align="right"><?php if (isset($dInfo) && is_object($dInfo) && (isset($dInfo->documents_id) && $documents['documents_id'] == $dInfo->documents_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('did') ) . 'did=' . $documents['documents_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    } // while
?>
              <tr>
                <td colspan="8"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $documents_split->display_count($documents_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_DOCUMENTS); ?></td>
                    <td class="smallText" align="right"><?php echo $documents_split->display_links($documents_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params (array ('page','did') ) ); ?></td>
                  </tr>
                 <tr>
                   <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td align="right"><?php echo '<a href="' . tep_href_link
                    (FILENAME_IMAGE_DOCUMENTS, 'dt=' . $doc_path) . '">' .
                    tep_image_button ('button_back.gif', IMAGE_BACK) . '</a> <a
                    href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS,
  tep_get_all_get_params (array ('action') ) . '&action=upload') . '">' .
    tep_image_button('button_image_new.gif', IMAGE_NEW) . '</a>'; //<a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . '&action=process_doc_update') . '">' . tep_image_button('button_update.gif', IMAGE_UPDATE) . '</a>'; ?>
</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  }
  
  $heading = array();
  $contents = array();
  $type_array = image_document_types();

  switch ($action) {
    // Actions on Document Types
    case 'new_type':
      $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_NEW_DOC_TYPE . '</b>');

      $contents = array ('form' => tep_draw_form ('new_type', FILENAME_IMAGE_DOCUMENTS, 'action=process_new_type', 'post'));
      $contents[] = array ('text' => TEXT_INFO_NEW_TYPE_INTRO);
      
      $directory_array = get_image_directory_list (DIR_FS_CATALOG_IMAGE_DOCUMENTS, false);
      //$contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DIRECTORY . ' ' . tep_draw_pull_down_menu ('type_name', $directory_array) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DIRECTORY . ' ' . tep_draw_input_field ('type_name') );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DESCRIPTION . ' ' . tep_draw_input_field ('type_description') );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_SORT_ORDER . ' ' . tep_draw_input_field ('sort_order', '', 'size=5') );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_DOCUMENT_VISIBLE . '<br>' . tep_draw_checkbox_field ('type_visible', 'True', 'True') . TEXT_IS_VISIBLE);
      $contents[] = array ('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . (isset($dInfo->document_types_id) ? $dInfo->document_types_id:'')) . '">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    case 'edit_type':
      $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_EDIT_DOC_TYPE . '</b>');

      $contents = array ('form' => tep_draw_form ('edit_type', FILENAME_IMAGE_DOCUMENTS, 'dt=' . $dtInfo->document_types_id . '&action=process_edit_type', 'post'));
      $contents[] = array ('text' => TEXT_INFO_EDIT_INTRO . tep_draw_hidden_field ('document_types_id', $dtInfo->document_types_id));
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DIRECTORY . ' <b>' . $dtInfo->type_name . '</b>' );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DESCRIPTION . ' ' . tep_draw_input_field ('type_description', $dtInfo->type_description) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_SORT_ORDER . ' ' . tep_draw_input_field ('sort_order', $dtInfo->sort_order) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_DOCUMENT_VISIBLE . '<br>' . tep_draw_checkbox_field ('type_visible', 'True', ( ($dtInfo->type_visible == 'True') ? true : false) ) . TEXT_IS_VISIBLE);
      $contents[] = array ('align' => 'center', 'text' => '<br>' . tep_image_submit ('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'dt=' . $dtInfo->document_types_id) . '">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    case 'delete_type':
      $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_DELETE_DOC_TYPE . '</b>');

      $contents = array ('form' => tep_draw_form ('delete_type', FILENAME_IMAGE_DOCUMENTS, 'dt=' . $dtInfo->document_types_id . '&action=process_delete_type', 'post'));
      $contents[] = array ('text' => TEXT_INFO_DELETE_DOC_TYPE_INTRO . tep_draw_hidden_field ('document_types_id', $dtInfo->document_types_id) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DIRECTORY . ' ' . $dtInfo->type_name . tep_draw_hidden_field ('type_name', $dtInfo->type_name) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_TYPE_DESCRIPTION . ' ' . $dtInfo->type_description);

      $documents_query = tep_db_query ("select documents_name,
                                               documents_title
                                        from " . TABLE_IMAGE_DOCUMENTS . "
                                        where document_types_id = '" . (int) $dtInfo->document_types_id . "'"
                                        );
      if (tep_db_num_rows ($documents_query) > 0) {
        $contents[] = array('text' => '<br>' . TEXT_INFO_DOCUMENTS_IN_TYPE);
        while ($documents = tep_db_fetch_array($documents_query) ) {
          $contents[] = array('text' => '<b>' . $documents['documents_title'] . '</b> (' . $documents['documents_name'] . ')');
        }
        $contents[] = array('text' => '<br>');
      }

      $contents[] = array ('align' => 'center', 'text' => '<br>' . tep_image_submit ('button_confirm.gif', IMAGE_CONFIRM) . ' <a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'dt=' . $dtInfo->document_types_id) . '">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
      
    // Actions on Documents
    case 'upload':
      $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_UPLOAD . '</b>');

      $documents_type_query_raw = "select type_name
                                   from " . TABLE_IMAGE_DOCUMENT_TYPES . "
                                   where document_types_id = " . $doc_path . "
                                 ";
      $documents_type_query = tep_db_query ($documents_type_query_raw);
      $documents_type = tep_db_fetch_array ($documents_type_query);

      $contents = array ('form' => tep_draw_form ('file_upload', FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action') ) . 'action=process_doc_upload', 'post', 'enctype="multipart/form-data"') );
      $contents[] = array ('text' => TEXT_UPLOAD_INTRO . tep_draw_hidden_field ('document_types_id', $doc_path) ); 
      $contents[] = array ('text' => tep_draw_hidden_field ('type_name', $documents_type['type_name']) ); 

      $contents[] = array ('text' => '<br>' . TEXT_UPLOAD_FILE_NAME . '<br>' . tep_draw_file_field ('documents_name') );
      $contents[] = array ('text' => '<br>' . TEXT_UPLOAD_FILE_TITLE . '<br>' . tep_draw_input_field ('documents_title') );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_SORT_ORDER . ' ' . tep_draw_input_field ('sort_order', '', 'size=5') );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_DOCUMENT_VISIBLE . '<br>' . tep_draw_checkbox_field ('documents_visible', 'True', ( (isset($dInfo->documents_visible) && $dInfo->documents_visible == 'True') ? true : false) . TEXT_IS_VISIBLE));
      $contents[] = array ('align' => 'center', 'text' => '<br>' . tep_image_submit ('button_upload.gif', IMAGE_UPLOAD) . ' <a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, (isset($_GET['info']) ? 'info=' . urlencode($_GET['info']) : '')) . '">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    case 'update':
      $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_UPDATE . '</b>');

      $contents = array ('form' => tep_draw_form ('file_update', FILENAME_IMAGE_DOCUMENTS, 'action=process_doc_update', 'post'));
      $contents[] = array ('text' => TEXT_UPDATE_INTRO);

      $contents[] = array ('text' => '<br>' . TEXT_UPDATE_FILE_TYPE . '<br>' . tep_draw_pull_down_menu ('type', $type_array) );
      $contents[] = array ('align' => 'center', 'text' => '<br>' . tep_image_submit ('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . $dInfo->documents_id) . '">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    case 'edit':
      $heading[] = array ('text' => '<b>' . TEXT_INFO_HEADING_EDIT_DOCUMENT . '</b>');

      $contents = array ('form' => tep_draw_form ('file', FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . $dInfo->documents_id . '&action=doc_edit_confirm', 'post'));
      $contents[] = array ('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array ('text' => tep_draw_hidden_field ('name', $dInfo->documents_name));
      $contents[] = array ('text' => tep_draw_hidden_field ('type', $dInfo->document_types_id)); 
      $contents[] = array ('text' => '<br>' . TEXT_INFO_DOCUMENT_TITLE . '<br>' . tep_draw_input_field ('title', $dInfo->documents_title) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_DOCUMENT_SORT_ORDER . '<br>' . tep_draw_input_field ('sort_order', $dInfo->sort_order) );
      $contents[] = array ('text' => '<br>' . TEXT_INFO_DOCUMENT_VISIBLE . '<br>' . tep_draw_checkbox_field ('visible', 'True', ( ($dInfo->documents_visible == 'True') ? true : false) . TEXT_IS_VISIBLE));
      $contents[] = array ('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . $dInfo->documents_id) . '">' . tep_image_button ('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    case 'associate':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_ASSOCIATE_DOCUMENT . '</b>');

      $contents = array('form' => tep_draw_form ('file', FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . $dInfo->documents_id . '&action=doc_associate_confirm', 'post'));
      $contents[] = array('text' => TEXT_INFO_ASSOCIATE_INTRO);

      $products_query_raw = "select products_name,
                                    products_id
                             from " . TABLE_PRODUCTS_DESCRIPTION . "
                             where language_id = '" . $languages_id . "'
                               and site_id = '0'
                             order by products_name
                            ";
      // print $products_query_raw . '<br>';                              
      $products_query = tep_db_query($products_query_raw);
      $products_array = array();
      $products_array[] = array ('id' => '-',
                                 'text' => '-=Select Product=-');
      while ($products = tep_db_fetch_array ($products_query)) {
        $products_array[] = array ('id' => $products['products_id'],
                                   'text' => $products['products_name']);
      }
      $contents[] = array('text' => '<br>' . sprintf(TEXT_INFO_ASSOCIATE_SELECT, $dInfo->documents_title) . '<br>' . tep_draw_pull_down_menu('product', $products_array, '-'));

      $associations_query = tep_db_query("select pd.products_name,
                                                 pd.products_id
                                          from " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                               " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . " p2d
                                          where pd.language_id = '" . $languages_id . "'
                                            and pd.products_id = p2d.products_id
                                            and pd.site_id = '0'
                                            and p2d.documents_id = '" . $dInfo->documents_id . "'"
                                        );
      if (tep_db_num_rows ($associations_query) > 0) {
        $contents[] = array('text' => '<br>' . TEXT_INFO_EXISTING_ASSOCIATIONS . TEXT_INFO_REMOVE_ASSOCIATIONS);
        while ($associations = tep_db_fetch_array ($associations_query) ) {
          $contents[] = array('text' => tep_draw_checkbox_field('pid_' . $associations['products_id'], '') . $associations['products_name'] . '<br>');
        }
      }

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . 'did=' . $dInfo->documents_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_DOCUMENT . '</b>');

      $contents = array('form' => tep_draw_form ('file', FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','did') ) . '&did=' . $dInfo->documents_id . '&action=doc_delete_confirm', 'post'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $dInfo->documents_name . '</b> - ' . $dInfo->documents_title);

      $associations_query = tep_db_query ("select pd.products_name
                                           from " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                                " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . " p2d
                                           where pd.products_id = p2d.products_id
                                             and p2d.documents_id = '" . (int) $dInfo->documents_id . "'
                                             and pd.site_id = '0'
                                             and pd.language_id = '" . $languages_id . "'
                                        ");
      if (tep_db_num_rows ($associations_query) > 0) {
        $contents[] = array('text' => '<br>' . TEXT_INFO_ASSOCIATIONS . '</b> - ' . $dInfo->documents_title);
        while ($associations = tep_db_fetch_array($associations_query)) {
          $contents[] = array('text' => '<b>' . $associations['products_name']);
        }
      }

      $contents[] = array('text' => tep_draw_hidden_field('name', $dInfo->documents_name));
      $contents[] = array('text' => tep_draw_hidden_field('type', $dInfo->document_types_id));
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_confirm.gif', IMAGE_CONFIRM) . ' <a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','flag','did') ) . 'did=' . $dInfo->documents_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;

    default:
      if ($doc_path == 0) {
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DOC_TYPES . '</b>');
        if (isset ($dtInfo->document_types_id) ) {
          $contents[] = array ('align' => 'center', 'text' => '<a href="' . tep_href_link (FILENAME_IMAGE_DOCUMENTS, 'action=edit_type&dt=' . $dtInfo->document_types_id) . '">' . tep_image_button ('button_edit.gif', IMAGE_EDIT) . '</a>  <a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','dt') ) . 'dt=' . $dtInfo->document_types_id . '&action=delete_type') . '">' . tep_image_button ('button_delete.gif', IMAGE_DELETE) . '</a>');
        }
        
      } else {
        $documents_query = tep_db_query("select type_description
                                         from " . TABLE_IMAGE_DOCUMENT_TYPES . "
                                         where document_types_id = '" . $doc_path . "'"
                                       );
        $documents = tep_db_fetch_array ($documents_query);
        $heading[] = array ('text' => '<b>' .  $documents['type_description'] . '</b>');

        if (isset ($dInfo->documents_id) ) {
          $document_url = tep_get_image_document_url($dInfo->documents_id);
          $document_image = tep_get_image_document_image($dInfo->documents_id);
          $contents[] = array('align' => 'left', 'text' => '<br><a href="' .
              tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array
                  ('action','did') ) . '&action=edit&did=' . $dInfo->documents_id) .
              '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>'
             // <a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params
            //(array ('action','did') ) . 'did=' . $dInfo->documents_id .
            //'&action=associate') . '">' . tep_image_button('button_associate.gif',
            //IMAGE_ASSOCIATE) . '</a>
              .'<a href="' . tep_href_link(FILENAME_IMAGE_DOCUMENTS, tep_get_all_get_params (array ('action','flag','did') ) . 'did=' . $dInfo->documents_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'
              .'<br><br><code>'.$document_url . '</code><br><br><code>' .
              $document_image. '</code>'  );

          $associations_query = tep_db_query("select pd.products_name,
                                                     pd.products_id
                                              from " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                                   " . TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS . " p2d
                                              where pd.language_id = '" . $languages_id . "'
                                                and pd.products_id = p2d.products_id
                                                and pd.site_id = '0'
                                                and p2d.documents_id = '" . $dInfo->documents_id . "'"
                                            );
          if (tep_db_num_rows ($associations_query) > 0) {
            $contents[] = array ('text' => '<br>' . TEXT_INFO_EXISTING_ASSOCIATIONS);
            while ($associations = tep_db_fetch_array ($associations_query) ) {
              $contents[] = array ('text' => '<b>' . $associations['products_name'] . '</b><br>');
            } // while ($associations
          } // if (tep_db_num_rows
        } // if (isset
      } // if ($doc_path ... else ...
      $contents[] = array ('text' => '<br> ');
      break;
  }

  if ( (tep_not_null ($heading)) && (tep_not_null ($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
