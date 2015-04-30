<?php

/**
 * @file
 * Contains \Drupal\optimizedb\Form\OptimizedbListTablesForm.
 */

namespace Drupal\optimizedb\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Displays banned IP addresses.
 */
class OptimizedbListTablesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'optimizedb_list_tables_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $headers = array(
      'name' => array(
        'data' => $this->t('Table name'),
      ),
      'size' => array(
        'data' => $this->t('Table size'),
        'field' => 'size',
        'sort' => 'desc',
      ),
    );

    $tables = _optimizedb_tables_list();

    $rows = array();

    foreach ($tables as $table) {
      // Parameter "size_byte" us only needed to sort, now his unit to remove.
      unset($table['size_byte']);

      $rows[$table['name']] = $table;
    }

    if (db_driver() == 'mysql') {
      $form['operations'] = array(
        '#type' => 'fieldset',
        '#title' => t('Operations with tables:'),
      );

      $form['operations']['check_tables'] = array(
        '#type' => 'submit',
        '#value' => t('Check tables'),
        '#submit' => array('optimizedb_list_tables_check_tables_submit'),
      );

      $form['operations']['repair_tables'] = array(
        '#type' => 'submit',
        '#value' => t('Repair tables'),
        '#submit' => array('optimizedb_list_tables_repair_tables_submit'),
      );

      $form['operations']['optimize_tables'] = array(
        '#type' => 'submit',
        '#value' => t('Optimize tables'),
        '#submit' => array('optimizedb_list_tables_optimize_tables_submit'),
      );
    }

    $form['tables'] = array(
      '#type' => 'tableselect',
      '#header' => $headers,
      '#options' => $rows,
      '#empty' => $this->t('No content available.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
