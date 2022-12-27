<?php

namespace Drupal\trim_video\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class ConvertVideoForm extends FormBase
{

    const RESULT_TRIM_VIDEO = 'convert_video_form:values';

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'convert_video_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form = [
            'file' => [
                'video_dir' => [
                    '#type'                 => 'managed_file',
                    '#title'                => $this->t('Upload an video file'),
                    '#required' => TRUE,
                    '#upload_location'      => 'public://videos/' . time(),
                    '#multiple'             => FALSE,
                    '#description'          => $this->t('Allowed extensions: mp4 mov webm'),
                    '#upload_validators'    => [
                        'file_validate_extensions'    => array('mp4 mov webm'),
                        'file_validate_size'          => array(25600000)
                    ],
                ]
            ],

            'duration' => [
                '#type' => 'number',
                '#title' => $this->t('Duration'),
                '#description' => $this->t('The duration of video you want to cut (seconds)'),
                '#required' => TRUE,
                '#default_value' => 5,
            ],

            'actions' => [
                '#type' => 'actions',
                'submit' => [
                    '#type' => 'submit',
                    '#value' => $this->t('Convert'),
                    '#button_type' => 'primary'
                ]
            ]
        ];



        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $value = $form_state->getValues();
        if (!$value['video_dir']) {
            $form_state->setErrorByName('upload', $this->t('Please upload file'));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $value = $form_state->getValues();
        $file_data = $value['video_dir'];
        $duration = $value['duration'];
        $file = \Drupal\file\Entity\File::load($file_data[0]);

        try {
            $slash_uri = explode("//", $file->getFileUri());
            $public_id = explode(".", $slash_uri[1])[0];

            $cloudinary_service = \Drupal::service('trim_video.cloudinary_service');
            $result = $cloudinary_service->uploadFile($file->getFileUri(), $public_id);

            $trim_video = $cloudinary_service->getTrimVideo($result['public_id'], $result['format'], $duration);
            $thumb_video = $cloudinary_service->getThumbVideo($result['public_id']);

            $form_state->setRedirect('trim_video.content', [
                'trim_video' => $trim_video,
                'thumb_video' => $thumb_video
            ]);
        } catch (\Throwable $th) {
            $this->messenger()->addError($this->t('Please check your video and try again.'));
        }
    }
}
