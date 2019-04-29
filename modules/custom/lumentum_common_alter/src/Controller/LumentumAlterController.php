<?php

namespace Drupal\lumentum_common_alter\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\views\Views;
use Drupal\taxonomy\Entity\Term;

/**
 * Class LumentumAlterController.
 */
class LumentumAlterController extends ControllerBase {

  /**
 * On product page - Related Information block
 * @param type $nid
 */
  function get_related_info_product($nid){
    $connection = \Drupal::database();
    if(db_table_exists('node__field_technical_library') != true || db_table_exists('node__field_file') != true || db_table_exists('node__field_technical_library_category') != true || db_table_exists('taxonomy_term_field_data') != true || db_table_exists('node_field_data') != true){
      return false;
    }

    $query = $connection->select('node__field_technical_library', 'nftl');
    $query->condition('nftl.entity_id', $nid);
    $query->fields('nff', ['field_file_target_id']);
    $query->fields('ttfd', ['name']);
    $query->fields('nfd', ['title']);
    $query->join('node__field_file', 'nff', 'nff.entity_id = nftl.field_technical_library_target_id');
    $query->join('node__field_technical_library_category', 'nftlc', 'nftlc.entity_id = nftl.field_technical_library_target_id');
    $query->join('taxonomy_term_field_data', 'ttfd', 'ttfd.tid = nftlc.field_technical_library_category_target_id');
    $query->join('node_field_data', 'nfd', 'nfd.nid = nff.entity_id');
    $query->orderBy('ttfd.name', 'ASC');
    $query->orderBy('nfd.title', 'ASC');
    $result = $query->execute();
    $results = $result->fetchAll();

    foreach ($results as $technical){
      $name_technical = $technical->name.' - '.$technical->title;
      $file_url_technical = file_create_url(File::load($technical->field_file_target_id)->getFileUri());
      $technical_arr[] = [$name_technical, $file_url_technical];
    }
    return $technical_arr;
  }

}
