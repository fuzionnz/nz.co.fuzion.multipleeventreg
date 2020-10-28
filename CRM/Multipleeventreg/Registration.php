<?php

class CRM_Multipleeventreg_Registration {

  /**
   * Get event custom field ID
   */
  public static function eventCustomField() {
    return civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'return' => ["id"],
      'name' => "child_event_ids_cf",
    ])['id'] ?? NULL;
  }

  /**
   * Refine participant params.
   *
   * @param array $participantParams
   *
   * @return array
   */
  public static function refineParamsForCreate($participantParams) {
    $createParams = [
      'event_id',
      'contact_id',
      'discount_amount',
      'cart_id',
      'must_wait',
      'transferred_to_contact_id',
      'id',
      'status_id',
      'role_id',
      'source',
      'register_date',
      'fee_level',
      'is_pay_later',
      'is_test',
      'registered_by_id',
      'fee_amount',
      'discount_id',
      'fee_currency',
      'campaign_id',
    ];
    foreach ($createParams as $value) {
      if (isset($participantParams["participant_{$value}"])) {
        $participantParams[$value] = $participantParams["participant_{$value}"];
        unset($participantParams["participant_{$value}"]);
      }
    }

    //Remove unnecessary params.
    $unnecessaryParams = [
      'event_title', 'event_start_date', 'event_end_date',
      'default_role_id', 'contact_type', 'contact_sub_type',
      'participant_status', 'participant_role', 'participant_discount_name', 'participant_note',
      'id', 'display_name', 'sort_name', 'event_type', 'fee_level', 'fee_amount', 'fee_currency'
    ];
    foreach ($unnecessaryParams as $param) {
      unset($participantParams[$param]);
    }
    return $participantParams;
  }

  /**
   * Copy registration from main event to all child events.
   *
   * @param integer $participantID
   * @param string $action
   */
  public static function createRelatedEventRegistration($participantID, $action) {
    $fieldID = CRM_Multipleeventreg_Registration::eventCustomField();
    if (empty($fieldID)) {
      return;
    }
    $customFieldID = 'custom_' . $fieldID;

    $participantParams = civicrm_api3('Participant', 'getsingle', [
      'id' => $participantID,
    ]);
    $event = civicrm_api3('Event', 'getsingle', [
      'return' => [$customFieldID],
      'id' => $participantParams['event_id'],
    ]);
    if (!empty($event[$customFieldID])) {
      $eventIds =  array_map('trim', (array) explode(',', $event[$customFieldID]));
      $participantParams = CRM_Multipleeventreg_Registration::refineParamsForCreate($participantParams);
      foreach ($eventIds as $eventID) {
        $participantParams['event_id'] = $eventID;
        if ($action == 'edit') {
          $relatedParticipants = civicrm_api3('Participant', 'get', [
            'contact_id' => $participantParams['contact_id'],
            'event_id' => $eventID,
          ]);
          if (empty($relatedParticipants['values'])) {
            continue;
          }
          foreach ($relatedParticipants['values'] as $participant) {
            $updateParams = $participantParams;
            $updateParams['id'] = $participant['id'];
            civicrm_api3('Participant', 'create', $updateParams);
          }
        }
        else {
          civicrm_api3('Participant', 'create', $participantParams);
        }
      }
    }
  }

  /**
   * Delete participants from related events.
   *
   * @param integer $participantID
   */
  public static function deleteRelatedEventRegistration($participantID) {
    $fieldID = CRM_Multipleeventreg_Registration::eventCustomField();
    if (empty($fieldID)) {
      return;
    }
    $customFieldID = 'custom_' . $fieldID;

    $participantParams = civicrm_api3('Participant', 'getsingle', [
      'id' => $participantID,
    ]);
    $event = civicrm_api3('Event', 'getsingle', [
      'return' => [$customFieldID],
      'id' => $participantParams['event_id'],
    ]);
    if (!empty($event[$customFieldID])) {
      $eventIds =  array_map('trim', (array) explode(',', $event[$customFieldID]));

      //deleted related event registration.
      foreach ($eventIds as $eventID) {
        $relatedParticipants = civicrm_api3('Participant', 'get', [
          'contact_id' => $participantParams['contact_id'],
          'event_id' => $eventID,
        ]);
        if (empty($relatedParticipants['values'])) {
          continue;
        }
        foreach ($relatedParticipants['values'] as $participant) {
          civicrm_api3('Participant', 'delete', [
            'id' => $participant['id'],
          ]);
        }
      }
    }
  }

}