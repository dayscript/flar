<?php

namespace Drupal\powerbi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Declaration of class PowerbiSettingsForm.
 */
class PowerbiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'powerbi_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'powerbi.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Default settings.
    $config = $this->config('powerbi.settings');
    $form['settings'] = ['#type' => 'vertical_tabs'];
    // Account tab.
    $form['account'] = [
      '#type' => 'details',
      '#title' => $this->t('Account Information'),
      '#group' => 'settings',
      '#description' => $this->t('This will be used for authenticating with power BI service in order to embed <a href="@power-bi">PowerBI</a> data.', ['@power-bi' => 'https://powerbi.microsoft.com/en-us/landing/signin/']),
    ];
    // Application tab.
    $form['application'] = [
      '#type' => 'details',
      '#title' => $this->t('Application Information'),
      '#group' => 'settings',
      '#description' => $this->t('Application for which you want to embed the data for.'),
    ];
    $form['account']['powerbi_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Username'),
      '#description' => $this->t('Enter your PowerBI username., Ex: john.doe@yourdomain.com.'),
      '#default_value' => $config->get('powerbi_username'),
    ];
    $form['account']['powerbi_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Power BI Password'),
      '#description' => $this->t('Enter your password, saved password will not be shown here.'),
    ];
    $form['application']['powerbi_client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Application ID'),
      '#description' => $this->t('Enter your registered application ID.'),
      '#default_value' => $config->get('powerbi_client_id'),
    ];
    $form['application']['powerbi_group_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Group ID'),
      '#description' => $this->t('Enter your group ID.'),
      '#default_value' => $config->get('powerbi_group_id'),
    ];
    $form['application']['powerbi_dataset_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Dataset ID'),
      '#description' => $this->t('Enter your Dataset ID.'),
      '#default_value' => $config->get('powerbi_dataset_id'),
    ];
    $form['application']['powerbi_report_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Power BI Report ID'),
      '#description' => $this->t('Enter your Report ID.'),
      '#default_value' => $config->get('powerbi_report_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Retrieve the configuration.
    $config = $this->configFactory->getEditable('powerbi.settings');
    $config->set('powerbi_username', $form_state->getValue('powerbi_username'));
    $config->set('powerbi_group_id', $form_state->getValue('powerbi_group_id'));
    $config->set('powerbi_client_id', $form_state->getValue('powerbi_client_id'));
    $config->set('powerbi_dataset_id', $form_state->getValue('powerbi_dataset_id'));
    $config->set('powerbi_report_id', $form_state->getValue('powerbi_report_id'));
    $config->set('auth_uri', $form_state->getValue('auth_uri'));
    if (!empty($form_state->getValue('powerbi_password'))) {
      $config->set('powerbi_password', $form_state->getValue('powerbi_password'));
    }
    $config->save();

    return parent::submitForm($form, $form_state);
  }

}
